<?php

namespace Database\Seeders;

use App\Models\Agenda;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AgendaSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        $agendas = [
            [
                'jenis_agenda'       => 'internal',
                'perihal_kegiatan'   => 'Rapat Koordinasi Penyusunan APBD Perubahan Tahun 2026',
                'waktu_mulai'        => now()->addDays(3)->setTime(9, 0),
                'waktu_selesai'      => now()->addDays(3)->setTime(12, 0),
                'tempat'             => 'Ruang Rapat Utama Sekretariat Daerah',
                'asal_surat'         => 'Sekretariat Daerah Kab. Sambas',
                'tanggal_surat'      => now()->subDays(2)->toDateString(),
                'pakaian'            => 'Batik / Pakaian Dinas',
                'disposisi'          => 'Kepala Dinas, Sekretaris',
                'petugas_ditugaskan' => 'Kepala Dinas',
                'status'             => 'terjadwal',
                'keterangan'         => 'Harap membawa dokumen usulan anggaran masing-masing bidang.',
                'diinput_oleh'       => $admin?->name ?? 'Admin Dinas',
                'created_by'         => $admin?->id,
            ],
            [
                'jenis_agenda'       => 'eksternal',
                'perihal_kegiatan'   => 'Sosialisasi Program Digitalisasi Layanan Publik Kabupaten Sambas',
                'waktu_mulai'        => now()->addDays(7)->setTime(8, 0),
                'waktu_selesai'      => now()->addDays(7)->setTime(16, 0),
                'tempat'             => 'Aula Kantor Bupati Sambas',
                'asal_surat'         => 'Dinas Komunikasi dan Informatika Kab. Sambas',
                'tanggal_surat'      => now()->subDays(5)->toDateString(),
                'pakaian'            => 'Pakaian Dinas Harian (PDH)',
                'disposisi'          => 'Kabid Umum dan Kepegawaian',
                'petugas_ditugaskan' => 'Kabid Umum dan Kepegawaian',
                'status'             => 'terjadwal',
                'keterangan'         => null,
                'diinput_oleh'       => $admin?->name ?? 'Admin Dinas',
                'created_by'         => $admin?->id,
            ],
            [
                'jenis_agenda'       => 'internal',
                'perihal_kegiatan'   => 'Evaluasi Kinerja Triwulan I Tahun 2026',
                'waktu_mulai'        => now()->subDays(10)->setTime(10, 0),
                'waktu_selesai'      => now()->subDays(10)->setTime(13, 0),
                'tempat'             => 'Ruang Rapat Lantai 2',
                'asal_surat'         => 'Bagian Perencanaan dan Pelaporan',
                'tanggal_surat'      => now()->subDays(15)->toDateString(),
                'pakaian'            => 'Batik',
                'disposisi'          => 'Seluruh Kepala Bidang',
                'petugas_ditugaskan' => 'Sekretaris Dinas',
                'status'             => 'selesai',
                'keterangan'         => 'Evaluasi berjalan lancar, notulen terlampir.',
                'diinput_oleh'       => $admin?->name ?? 'Admin Dinas',
                'created_by'         => $admin?->id,
            ],
            [
                'jenis_agenda'       => 'eksternal',
                'perihal_kegiatan'   => 'Bimbingan Teknis Pengelolaan Arsip Elektronik Pemerintah Daerah',
                'waktu_mulai'        => now()->subDays(3)->setTime(8, 30),
                'waktu_selesai'      => now()->subDays(3)->setTime(17, 0),
                'tempat'             => 'Hotel Mahkota Sambas',
                'asal_surat'         => 'Arsip Nasional Republik Indonesia (ANRI)',
                'tanggal_surat'      => now()->subDays(14)->toDateString(),
                'pakaian'            => 'Pakaian Dinas Harian (PDH)',
                'disposisi'          => 'Staf Arsip dan Dokumentasi',
                'petugas_ditugaskan' => 'Kasubbag Umum',
                'status'             => 'selesai',
                'keterangan'         => 'Peserta mendapatkan sertifikat bimtek.',
                'diinput_oleh'       => $admin?->name ?? 'Admin Dinas',
                'created_by'         => $admin?->id,
            ],
            [
                'jenis_agenda'       => 'eksternal',
                'perihal_kegiatan'   => 'Kunjungan Kerja Komisi I DPRD Kab. Sambas ke Dinas',
                'waktu_mulai'        => now()->subDays(1)->setTime(10, 0),
                'waktu_selesai'      => now()->subDays(1)->setTime(12, 0),
                'tempat'             => 'Ruang Rapat Dinas',
                'asal_surat'         => 'Sekretariat DPRD Kab. Sambas',
                'tanggal_surat'      => now()->subDays(5)->toDateString(),
                'pakaian'            => 'Pakaian Dinas Upacara (PDU)',
                'disposisi'          => 'Kepala Dinas, Sekretaris, seluruh Kabid',
                'petugas_ditugaskan' => 'Kepala Dinas',
                'status'             => 'dibatalkan',
                'keterangan'         => 'Dibatalkan karena agenda DPRD berubah mendadak.',
                'diinput_oleh'       => $admin?->name ?? 'Admin Dinas',
                'created_by'         => $admin?->id,
            ],
            [
                'jenis_agenda'       => 'internal',
                'perihal_kegiatan'   => 'Rapat Persiapan Pelaksanaan Audit BPK RI Semester I',
                'waktu_mulai'        => now()->addDays(14)->setTime(9, 0),
                'waktu_selesai'      => now()->addDays(14)->setTime(11, 30),
                'tempat'             => 'Ruang Rapat Keuangan',
                'asal_surat'         => 'Bidang Keuangan dan Aset',
                'tanggal_surat'      => now()->subDay()->toDateString(),
                'pakaian'            => 'Batik / Pakaian Dinas',
                'disposisi'          => 'Kasubbag Keuangan, Bendahara, Staf Aset',
                'petugas_ditugaskan' => 'Kabid Keuangan',
                'status'             => 'terjadwal',
                'keterangan'         => null,
                'diinput_oleh'       => $admin?->name ?? 'Admin Dinas',
                'created_by'         => $admin?->id,
            ],
        ];

        foreach ($agendas as $data) {
            $waktuMulai = Carbon::parse($data['waktu_mulai']);
            $dateStr    = $waktuMulai->translatedFormat('d F Y');
            $baseSlug   = Str::slug($data['perihal_kegiatan'] . '-' . $dateStr);
            $slug       = $baseSlug;
            $count      = 1;
            while (Agenda::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $count++;
            }
            $data['slug'] = $slug;

            Agenda::create($data);
        }
    }
}
