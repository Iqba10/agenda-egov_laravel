<?php

use App\Console\Commands\CreateUser;
use App\Models\Agenda;
use App\Models\AgendaDocument;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

Schedule::command('agenda:sync-statuses')
    ->everyMinute()
    ->timezone(config('app.timezone'));

Schedule::command('agenda:send-reminders')
    ->everyMinute()
    ->timezone(config('app.timezone'));

Artisan::command('user:create {name} {email} {username} {password} {role=user}', function () {
    $name = $this->argument('name');
    $email = $this->argument('email');
    $username = $this->argument('username');
    $password = $this->argument('password');
    $role = $this->argument('role');

    if (!in_array($role, ['admin', 'user'])) {
        $this->error('Role must be either "admin" or "user"');
        return 1;
    }

    $user = \App\Models\User::create([
        'name' => $name,
        'email' => $email,
        'username' => $username,
        'password' => \Illuminate\Support\Facades\Hash::make($password),
        'role' => $role,
    ]);

    $this->info("User created successfully!");
    $this->info("Name: {$user->name}");
    $this->info("Email: {$user->email}");
    $this->info("Username: {$user->username}");
    $this->info("Role: {$user->role}");

    return 0;
})->purpose('Create a new user manually');

Artisan::command('legacy:import {--path=} {--uploads=} {--fresh}', function () {
    $resolvePath = static function (array $candidates, ?string $override = null): ?string {
        $toAbsolutePath = static function (string $candidate): string {
            $first = $candidate[0] ?? '';
            $second = $candidate[1] ?? '';

            if (($second === ':' && ctype_alpha($first)) || $first === '\\' || $first === '/') {
                return $candidate;
            }

            return base_path($candidate);
        };

        $paths = $override ? [$override] : $candidates;

        foreach ($paths as $candidate) {
            $path = $toAbsolutePath($candidate);

            if (is_file($path) || is_dir($path)) {
                return $path;
            }
        }

        return null;
    };

    $tokenize = static function (string $value): array {
        $value = Str::lower($value);
        $value = preg_replace('/[^\pL\pN]+/u', ' ', $value) ?? $value;
        $parts = preg_split('/\s+/', trim($value), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $stopwords = [
            'agenda', 'and', 'atau', 'bagi', 'bentuk', 'dalam', 'dengan', 'dan', 'di', 'dokumen', 'dinas',
            'diskominfo', 'download', 'e', 'egov', 'fasilitasi', 'kab', 'kabupaten', 'ke', 'kominfo', 'kegiatan',
            'konfirmasi', 'meeting', 'meeting', 'menjadi', 'pelaksanaan', 'pemerintah', 'pemda', 'perihal', 'rapat',
            'revisi', 'sambas', 'sebagai', 'surat', 'suratundangan', 'ta', 'tahun', 'untuk', 'undangan', 'via',
            'zoom', 'meeting', 'nino', 'nugroho', 'othman', 'alydrus', 'dinas', 'komunikasi', 'informatika',
        ];

        return array_values(array_unique(array_filter($parts, static function (string $part) use ($stopwords): bool {
            if (strlen($part) < 2) {
                return false;
            }

            if (in_array($part, $stopwords, true)) {
                return false;
            }

            return true;
        })));
    };

    $normalizeAgenda = static function (Agenda $agenda) use ($tokenize): string {
        $parts = array_filter([
            $agenda->perihal_kegiatan,
            $agenda->asal_surat,
            $agenda->tempat,
            $agenda->pakaian,
            $agenda->disposisi,
            $agenda->petugas_ditugaskan,
            $agenda->keterangan,
            $agenda->tanggal_surat?->format('Y-m-d'),
            $agenda->waktu_mulai?->format('Y-m-d'),
        ]);

        return implode(' ', $tokenize(implode(' ', $parts)));
    };

    $scoreAgenda = static function (string $fileName, string $agendaText) use ($tokenize): int {
        $fileTokens = $tokenize(pathinfo($fileName, PATHINFO_FILENAME));
        $agendaTokens = $tokenize($agendaText);

        $shared = count(array_intersect($fileTokens, $agendaTokens));
        $tokenScore = $shared > 0 ? (int) round(($shared / max(count($fileTokens), 1)) * 60) : 0;

        $similarity = 0.0;
        similar_text(implode(' ', $fileTokens), implode(' ', $agendaTokens), $similarity);

        $yearBonus = 0;
        if (preg_match('/\b(20\d{2})\b/', $fileName, $yearMatch) && str_contains($agendaText, $yearMatch[1])) {
            $yearBonus = 10;
        }

        return (int) round(max($tokenScore, $similarity) + $yearBonus);
    };

    $sqlPath = $resolvePath([
        'z_refs/agenda-egov_revisi_Fixedbase/agenda_egov.sql',
        'agenda_egov_laravel.sql',
        'native_old/agenda_egov.sql',
    ], $this->option('path'));

    $uploadsPath = $resolvePath([
        'z_refs/agenda-egov_revisi_Fixedbase/uploads',
        'native_old/uploads',
    ], $this->option('uploads'));

    if (! $sqlPath) {
        $this->error('SQL dump tidak ditemukan.');

        return self::FAILURE;
    }

    $sql = (string) file_get_contents($sqlPath);
    $pattern = '/INSERT INTO `agenda` \(([^)]+)\) VALUES\s*(.*?);/is';

    if (! preg_match($pattern, $sql, $match)) {
        $this->warn('Tidak ada INSERT agenda yang ditemukan pada dump SQL.');

        return self::SUCCESS;
    }

    $columns = array_map(
        static fn (string $column): string => trim($column, " `\t\n\r\0\x0B"),
        explode(',', $match[1])
    );

    preg_match_all('/\((.*?)\)(?:,|$)/s', trim($match[2]), $rows);

    if ($this->option('fresh')) {
        AgendaDocument::query()->delete();
        Agenda::query()->delete();
        Storage::disk('public')->deleteDirectory('agendas/documents');
        Storage::disk('public')->makeDirectory('agendas/documents');
    }

    $imported = 0;
    $agendaIndex = [];

    foreach ($rows[1] as $row) {
        $values = str_getcsv($row, ',', "'", '\\');
        $record = array_combine($columns, $values);

        if (! $record) {
            continue;
        }

        $agenda = Agenda::unguarded(static function () use ($record): Agenda {
            return Agenda::updateOrCreate(
                ['id' => (int) $record['id']],
                [
                    'jenis_agenda' => $record['jenis_agenda'] ?: 'eksternal',
                    'perihal_kegiatan' => $record['perihal_kegiatan'] ?: '-',
                    'waktu_mulai' => $record['waktu_mulai'],
                    'waktu_selesai' => $record['waktu_selesai'],
                    'tempat' => $record['tempat'] ?: '-',
                    'asal_surat' => $record['asal_surat'] ?: '-',
                    'tanggal_surat' => $record['tanggal_surat'] ?? null,
                    'pakaian' => $record['pakaian'] ?? null,
                    'disposisi' => $record['disposisi'] ?? null,
                    'petugas_ditugaskan' => $record['petugas_ditugaskan'] ?? null,
                    'status' => $record['status'] ?: 'terjadwal',
                    'keterangan' => $record['keterangan'] ?? null,
                    'diinput_oleh' => $record['diinput_oleh'] ?? 'Admin Dinas',
                    'created_at' => $record['created_at'] ?? now(),
                    'updated_at' => $record['updated_at'] ?? now(),
                ]
            );
        });

        $agendaIndex[$agenda->id] = [
            'model' => $agenda,
            'text' => $normalizeAgenda($agenda),
        ];
        $imported++;
    }

    $copied = 0;
    $attached = 0;
    $seenDocumentHashes = [];

    if ($uploadsPath && is_dir($uploadsPath)) {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($uploadsPath, FilesystemIterator::SKIP_DOTS));

        foreach ($iterator as $file) {
            if (! $file->isFile()) {
                continue;
            }

            $content = (string) file_get_contents($file->getPathname());
            $contentHash = hash('sha256', $content);

            if (isset($seenDocumentHashes[$contentHash]) || AgendaDocument::query()->where('content_hash', $contentHash)->exists()) {
                continue;
            }

            $seenDocumentHashes[$contentHash] = true;

            $originalName = $file->getFilename();
            $storedBase = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) ?: 'legacy-file';
            $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION) ?: 'bin');
            $hash = substr(sha1($file->getPathname() . '|' . $originalName . '|' . $contentHash), 0, 12);
            $storedName = $storedBase . '-' . $hash . '.' . $extension;
            $relativePath = 'agendas/documents/' . $storedName;

            while (Storage::disk('public')->exists($relativePath)) {
                $hash = substr(sha1($hash . '|' . microtime(true)), 0, 12);
                $storedName = $storedBase . '-' . $hash . '.' . $extension;
                $relativePath = 'agendas/documents/' . $storedName;
            }

            Storage::disk('public')->put($relativePath, $content);
            $copied++;

            $matchedAgenda = null;
            $bestScore = -1;
            $fileName = pathinfo($originalName, PATHINFO_FILENAME);

            foreach ($agendaIndex as $agendaData) {
                $score = $scoreAgenda($fileName, $agendaData['text']);

                if ($score > $bestScore) {
                    $bestScore = $score;
                    $matchedAgenda = $agendaData['model'];
                }
            }

            if ($matchedAgenda) {
                AgendaDocument::create([
                    'agenda_id' => $matchedAgenda->id,
                    'nama_file' => $storedName,
                    'original_name' => $originalName,
                    'content_hash' => $contentHash,
                ]);
                $attached++;
            }
        }
    }

    $this->info("Import selesai: {$imported} agenda, {$copied} file dokumen disalin, {$attached} dokumen ditautkan.");

    return self::SUCCESS;
})->purpose('Import agenda dan dokumen legacy dari dump SQL dan folder uploads ke struktur Laravel saat ini.');
