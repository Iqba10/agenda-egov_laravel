<?php

namespace App\Http\Controllers;

use App\Http\Requests\BulkRegistrationRequest;
use App\Models\Agenda;
use App\Models\NotifikasiPendaftar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

class BulkRegistrationController extends Controller
{
    /**
     * Halaman registrasi massal
     */
    public function index()
    {
        $agendas = Agenda::query()
            ->where('status', '!=', 'dibatalkan')
            ->where('waktu_mulai', '>', now())
            ->orderBy('waktu_mulai', 'asc')
            ->get();

        return view('agenda.bulk-register', compact('agendas'));
    }

    /**
     * Proses registrasi massal (manual atau file upload)
     */
    public function store(BulkRegistrationRequest $request)
    {
        $data = $request->validated();
        $agendaIds = $data['agenda_ids'];
        $inputMethod = $data['input_method'];
        $reminderMinutes = $data['reminder_minutes'] ?? 60;

        // 1. Parse input — dapatkan list [{nama, phone_number}]
        $entries = $inputMethod === 'manual'
            ? $this->parseManualInput($data['manual_numbers'] ?? '')
            : $this->parseUploadedFile($request->file('bulk_file'));

        if (empty($entries)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada nomor valid yang ditemukan dari input.',
            ], 422);
        }

        // 2. Normalisasi & validasi
        $validated = [];
        $errors = [];

        foreach ($entries as $idx => $entry) {
            $phone = $this->normalizePhone($entry['phone_number'] ?? '');
            $nama = trim($entry['nama'] ?? '');

            if (strlen($phone) < 10) {
                $errors[] = [
                    'line'   => $idx + 1,
                    'phone'  => $entry['phone_number'] ?? '',
                    'reason' => 'Nomor tidak valid (kurang dari 10 digit setelah normalisasi)',
                ];
                continue;
            }

            $validated[] = [
                'nama'        => $nama ?: null,
                'phone_number' => $phone,
            ];
        }

        // 3. Bulk insert — skip duplikat
        $inserted = 0;
        $duplicates = 0;
        $insertedDetails = [];
        $duplicateDetails = [];

        foreach ($validated as $entry) {
            foreach ($agendaIds as $agendaId) {
                $exists = NotifikasiPendaftar::where('agenda_id', $agendaId)
                    ->where('phone_number', $entry['phone_number'])
                    ->exists();

                if ($exists) {
                    $duplicates++;
                    $duplicateDetails[] = [
                        'phone'  => $entry['phone_number'],
                        'agenda_id' => $agendaId,
                        'nama'   => $entry['nama'],
                    ];
                    continue;
                }

                NotifikasiPendaftar::create([
                    'agenda_id'         => $agendaId,
                    'nama'              => $entry['nama'],
                    'phone_number'      => $entry['phone_number'],
                    'channel_preference' => 'whatsapp',
                    'reminder_minutes'  => $reminderMinutes,
                    'is_immediate'      => false,
                ]);

                $inserted++;
                $insertedDetails[] = [
                    'phone'     => $entry['phone_number'],
                    'agenda_id' => $agendaId,
                    'nama'      => $entry['nama'],
                ];
            }
        }

        Log::info('BulkRegistration completed', [
            'input_method' => $inputMethod,
            'agenda_count' => count($agendaIds),
            'total_entries' => count($entries),
            'validated'    => count($validated),
            'inserted'     => $inserted,
            'duplicates'   => $duplicates,
            'errors'       => count($errors),
        ]);

        return response()->json([
            'success'    => true,
            'message'    => $this->buildResultMessage($inserted, $duplicates, count($errors)),
            'summary'    => [
                'total_input'  => count($entries),
                'validated'    => count($validated),
                'inserted'     => $inserted,
                'duplicates'   => $duplicates,
                'errors'       => count($errors),
                'agenda_count' => count($agendaIds),
            ],
            'error_details'          => $errors,
            'duplicate_details'      => $duplicateDetails,
            'inserted_details'       => $insertedDetails,
        ]);
    }

    /**
     * Parse input manual (newline atau koma sebagai pemisah)
     */
    private function parseManualInput(string $input): array
    {
        $lines = preg_split('/[\n,]+/', $input);
        $entries = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            // Cek apakah ada pemisah tab atau pipe (untuk format "nama\tnomor" atau "nama|nomor")
            if (preg_match('/[\t|;]/', $line)) {
                $parts = preg_split('/[\t|;]/', $line);
                $entries[] = [
                    'nama'        => trim($parts[0]),
                    'phone_number' => trim($parts[1] ?? ''),
                ];
            } else {
                $entries[] = [
                    'nama'        => null,
                    'phone_number' => $line,
                ];
            }
        }

        return $entries;
    }

    /**
     * Parse file CSV yang di-upload
     */
    private function parseUploadedFile($file): array
    {
        $entries = [];

        try {
            $csv = Reader::createFromPath($file->getPathname(), 'r');
            $csv->setDelimiter($this->detectDelimiter($file->getPathname()));
            $csv->setHeaderOffset(0);

            $headers = [];
            $headerFound = false;

            foreach ($csv->getRecords() as $record) {
                if (!$headerFound) {
                    $headers = array_map('strtolower', array_map('trim', $record->getKeys()));
                    $headerFound = true;

                    // Cari index kolom nama dan nomor
                    $namaIdx = $this->findColumnIndex($headers, ['nama', 'name', 'nama_lengkap', 'nama lengkap']);
                    $phoneIdx = $this->findColumnIndex($headers, [
                        'nomor_whatsapp', 'nomor wa', 'phone', 'whatsapp', 'hp', 'telp',
                        'telepon', 'no_hp', 'no. hp', 'no wa', 'no. wa', 'nomor',
                        'phone_number', 'nohp', 'nowa',
                    ]);

                    if ($phoneIdx === null) {
                        // Fallback: jika hanya ada 2 kolom, asumsikan [nama, nomor]
                        if (count($headers) >= 2) {
                            $namaIdx = $namaIdx ?? 0;
                            $phoneIdx = 1;
                        } else {
                            return [];
                        }
                    }

                    continue;
                }

                $nama = $namaIdx !== null ? trim($record[$headers[$namaIdx]] ?? '') : null;
                $phone = $phoneIdx !== null ? trim($record[$headers[$phoneIdx]] ?? '') : '';

                if ($phone !== '') {
                    $entries[] = [
                        'nama'        => $nama ?: null,
                        'phone_number' => $phone,
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::error('CSV parse error', ['message' => $e->getMessage()]);
            return [];
        }

        return $entries;
    }

    /**
     * Deteksi delimiter CSV secara otomatis
     */
    private function detectDelimiter(string $path): string
    {
        $handle = fopen($path, 'r');
        if (!$handle) {
            return ',';
        }

        $firstLine = fgets($handle);
        fclose($handle);

        if (!$firstLine) {
            return ',';
        }

        $delimiters = [',', ';', "\t", '|'];
        $bestDelimiter = ',';
        $maxCount = 0;

        foreach ($delimiters as $delimiter) {
            $count = substr_count($firstLine, $delimiter);
            if ($count > $maxCount) {
                $maxCount = $count;
                $bestDelimiter = $delimiter;
            }
        }

        return $bestDelimiter;
    }

    /**
     * Cari index kolom berdasarkan kemungkinan nama header
     */
    private function findColumnIndex(array $headers, array $candidates): ?int
    {
        foreach ($candidates as $candidate) {
            $idx = array_search($candidate, $headers);
            if ($idx !== false) {
                return (int) $idx;
            }
        }

        return null;
    }

    /**
     * Normalisasi nomor telepon Indonesia
     */
    private function normalizePhone(string $phone): string
    {
        $cleaned = preg_replace('/[^0-9]/', '', $phone);

        // Hilangkan leading zeros
        $cleaned = ltrim($cleaned, '0');

        // Jika belum diawali 62, tambahkan
        if (!str_starts_with($cleaned, '62')) {
            $cleaned = '62' . $cleaned;
        }

        return $cleaned;
    }

    /**
     * Bangun pesan hasil registrasi
     */
    private function buildResultMessage(int $inserted, int $duplicates, int $errors): string
    {
        $parts = [];

        if ($inserted > 0) {
            $parts[] = "{$inserted} berhasil didaftarkan";
        }
        if ($duplicates > 0) {
            $parts[] = "{$duplicates} duplikat (sudah terdaftar)";
        }
        if ($errors > 0) {
            $parts[] = "{$errors} gagal validasi";
        }

        return implode(', ', $parts) . '.';
    }
}
