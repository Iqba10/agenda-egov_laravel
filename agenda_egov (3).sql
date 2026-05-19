-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 19 Bulan Mei 2026 pada 19.13
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `agenda_egov`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `agenda`
--

CREATE TABLE `agenda` (
  `id` int(11) NOT NULL,
  `jenis_agenda` enum('eksternal','internal') NOT NULL,
  `perihal_kegiatan` text NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `waktu_mulai` datetime NOT NULL,
  `waktu_selesai` datetime NOT NULL,
  `tempat` varchar(255) NOT NULL,
  `asal_surat` varchar(255) NOT NULL,
  `tanggal_surat` date DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `pakaian` varchar(100) NOT NULL,
  `disposisi` text DEFAULT NULL,
  `petugas_ditugaskan` varchar(255) NOT NULL,
  `status` enum('terjadwal','selesai','dibatalkan') DEFAULT 'terjadwal',
  `diinput_oleh` varchar(100) DEFAULT 'Admin Dinas',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `nomor_surat` varchar(100) DEFAULT NULL,
  `email_peserta` longtext DEFAULT NULL,
  `whatsapp_peserta` longtext DEFAULT NULL,
  `channel_preference_admin` enum('email','whatsapp','both') DEFAULT 'email',
  `phone_peserta` longtext DEFAULT NULL COMMENT 'JSON array berisi nomor WhatsApp admin/staf yang wajib mengikuti',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `agenda`
--

INSERT INTO `agenda` (`id`, `jenis_agenda`, `perihal_kegiatan`, `slug`, `waktu_mulai`, `waktu_selesai`, `tempat`, `asal_surat`, `tanggal_surat`, `keterangan`, `pakaian`, `disposisi`, `petugas_ditugaskan`, `status`, `diinput_oleh`, `created_at`, `updated_at`, `nomor_surat`, `email_peserta`, `whatsapp_peserta`, `channel_preference_admin`, `phone_peserta`, `created_by`) VALUES
(15, 'internal', 'Permohonan Fasilitasi Zoom Meeting', 'permohonan-fasilitasi-zoom-meeting-28-oktober-2025', '2025-10-28 09:00:00', '2025-10-28 12:30:00', 'Aula Bapperida Kabupaten Sambas', 'Dinas Pekerjaan Umum dan Penataan Ruang Kabupaten Sambas', '2025-10-27', 'Permohonan fasilitas Zoom Meeting dan dukungan teknis dari Dinas Kominfo untuk kegiatan Seminar Antara FS PLB Temajuk.', 'Batik', 'Semua peserta OPD', 'Semua', 'selesai', 'Admin Dinas', '2025-10-29 14:42:57', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(19, 'eksternal', 'Permintaan Data Sekolah yang Belum Memiliki Akses Internet', 'permintaan-data-sekolah-yang-belum-memiliki-akses-internet-23-oktober-2025', '2025-10-23 09:00:00', '2025-10-23 12:30:00', 'Ruang Rapat Diskominfo', 'Dinas Komunikasi dan Informatika Kabupaten Sambas', '2025-10-23', 'Menindaklanjuti surat dari Kementerian Dalam Negeri RI tentang pendataan sekolah yang belum memiliki akses internet.', 'Batik', 'Tidak tercantum di surat ini', '-', 'selesai', 'Admin Dinas', '2025-10-30 03:16:37', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(20, 'eksternal', 'Tindak Lanjut Pengelolaan Website Perangkat Daerah', 'tindak-lanjut-pengelolaan-website-perangkat-daerah-14-oktober-2025', '2025-10-14 09:00:00', '2025-10-14 12:30:00', 'Ruang Rapat Diskominfo', 'Dinas Komunikasi dan Informatika Kabupaten Sambas', '2025-10-17', 'Menindaklanjuti hasil rapat evaluasi Website Perangkat Daerah', 'PDH/Seragam Dinas', '-', 'Seluruh Perangkat Daerah', 'selesai', 'Admin Dinas', '2025-10-30 03:36:09', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(21, 'eksternal', 'Monitoring dan Evaluasi Website Perangkat Daerah (OPD).', 'monitoring-dan-evaluasi-website-perangkat-daerah-opd-14-oktober-2025', '2025-10-14 08:00:00', '2025-10-30 12:00:00', 'Ruang Rapat Diskominfo', 'Dinas Komunikasi dan Informatika Kab. Sambas', '2025-10-08', 'Dalam rangka pelaksanaan tugas dan fungsi Dinas Komunikasi dan Informatika\r\nKabupaten Sambas dalam penyediaan layanan hosting dan subdomain sambas.go.id\r\nsebagai upaya memfasilitasi pengelolaan website Perangkat Daerah di lingkungan\r\nPemerintah Kabupaten Sambas dan dalam rangka mendukung percepatan transformasi\r\ndigital Pemerintah', 'PDH/Seragam Dinas', '--', 'Seluruh Perangkat Daerah', 'selesai', 'Admin Dinas', '2025-10-30 03:49:57', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(24, 'eksternal', 'Permohonan Fasilitasi Zoomeeting Bagian Hukum Setda Kab. Sambas', 'permohonan-fasilitasi-zoomeeting-bagian-hukum-setda-kab-sambas-26-februari-2025', '2025-02-26 15:30:00', '2025-02-26 18:00:00', 'Ruang Rapat Staf Ahli Bupati Sekretariat Daerah Kabupaten Sambas', 'Kepala Bagian Hukum Sekretariat Daerah Kab Sambas', '2025-02-25', 'Sehubungan dengan akan diadakannya rapat klarifikasi usulan formasi Jabatan\r\nFungsional Analis Hukum (JFAH) yang diselenggarankan oleh Badan Pembinaan\r\nHukum Nasional Kementerian Hukum selaku Unit Pembina Teknis JFAH, secara daring\r\nmelalui Zoom Meeting,', 'PDH/Seragam Dinas', '-', '-', 'selesai', 'Admin Dinas', '2025-10-30 08:15:35', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(25, 'internal', 'Permohonan Fasilitasi Zoom Meeting', 'permohonan-fasilitasi-zoom-meeting-12-agustus-2025', '2025-08-12 08:30:00', '2025-08-12 11:30:00', 'Aula Kantor Bupati kabupaten Sambas', 'Kepala Dinas Kesehatan KabupatenSambas', '2025-08-07', 'Sehubungan Akan dilaksanakannya kegiatan Pertemuan Advokasi dan\r\nKoordinasi Sanitasi Total Berbasis Masyarakat (STBM) Kabupaten Sambas maka dari\r\nitu kami mohon kepada bapak/ibu untuk dapat memfasilitasi kegiatan tersebut berupa\r\npenyediaan perlengkapan zoom meeting', 'Kemeja Putih Celana Hitam', '-', '-', 'selesai', 'Admin Dinas', '2025-10-31 00:54:58', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(26, 'internal', 'Undangan', 'undangan-17-oktober-2025', '2025-10-17 14:00:00', '2025-10-17 17:00:00', 'Desa Parit Setia Kec. Jawai Kab. Sambas.', 'Komandan Kodim 1208', '2025-10-16', 'tentang peletakan batu pertama pembangunan fisik gerai,\r\npergudangan dan kelengkapan Koperasi Desa/Kelurahan Merah Putih di Koperasi\r\nDesa Merah Putih Wanajaya Kec. Cibitung Kab. Bekasi Prov. Jawa Barat sekaligus\r\njuga secara daring di 800 KDKMP di berbagai lokasi', 'Pakaian Bebas Rapi', '-', '-', 'selesai', 'Admin Dinas', '2025-10-31 06:46:51', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(27, 'eksternal', 'Undangan Rapat terkait Kesediaan Menjadi\r\nDewan Pengurus Asosiasi Pemerintah\r\nKabupaten Seluruh Indonesia (Apkasi) Masa\r\nBhakti 2025-2030', 'undangan-rapat-terkait-kesediaan-menjadi-dewan-pengurus-asosiasi-pemerintah-kabupaten-seluruh-indonesia-apkasi-masa-bhakti-2025-2030-30-juni-2025', '2025-06-30 13:30:00', '2025-06-30 17:00:00', 'Secara Online Zoom meeting', 'Asosiasi Pemerintahan Kabupaten Seluruh Indonesia', '2025-06-27', 'Bersama ini dengan hormat kami sampaikan bahwa, sesuai dengan hasil\r\nMusyawarah Nasional VI Apkasi yang diselenggarakan pada tanggal 30 Mei\r\n2025, di Kabupaten Minahasa Utara, Ketua Umum dan Sekretaris Jenderal\r\nterpilih bersama Tim Formatur diamanatkan untuk menyusun kepengurusan\r\nApkasi masa bhakti 2025-2030 paling lambat 30 (tiga puluh) hari sejak\r\nberakhirnya Munas VI Apkasi Tahun 2025.', 'PDH/Seragam Dinas', '-', 'Semua', 'selesai', 'Admin Dinas', '2025-10-31 06:54:08', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(28, 'eksternal', 'Rapat Koordinasi Kurbuk Rakor\r\nKurtup', 'rapat-koordinasi-kurbuk-rakor-kurtup-20-oktober-2025', '2025-10-20 08:00:00', '2025-10-20 10:00:00', 'Secara Online Zoom meeting', 'Mentri Dalam Negeri', '2025-10-16', 'Dalam rangka pengendalian inflasi tahun 2025 kma akan dilaksanakan rapat koordinasi kurbuk rakor kurtup yang dirangkaikan dengan arahan mentri keuangan terkait percepatan realisasi belanja untuk menjaga pertumbhan ekonomi', 'PDH/Seragam Dinas', '-', 'Semua', 'selesai', 'Admin Dinas', '2025-10-31 07:01:29', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(29, 'internal', 'Undangan Pelantikan PPPK Tenaga\r\nKesehatan dan Guru serta Pengangkatan\r\nPPPK Teknis Khusus di Lingkungan\r\nKementerian Sosial', 'undangan-pelantikan-pppk-tenaga-kesehatan-dan-guru-serta-pengangkatan-pppk-teknis-khusus-di-lingkungan-kementerian-sosial-03-oktober-2025', '2025-10-03 13:30:00', '2025-10-03 17:00:00', 'Secara Online Zoom meeting', 'Biro Organisasi dan Sumber Daya Manusia', '2025-10-02', 'Berdasarkan Peraturan Pemerintah Nomor 49 Tahun 2018 tentang Manajemen\r\nPegawai Pemerintah dengan Perjanjian Kerja, setiap Calon Pegawai Pemerintah\r\ndengan Perjanjian Kerja (CPPPK) yang telah ditetapkan Nomor Induk PPPK wajib\r\ndilantik bagi Pegawai Pemerintah dengan Perjanjian Kerja (PPPK) yang menduduki\r\nJabatan Fungsional Tenaga Kesehatan dan Guru serta diangkat bagi PPPK Teknis\r\nKhusus.', 'PDH/Seragam Dinas', '-', 'Semua', 'selesai', 'Admin Dinas', '2025-10-31 07:05:58', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(30, 'internal', 'Undangan Sebagai Peserta', 'undangan-sebagai-peserta-18-juni-2025', '2025-06-18 08:30:00', '2025-06-18 12:00:00', 'Secara Online Zoom meeting', 'Direktorat Jendral Politik dan Pemerintahan Umum', '2025-06-14', 'Dalam rangka pelaksanaan program kegiatan pemeliharaan kerukunan umat beragama tahun 2025, Direktorat Jendral Politik dan Pemerintahan Umum Kementrian Dalam Negeri akan mengadakan kegiatan webiner dengan tema \"Penguatan Toleransi Umat Beragama Untuk Indonesia yang Harmonis\"', 'PDH/Seragam Dinas', '-', 'Semua', 'selesai', 'Admin Dinas', '2025-10-31 07:15:11', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(31, 'eksternal', 'Rapat koordinasi kurbuk rakor kurtup', 'rapat-koordinasi-kurbuk-rakor-kurtup-16-september-2025', '2025-09-16 08:00:00', '2025-09-16 13:00:00', 'Secara Online Zoom meeting', 'Mentri Dalam Negeri Sekretariat Jendral', '2025-09-15', 'Dalam rangka pengendalian inflasi tahun 2025 KMA akan dilaksanakan rapat koordinasi kurbuk rakor kurtup yang dirangkai dengan pembahasan evaluasi dukungan pemerintahan daerah dalam program 3 juta rumah dan pelaksanaan peta jalan pembangunan kependudukan', 'PDH/Seragam Dinas', '-', 'Semua', 'selesai', 'Admin Dinas', '2025-10-31 07:20:22', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(32, 'internal', 'Rapat Tim Teknis KK Bidang Kerjasama\r\nKeselamatan/Keamanan dan Pengurusan\r\nSempatan Sosek Malindo Provinsi Kalimantan\r\nBarat Tahun 2025', 'rapat-tim-teknis-kk-bidang-kerjasama-keselamatankeamanan-dan-pengurusan-sempatan-sosek-malindo-provinsi-kalimantan-barat-tahun-2025-27-agustus-2025', '2025-08-27 13:00:00', '2025-08-27 16:00:00', 'Ruang Rapat Badan Perencanaan Pembangunan Daerah Provinsi Kalimantan Barat, Gedung Pelayanan Terpadu Provinsi Klaimantan Barat Jalan Ahmad Yani, Pontianak.', 'Gubernur Kalimantan Barat', '2025-08-25', 'Menindaklanjuti surat Ketua Sekretariat Sosek Malindo Sarawak MKN Negeri\r\nSarawak Nomor : BKNQ/R/60/4 Klt.34 (1) tanggal 18 Juni 2025, Hal : Mesyuarat Tim\r\nTeknis Ke-38 dan Sidang ke-38 JKK/KK Sosek Malindo Peringkat/Tingkat Negeri\r\nSarawak-Provinsi Kalimantan Barat,', 'PDH/Seragam Dinas', '-', 'Semua', 'selesai', 'Admin Dinas', '2025-10-31 07:24:55', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(33, 'internal', 'Koordinasi Evaluasi Tindak Lanjut\r\nKonsolidasi Pengadaan Barang/Jasa\r\nPemerintah se-Provinsi Kalimantan Barat', 'koordinasi-evaluasi-tindak-lanjut-konsolidasi-pengadaan-barangjasa-pemerintah-se-provinsi-kalimantan-barat-20-agustus-2025', '2025-08-20 13:30:00', '2025-08-20 16:30:00', 'Secara Online Zoom meeting', 'Deputi Bidang Koordinasi dan Supervisi, Komisi Pemberantasan Korupsi', '2025-08-15', 'Berdasarkan ketentuan Pasal 6 huruf b Undang-Undang Nomor 19 Tahun 2019\r\ntentang Perubahan Kedua Atas Undang-Undang Nomor 30 Tahun 2002 tentang Komisi\r\nPemberantasan Tindak Pidana Korupsi, bahwa KPK mempunyai tugas melakukan\r\nkoordinasi dengan instansi yang berwenang melaksanakan Pemberantasan Tindak Pidana\r\nKorupsi dan instansi yang bertugas melaksanakan pelayanan publik serta melakukan\r\ntindakan pencegahan sehingga tidak terjadi tindak pidana korupsi.', 'PDH/Seragam Dinas', '-', 'Semua', 'selesai', 'Admin Dinas', '2025-10-31 07:29:24', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(34, 'internal', 'Undangan Sosialisasi Inpres Percepatan\r\n\r\nKonektivitas Jalan Daerah mendukung mendukung Swasembada Pangan\r\ndan Energi', 'undangan-sosialisasi-inpres-percepatan-konektivitas-jalan-daerah-mendukung-mendukung-swasembada-pangan-dan-energi-04-juli-2025', '2025-07-04 14:00:00', '2025-07-04 17:30:00', 'Secara Online Zoom meeting', 'Badan Perencanaan Pembangunan  Nasional REPUBLIK Indonesia', '2025-07-03', 'Sehubungan dengan telah ditetapkannya Instruksi Presiden Nomor 11 Tahun 2025 tentang\r\nPercepatan Peningkatan Konektivitas Jalan Daerah untuk mendukung Swasembada Pangan dan\r\nEnergi,', 'PDH/Seragam Dinas', '-', 'Semua', 'selesai', 'Admin Dinas', '2025-10-31 07:34:24', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(35, 'internal', 'Rencana Kebutuhan Pembangunan Penggilingan dan Gudang Bulog', 'rencana-kebutuhan-pembangunan-penggilingan-dan-gudang-bulog-04-november-2025', '2025-11-04 15:00:00', '2025-11-04 18:00:00', 'Secara Online Zoom Meeting', 'Sekretariat Daerah', '2025-11-01', 'Berkenaan dengan rencana pembangunan gudang dan penggilingan bulog serta koordinasi perwakilan bulog diwilayah kabupaten', 'Batik', '-', 'Semua', 'selesai', 'Admin Dinas', '2025-11-05 01:56:25', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(36, 'eksternal', 'Rapat Koordinasi', 'rapat-koordinasi-04-november-2025', '2025-11-04 08:00:00', '2025-11-04 12:00:00', 'Secara Online Zoom meeting', 'Mentri Dalam Negeri Sekretariat Jendral', '2025-11-01', 'Dalam rangka pengendalian inplasi tahun2025 KMA akan dilaksanakan rapat koordinasi kurbuk rakor kurtup yang dirangkaikan dengan evaluasi dukungan pemerintahan daerah dalam program 3 juta rumah', 'PDH/Seragam Dinas', '-', 'Semua', 'selesai', 'Admin Dinas', '2025-11-05 02:18:58', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(37, 'eksternal', 'Rapat Koordinasi Menyemarakan HUT ke-80 Kemerdekaan RI dan Gerakan Pembagian Bendera Merah putih Tahun 2025', 'rapat-koordinasi-menyemarakan-hut-ke-80-kemerdekaan-ri-dan-gerakan-pembagian-bendera-merah-putih-tahun-2025-04-agustus-2025', '2025-08-04 13:00:00', '2025-08-04 16:30:00', 'Secara Online Zoom Meeting', 'Politik dan Pemerintahan Umum', '2025-08-03', 'Dalam rangka menggelorakan semangat nasionalisme dan cinta tanah air bagi seluruh masyarakat Indonesia serta menindaklanjuti Surat Menteri Sekretaris Negara Nomor B-20/M/S/TU.00.03/07/2025 tanggal 28 Juli 2025 hal Penyampaian Tema, Logo dan Partisipasi Menyemarakkan Peringatan HUT Ke-80 Kemerdekaan RI Tahun 2025 dan Surat Menteri Dalam Negeri Nomor 400.10.1.1/3823/SJ tanggal 15 Juli 2024 hal Gerakan Pembagian Bendera Merah Putih Tahun 2025, bersama ini disampaikan bahwa Direktorat Jenderal Politik dan Pemerintahan Umum Kementerian Dalam Negeri akan menyelenggarakan Rapat Koordinasi\r\n\r\n', 'Batik', '-', 'Semua', 'selesai', 'Admin Dinas', '2025-11-05 02:40:58', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(38, 'eksternal', ' Pelaksanaan Seminar Kesehatan Reproduksi Wanita', 'pelaksanaan-seminar-kesehatan-reproduksi-wanita-04-september-2025', '2025-09-04 08:00:00', '2025-09-04 15:00:00', 'Aula Bupati Sambas', 'Dinas Kesehatan', '2025-06-03', 'Dalam rangka pelaksanaan Seminar Kesehatan Reproduksi Wanita yang\r\ndiselenggarakan oleh RSUD Sambas dan Ikatan Bidan Indonesia (IBI) cabang\r\nSambas bekerja sama dengan UPELKES Provinsi Kalimantan Barat', 'PDH/Seragam Dinas', '-', 'Semua', 'selesai', 'Admin Dinas', '2025-11-05 02:46:12', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(39, 'internal', 'Seminar Kegiatan\r\nFeasibility Study (FS) Pos Lintas Batas (PLB) Temajuk)', 'seminar-kegiatan-feasibility-study-fs-pos-lintas-batas-plb-temajuk-28-oktober-2025', '2025-10-28 09:00:00', '2025-10-28 12:30:00', ' Aula Bapperida Kabupaten Sambas', 'Dinas Pekerjaan Umum dan Penataan Ruang', '2025-10-27', 'Sehubungan dengan akan dilaksanakannya Seminar Antara Kegiatan\r\nFeasibility Study (FS) Pos Lintas Batas (PLB) Temajuk) yang diselenggarakan oleh\r\nDinas Pekerjaan Umum dan Penataan Ruang Kabupaten Sambas', 'Batik', '-', 'Semua', 'selesai', 'Admin Dinas', '2025-11-05 02:58:05', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(40, 'internal', 'Memahami Filosofi', 'memahami-filosofi-11-juli-2025', '2025-07-11 09:00:00', '2025-07-11 13:00:00', 'Secara Online Zoom Meeting', 'Direktorat Jendral Politik dan Pemerintahan Umum', '2025-07-10', 'Masyarakat Ilmu pemerintahan Indonesia (MIPI) adalah organisasi yang dibentuk pada\r\ntanggal 22 Oktober 1991 di Jakarta, sebagai wadah bagi keluarga besar Ilmu\r\nPemerintahan, baik bagi mereka yang berpotensi sebagai ilmuan pada Perguruan Tinggi,\r\npemerhati dan praktisi yang terdapat pada Birokrasi Pemerintahan di seluruh Indonesia.', 'PDH/Seragam Dinas', '-', 'Semua', 'selesai', 'Admin Dinas', '2025-11-05 03:05:05', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(41, 'internal', 'Kebersihan dan Kesehatan Dalam Pengolahan Hewan\r\nTernak untuk Pangan dan Evaluasi', 'kebersihan-dan-kesehatan-dalam-pengolahan-hewan-ternak-untuk-pangan-dan-evaluasi-13-oktober-2025', '2025-10-13 08:00:00', '2025-10-13 13:00:00', 'Secara Online Zoom Meeting', 'Sekretariat Jendral', '2025-10-11', 'Dalam rangka pengendalian inflasi tahun 2025 KMA akan dilaksanakan rapat koordinasi kurbuk rakor kurtup yang dirangkaikan dengan pembahasan kebersihan dan kesehatan dalam pengolahan hewan ternak untuk pangan dan  evaluasi dukungan pemerintah daerah dala, program 3 juta rumah', 'PDH/Seragam Dinas', '-', 'Semua', 'selesai', 'Admin Dinas', '2025-11-05 03:25:03', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(42, 'eksternal', 'Rapat Teknis Arah Kebijakan Perjalanan Dinas Luar Negeri(PDLN) di Masa Efesiensi Anggaran Tahun 2025', 'rapat-teknis-arah-kebijakan-perjalanan-dinas-luar-negeripdln-di-masa-efesiensi-anggaran-tahun-2025-11-juni-2025', '2025-06-11 10:30:00', '2025-06-11 11:30:00', 'Secara Online Zoom Meeting', 'Sekretariat Daerah', '2025-06-10', 'Menindaklanjuti surat undangan rapat dari kementrian dalam negeri republik indonesia', 'PDH/Seragam Dinas', '-', 'Semua', 'selesai', 'Admin Dinas', '2025-11-05 03:37:51', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(43, 'eksternal', 'Rapat Program SINERGI (Kolaborasi Lintas Unit Membangun Negeri) Dalam Rangka Penyediaan Infastruktur di Daerah', 'rapat-program-sinergi-kolaborasi-lintas-unit-membangun-negeri-dalam-rangka-penyediaan-infastruktur-di-daerah-25-juni-2025', '2025-06-25 09:00:00', '2025-06-25 12:30:00', 'Ruang Rapat Reformasi Birokrasi Setda Kab. Sambas', 'Sekretaris Daerah Kab. Sambas', '2025-06-24', 'Menindaklanjuti surat dari kementrian keuangan dalam rangka penyediaan infrastruktur di daerah', 'PDH/Seragam Dinas', '-', 'Semua', 'selesai', 'Admin Dinas', '2025-11-05 05:34:42', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(44, 'internal', 'Kurasi Produk Pameran Kriyanusa', 'kurasi-produk-pameran-kriyanusa-22-agustus-2025', '2025-08-22 21:41:00', '2025-08-22 22:41:00', 'Secara Online Zoom Meeting', 'Dinas Koperasi,Usaha Kecil, Usaha Menengah, Perindustrian dan Perdagangan Kab. Sambas', '2025-08-19', 'Sehubungan dengan keikutsertaan Kabupaten Sambas dalam pameran kriyanusa pada tanggal 3-7 september 2025', 'Pakaian Bebas Rapi', '-', 'Semua', 'selesai', 'Admin Dinas', '2025-11-05 05:46:54', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(45, 'eksternal', 'Pemeriksaan Substantif Permohonan Indikasi Geografis Tenun Cual Sambas oleh Dirjen Kekayaan Intelektual Kementrian Hukum RI', 'pemeriksaan-substantif-permohonan-indikasi-geografis-tenun-cual-sambas-oleh-dirjen-kekayaan-intelektual-kementrian-hukum-ri-22-juli-2025', '2025-07-22 09:00:00', '2025-07-22 12:30:00', 'Sentra IKM Tenun Desa Sumber Harapan', 'Dinas Koperasi,Usaha Kecil, Usaha Menengah, Perindustrian dan Perdagangan Kab. Sambas', '2025-07-17', 'Perihal pemeriksaan substantif permohonan indikasi geografis', 'PDH/Seragam Dinas', '-', 'Semua', 'selesai', 'Admin Dinas', '2025-11-05 06:23:39', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(46, 'eksternal', 'Perkembangan Situasi dan Kondisi Terkini Diberbagai Daerah', 'perkembangan-situasi-dan-kondisi-terkini-diberbagai-daerah-30-agustus-2025', '2025-08-30 15:30:00', '2025-08-30 18:00:00', 'Secara Online Zoom Meeting', 'Sekretariat Jendral', '2025-08-30', 'Merujuk RDG NO 500.8/4793hal perkembangan situasi dan kondisi terkini diberbagai daerah', 'PDH/Seragam Dinas', '-', 'Semua', 'selesai', 'Admin Dinas', '2025-11-05 06:36:42', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(47, 'eksternal', 'Upacara Pelantikan Lulusan Institut Pemerintahan Dalam Negeri', 'upacara-pelantikan-lulusan-institut-pemerintahan-dalam-negeri-28-juli-2025', '2025-07-28 10:00:00', '2025-07-28 14:00:00', 'Secara Online Zoom Meeting', 'Mentri Dalam Negeri', '2025-07-26', 'Dalam rangka pelaksanaan upacara pelantikan lulusan institut pemerintahan', 'PDH/Seragam Dinas', '-', 'Semua', 'selesai', 'Admin Dinas', '2025-11-06 01:18:52', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(48, 'eksternal', 'Sosialisasi Surat Edaran Mentri Dalam Negeri', 'sosialisasi-surat-edaran-mentri-dalam-negeri-14-juli-2025', '2025-07-14 09:00:00', '2025-07-14 13:00:00', 'Secara Online Zoom Meeting', 'Dirjen Bina Pembangunan Daerah', '2025-07-11', 'Menindaklanjuti terbitnya surat edaran mentri dalam negeri nomor 600.3.2/3021/SJ tentang Sinkronisasi dan Penyelarasan Dokumen Kebencanaan dengan Perencanaan Pembangunan Daerah, akan dilaksanakan rakor pusda dalam rangka Sosialisasi Surat Edaran Mentri Dalam Negeri', 'PDH/Seragam Dinas', '1. Sekda Provinsi Seluruh indonesia\r\n2. Sekda Kabupaten Garing Kota Seluruh Indonesia', 'Semua', 'selesai', 'Admin Dinas', '2025-11-06 01:33:24', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(49, 'eksternal', 'Optimalisasi Pelaksanaan Pengetasan Kemiskinan dan\r\nPenghapusan Kemiskinan Ekstrem', 'optimalisasi-pelaksanaan-pengetasan-kemiskinan-dan-penghapusan-kemiskinan-ekstrem-07-juli-2025', '2025-07-07 09:00:00', '2025-07-07 12:30:00', 'Secara Online Zoom Meeting', 'Dirjen Bina Pembangunan Daerah', '2025-07-02', 'Akan dilakukan sosialisasi tentang penilaian kinerja PEMDA THD optimalisasi pelaksanaan pengetasan kemiskinan dan penghapusan kemiskinan ekstrem', 'PDH/Seragam Dinas', '1. SEKDA PROV SELURUH INDONESIA\r\n2. SEKDA KAB GARING KOTA SELURUH INDONESIA', 'Semua', 'selesai', 'Admin Dinas', '2025-11-06 01:56:28', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(50, 'internal', 'Penilaian Eradikasi Frambusia tahun 2025 ', 'penilaian-eradikasi-frambusia-tahun-2025-10-november-2025', '2025-11-10 08:00:00', '2025-11-10 12:30:00', ' Aula Hotel Pantura Jaya Sambas', 'Dinas Kesehatan', '2025-11-04', 'Sehubungan akan dilaksanakannya kegiatan Penilaian Eradikasi Frambusia tahun 2025 di\r\nKabupaten Sambas, maka dengan ini kami akan mengadakan Pertemuan Sosialisasi\r\nFrambusia Dalam Rangka Penilaian Menuju Eradikasi Frambusia Tahun 2025 bersama ini\r\ndiharapkan kepada kepala Puskesmas menugaskan 3 (Tiga) orang terdiri dari Dokter\r\nPuskesmas, Petugas Surveilans dan Pengelola Program Frambusia untuk mengikuti\r\nkegiatan yang akan dilaksanakan', 'Batik', '-', 'Semua', 'selesai', 'Admin Dinas', '2025-11-06 02:04:39', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(51, 'eksternal', 'Perpanjang Masa Jabatan Kepala Desa', 'perpanjang-masa-jabatan-kepala-desa-02-oktober-2025', '2025-10-02 13:30:00', '2025-10-02 16:00:00', 'Ruang Rapat Sekretaris Daerah Kabupaten Sambas', 'Sekretaris Daerah Kab. Sambas', '2025-09-30', 'Sehubung dengan pelaksanaan rapat tindak lanjut Surat Edaran Mentri Dalam Negeri Republik Indonesia Nomor 100.3/4 179/SJ tanggal 31 juli 2025 tentang Perpanjangan Masa Jabatan Kepala Desa', 'Batik', '-', 'Semua', 'selesai', 'Admin Dinas', '2025-11-06 02:16:38', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(52, 'eksternal', 'Pelaksanaan Rakomwil Forsesdasi se-Kalbar Tahun 2025', 'pelaksanaan-rakomwil-forsesdasi-se-kalbar-tahun-2025-28-agustus-2025', '2025-08-28 08:30:00', '2025-08-28 12:30:00', 'Secara Online Zoom Meeting', 'Gubernur Kalimantan Barat', '2025-08-24', 'Penguatan Peran Sekretariat Daerah dalam Mewujudkan Visit dan Misi Kepala Daerah', 'PDH/Seragam Dinas', '-', 'Semua', 'selesai', 'Admin Dinas', '2025-11-06 02:37:45', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(53, 'eksternal', 'Pemberian Penghargaan Innovatite Governmment Award (IGA) Tahun 2025', 'pemberian-penghargaan-innovatite-governmment-award-iga-tahun-2025-05-november-2025', '2025-11-05 00:01:00', '2025-11-05 15:30:00', 'Ruang Rapat Asisten Administrasi Umum Sekretariat Daerah Kab. Sambas', 'Bapperida Kab. Sambas', '2025-11-04', 'Menindaklanjuti Surat dari Kementrian Dalam Negeri Republik Indonesia, Nomor 400.10.11/8767/SJ, 31 Oktober 2025, Hal Tahapan Penilaian Presentasi Kepala Daerah dalam rangka Pemberian Penghargaan Innovative Govvernment Award Tahun 2025', 'Batik', '-', 'Semua', 'selesai', 'Admin Dinas', '2025-11-07 03:09:31', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(54, 'internal', 'Permohonan Falisi Zoom Meeting', 'permohonan-falisi-zoom-meeting-24-oktober-2025', '2025-10-24 09:55:00', '2025-11-10 13:00:00', 'Aula Dinas Tenaga Kerja dan Transmigrasi Kab. Sambas', 'Dinas Tenaga Kerja dan Tranmigrasi Kab. Sambas', '2025-10-22', 'Dalam rangka mendukung pelaksanaan kegiatan Pendataan Usulan Pelaksanaan Kerjasama Daerah melalui Penandatanganan Nota Kesepakatan (MoU) antara Pemerintahan Daerah dengan Kementrian Perindungan Pekerja Migran Indonesia (KPPMI)', 'Batik', '-', 'Semua', 'selesai', 'Admin Dinas', '2025-11-10 07:02:51', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(55, 'eksternal', 'Percepatan Pembentukan Pos Bantuan Hukum di Kabupaten Sambas', 'percepatan-pembentukan-pos-bantuan-hukum-di-kabupaten-sambas-19-september-2025', '2025-09-19 13:30:00', '2025-09-19 16:30:00', 'Aula Sayap Kini Kantor Bupati Sambas', 'Sekretariat Daerah', '2025-09-18', 'Menindaklanjuti Kepala Kantor Wilayah Kementerian Hukum Kalimantan\r\nBarat Nomor : W.16.HN.04.03-4186, Tanggal 15 September 2025, Perihal : Mohon\r\nFasilitasi kegiatan dalam rangka Percepatan Pembentukan Pos Bantuan Hukum di\r\nKabupaten Sambas', 'PDH/Seragam Dinas', '-', 'Semua', 'selesai', 'Admin Dinas', '2025-11-10 07:07:07', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(56, 'internal', 'Pertemuan Advokasi dan Koordinasi Sanitasi Total Berbasis Masyarakat (STMB)', 'pertemuan-advokasi-dan-koordinasi-sanitasi-total-berbasis-masyarakat-stmb-12-agustus-2025', '2025-08-12 08:30:00', '2025-08-12 12:30:00', 'Aula Kantor Bupati Kabupaten Sambas', 'Dinas Kesehatan Kab. Sambas', '2025-08-07', 'Akan dilaksanakannya kegiatan Pertemuan Advokasi dan Koordinasi Sanitasi Total Berbasis Masyarakat (STMB)', 'Batik', '-', 'Semua', 'selesai', 'Admin Dinas', '2025-11-10 07:34:49', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(57, 'internal', 'pelaksanaan PRAMUSCAB KE – IX', 'pelaksanaan-pramuscab-ke-ix-01-september-2025', '2025-09-01 10:35:00', '2025-09-01 13:35:00', 'Ruang Rapat Kantor Bupati', 'Ikatan Bidan Indonesia Kab.Sambas', '2025-08-26', 'Dalam rangka pelaksanaan PRAMUSCAB KE – IX Ikatan Bidan Indonesia\r\nKab.Sambas, kami mohon dukungan dari Dinas Komunikasi dan Informasi\r\nKabupaten Sambas untuk dapat membantu', 'Batik', '-', 'Semua', 'selesai', 'Admin Dinas', '2025-11-10 07:38:51', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(58, 'internal', 'Undangan Ekspose Tindak Lanjut Permohonan Hak Guna Usaha', 'undangan-ekspose-tindak-lanjut-permohonan-hak-guna-usaha-08-juli-2025', '2025-07-08 08:30:00', '2025-11-10 12:40:00', 'Secara Online Zoom Meeting', 'Kementrian Agraria dan Tata Ruang', '2025-07-04', 'Sehubungan dengan Permohonan Hak Guna Usaha yang diajukan atas nama:\r\n\r\n1. Koperasi Maju Jiwa Bersama atas tanah seluas 64,51 ha yang terletak di Desa Galing,\r\nKecamatan Galing, Kabupaten Sambas;\r\n2. Koperasi Tuah Bersama atas tanah seluas 64,73 ha yang terletak di Desa Sijang,\r\nKecamatan Galing, Kabupaten Sambas;\r\n3. Koperasi Produsen Citra Mandiri Sejahtera Melawi atas tanah seluas 703,22 ha yang\r\nterletak di Desa Landau Leban dan Sungai Sampuk, Kecamatan Menukung, Kabupaten\r\nMelawi:\r\n4. Koperasi Produsen Citra Mitra Khatulistiwa atas tanah seluas 531,8388 ha yang terletak di\r\nDesa Popai, Nanga Nuak, Domet Permai, Sungai Mentoba dan Nanga Keruap, Kecamatan\r\nElla Hilir dan Menukung, Kabupaten Melawi;\r\n5. Koperasi Binua Anden Raya atas tanah seluas 1.195,2210 ha Desa Pasak Piang,\r\nKecamatan Sungai Ambawang, Kabupaten Kubu Raya.', 'Batik', '-', 'Semua', 'selesai', 'Admin Dinas', '2025-11-10 07:45:20', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(59, 'internal', 'Pemaparan Hasil Kajian Kebijakan Tahun 2025', 'pemaparan-hasil-kajian-kebijakan-tahun-2025-08-agustus-2025', '2025-08-08 08:30:00', '2025-08-08 12:30:00', 'Secara Online Zoom meeting', 'Ombusman Republik Indonesia prov Kalbar', '2025-08-05', 'Dalam rangka melaksanakan tugas sebagaimana ketentuan Pasal 7 huruf (g) Undang-Undang\r\nNomor 37 Tahun 2008 tentang Ombudsman Republik Indonesia, bahwa Ombudsman\r\nRepublik Indonesia bertugas melakukan upaya pencegahan Maladministrasi dalam\r\npenyelenggaraan pelayanan publik. Sebagai bentuk pelaksanaan tugas tersebut, pada Tahun 2025\r\nPerwakilan Ombudsman Republik Indonesia Provinsi Kalimantan Barat melaksanakan\r\nKajian Kebijakan dengan tema “Pengawasan Penegakan Peraturan Daerah Mengenai\r\nKetertiban Umum pada Pemerintah Kabupaten/Kota di Provinsi Kalimantan Barat”.', 'PDH/Seragam Dinas', '-', 'Semua', 'selesai', 'Admin Dinas', '2025-11-10 07:51:07', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(60, 'eksternal', 'Percepatan Peningkatan Pembangunan Infrastruktur Fisik di kawasan perbatasan negara', 'percepatan-peningkatan-pembangunan-infrastruktur-fisik-di-kawasan-perbatasan-negara-16-oktober-2025', '2025-10-16 13:00:00', '2025-10-16 16:30:00', 'Ruang Rapat Lantai IV Kantor BNPP Jl. Kebon Sirih Nomor 31, Jakarta Pusat', 'Badan Nasional Pengelola Perbatasan Republik Indonesia', '2025-10-13', 'Dengan hormat, sesuai dengan UU Nomor 43 Tahun 2008 Tentang Wilayah Negar jo.\r\nPerpres Nomor 44 Tahun 2017 tentang BNPP, bahwa dalam rangka Percepatan Peningkatan\r\nPembangunan Infrastruktur Fisik di kawasan perbatasan negara perlu adanya dukungan dari\r\nKementerian/Lembaga.\r\nSehubungan dengan hal tersebut, Deputi Bidang Pengelolaan Infrastruktur Kawasan\r\nPerbatasan BNPP melalui keasdepan infrastruktur fisik akan menyelenggarakan rapat\r\npercepatan pembangunan infrastruktur jalan dan jembatan serta transportasi darat, laut dan\r\nudara di kawasan perbatasan', 'Pakaian Bebas Rapi', '-', 'Semua', 'selesai', 'Admin Dinas', '2025-11-10 07:56:53', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(61, 'eksternal', 'Langkah-langkah Percepatan Rencana Pembangunan Pos Lintas Batas Negara (PLBN) Gelombang Ke-3.', 'langkah-langkah-percepatan-rencana-pembangunan-pos-lintas-batas-negara-plbn-gelombang-ke-3-05-juni-2025', '2025-06-05 13:00:00', '2025-06-05 16:30:00', 'Secara Online Zoom meeting', 'Badan Nasional Pengelola Perbatasan Republik Indonesia', '2025-06-04', 'Dipermaklumkan dengan hormat bahwa dalam rangka menindaklanjuti surat Menteri\r\nSekretaris Negara nomor B-22/M/SDK/RK.01.04/05/2025, tanggal 20 Mei 2025, hal\r\nPembangunan Pos Lintas Batas Negara (PLBN), Badan Nasional Pengelola Perbatasan (BNPP)\r\nmelalui Deputi Bidang Pengelolaan Batas Wilayah Negara akan melaksanakan Rapat\r\nKoordinasi yang akan dilaksanakan secara hybrid (luring dan daring)', 'Batik', '-', 'Semua', 'selesai', 'Admin Dinas', '2025-11-10 08:01:33', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(62, 'eksternal', 'Evaluasi Pelaksanaan Program/Kegiatan Pembangunan Jalan Inspeksi Patroli Perbatasan (JIPP) di Kawasan Perbatasan Negara.', 'evaluasi-pelaksanaan-programkegiatan-pembangunan-jalan-inspeksi-patroli-perbatasan-jipp-di-kawasan-perbatasan-negara-18-september-2025', '2025-09-18 13:03:00', '2025-09-18 16:30:00', 'Ruang Rapat Lantai IV Kantor BNPP Jl. Kebon Sirih Nomor 31, Jakarta Pusat', 'Badan Nasional Pengelola Perbatasan Republik Indonesia', '2025-09-10', 'dalam rangka monitoring dan evaluasi\r\npelaksanaan program/kegiatan Pembangunan Jalan Inspeksi Patroli Perbatasan (JIPP), Deputi\r\nBidang Pengelolaan Infrastruktur Kawasan Perbatasan BNPP melalui keasdepan infrastruktur\r\nfisik akan menyelenggarakan Rapat “Pengawasan dan Pengendalian Infrastruktur Fisik Bidang\r\nJalan dan Jembatan, Transportasi, Telekomunikasi dan Energi di Kawasan Perbatasan”', 'Batik', '-', 'Semua', 'selesai', 'Admin Dinas', '2025-11-10 08:06:22', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(63, 'internal', 'Rapat koordinasi bersama Desa di wilayah Daerah Otonomi Baru\r\n(DOB) Kabupaten Sambas Utara ', 'rapat-koordinasi-bersama-desa-di-wilayah-daerah-otonomi-baru-dob-kabupaten-sambas-utara-13-november-2025', '2025-11-13 13:00:00', '2025-11-13 16:30:00', 'Ruang Rapat Sekretaris Daerah Kabupaten Sambas (Lt.2)', 'Sekretariat Daerah', '2025-11-12', 'Zoom Meeting', 'PDH/Seragam Dinas', '- Kepala Desa di wilayah Kecamatan Jawai mengikuti rapat zoom\r\ndi Kantor Camat Jawai\r\n- Kepala Desa di wilayah Kecamatan Teluk Keramat mengikuti\r\nrapat zoom di Kantor Camat Teluk Keramat\r\n- Kepala Desa di wilayah Kecamatan Tangaran mengikuti rapat\r\nzoom di Kantor Camat Tangaran\r\n- Kepala Desa di wilayah Kecamatan Paloh mengikuti rapat zoom\r\ndi Kantor Camat Paloh', 'Semua', 'selesai', 'Admin Dinas', '2025-11-13 01:50:10', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(64, 'eksternal', 'Rapat Koordinasi Penyelenggaraan Nama Rupabumi Kabupaten Sambas Tahun 2025', 'rapat-koordinasi-penyelenggaraan-nama-rupabumi-kabupaten-sambas-tahun-2025-14-november-2025', '2025-11-14 09:00:00', '2025-11-14 11:30:00', 'Ruang Rapat Sekretaris Daerah Kabupaten Sambas (Lt.2)', 'Sekretariat Daerah', '2025-11-11', 'Penyelenggaraan Nama Rupabumi Kabupaten Sambas Tahun 2025\r\n(Khusus Kecamatan Salatiga dan Selakau Timur) secara zoom meeting', 'PDH/Seragam Dinas', '-', '-', 'selesai', 'Admin Dinas', '2025-11-13 01:54:04', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(65, 'eksternal', 'Pembahasan Pertumbuhan Ekonomi Daerah Triwulan III dan Evaluasi\r\nDukungan Pemerintah Daerah', 'pembahasan-pertumbuhan-ekonomi-daerah-triwulan-iii-dan-evaluasi-dukungan-pemerintah-daerah-11-november-2025', '2025-11-11 08:00:00', '2025-11-11 11:30:00', 'Secara Online Zoom meeting', 'Mentri Dalam Negeri Sekretariat Jendral', '2025-11-07', 'Dalam Rangka Pengendalian Inflasi Tahun 2025 KMA akan Dilaksanakan RApat Kordinasi KURBUK\r\nRAKOR KURTUP yang dirangkaikan dengan Pembahasan Pertumbuhan Ekonomi Daerah Triwulan III dan Evaluasi\r\nDukungan Pemerintahan Daerah', 'Batik', '-', 'Semua', 'selesai', 'Admin Dinas', '2025-11-13 02:04:16', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(66, 'internal', 'Laporan Situasi Terkini di Masing - Masing Wilayah oleh Kepala Daerah', 'laporan-situasi-terkini-di-masing-masing-wilayah-oleh-kepala-daerah-28-juli-2025', '2025-07-28 08:00:00', '2025-07-28 12:30:00', 'Ruang Pusdalops BNPB Lt. 12', 'Badan Nasional Penanggulangan Bencana', '2025-07-25', 'Dalam Rangka Penanganan Darurat Bencana Kebakaran Hutan dan Lahan di Beberapa Wilayah Indonesia', 'Batik', '-', 'Semua', 'selesai', 'Admin Dinas', '2025-11-14 01:53:29', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(67, 'eksternal', 'AudiensiStatus Pertanahan diWilayah Kecamatan Sajingan Besar', 'audiensistatus-pertanahan-diwilayah-kecamatan-sajingan-besar-29-oktober-2025', '2025-10-29 09:00:00', '2025-10-29 12:30:00', 'Ruang Rapat sekretariat Daerah Kabupaten sambas', 'Sekretariat Daerah', '2025-10-24', 'Rapat Undangan', 'Batik', '-', 'Seluruh Perangkat Daerah', 'selesai', 'Admin Dinas', '2025-11-14 01:56:55', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(68, 'internal', 'Peluncuran Kelembagaan 80.000 Koperasi Desa/Kelurahan Merah Putih dan Peringatan Hari Koperasi Nasional Ke-78 Tahun 2025', 'peluncuran-kelembagaan-80000-koperasi-desakelurahan-merah-putih-dan-peringatan-hari-koperasi-nasional-ke-78-tahun-2025-21-juli-2025', '2025-07-21 08:00:00', '2025-07-21 14:00:00', 'Aula Utama Kantor Bupati Kab. Sambas', 'Bupati Sambas', '2025-07-16', 'Undangan Tentang Peluncuran Kelembagaan 80.000 Koperasi Desa/Kelurahan Merah Putih', 'Jas Formal', '-', 'Seluruh Perangkat Daerah', 'selesai', 'Admin Dinas', '2025-11-14 02:07:18', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(69, 'eksternal', 'Sosialisasi Penilaian Maladministrasi Penyelenggaraan Pelayanan\r\nPublik Tahun 2025', 'sosialisasi-penilaian-maladministrasi-penyelenggaraan-pelayanan-publik-tahun-2025-10-oktober-2025', '2025-10-10 08:00:00', '2025-10-10 11:30:00', 'Ruang Rapat Sekretaris Daerah', 'Sekretariat Daerah', '2025-10-09', 'Menindaklanjuti Surat Kepala Ombudsman Republik Indonesia Perwakilan\r\nProvinsi Kalimantan Barat Nomor B/818/PC.02-19/X/2025 Tanggal 7 Oktober 2025\r\nPerihal Undangan Sosialisasi Penilaian Maladministrasi Penyelenggaraan Pelayanan\r\nPublik Tahun 2025, maka akan dilaksanakan Sosialisasi Penilaian Maladministrasi\r\nPenyelenggaraan Pelayanan Publik Tahun 2025 via zoom meeting oleh Ombudsman\r\nRepublik Indonesia.', 'Batik', '-', 'Seluruh Perangkat Daerah', 'selesai', 'Admin Dinas', '2025-11-14 02:33:57', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(70, 'eksternal', 'RAPAT KOORDINASI KURBUK\r\nRAKOR KURTUP', 'rapat-koordinasi-kurbuk-rakor-kurtup-17-november-2025', '2025-11-17 08:00:00', '2025-11-17 11:30:00', 'Secara Online Zoom meeting', 'Mentri Dalam Negeri Sekretariat Jendral', '2025-11-14', 'Dalam Rangka Pengendalian Inflasi Tahun 2025 KMA Akan dilaksanakan Rapat Koordinasi KURBUK\r\nRAKOR KURTUP yang dirangkaikan dengan Peran Pemerintah Daerah dalam Penyelenggaraan MBG di Daerah', 'Batik', '-', 'Semua', 'selesai', 'Admin Dinas', '2025-11-17 02:20:44', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(71, 'eksternal', 'rapat koordinasi percepatan realisasi APBD TA 2025', 'rapat-koordinasi-percepatan-realisasi-apbd-ta-2025-17-november-2025', '2025-11-17 10:30:00', '2025-11-17 14:30:00', 'Secara Online Zoom meeting', 'Mentri Dalam Negeri Sekretariat Jendral', '2025-11-14', 'Dalam rangka mendorong percepatan realisasi Anggaran Pendapatan dan Belanja Daerah\r\n(APBD) Tahun Anggaran (TA) 2025 guna mendukung pertumbuhan ekonomi di daerah', 'Kemeja Putih Celana Hitam', '-', 'Semua', 'selesai', 'Admin Dinas', '2025-11-20 03:53:48', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(72, 'eksternal', 'Fasilitasi Zoom Meeting Bagian Hukum', 'fasilitasi-zoom-meeting-bagian-hukum-17-november-2025', '2025-11-17 09:00:00', '2025-11-17 12:30:00', 'Ruang Rapat Reformasi Birokrasi', 'Sekretariat Daerah', '2025-11-14', 'Menindaklanjuti surat dari Direktorat Jendral Otonomi Daerah', 'PDH/Seragam Dinas', '-', 'Semua', 'selesai', 'Admin Dinas', '2025-11-20 05:29:29', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(73, 'eksternal', 'RAPAT KOORDINASI KURBUK RAKOR KURTUP', 'rapat-koordinasi-kurbuk-rakor-kurtup-18-november-2025', '2025-11-18 13:30:00', '2025-11-20 16:30:00', 'Secara Online Zoom meeting', 'Mentri Dalam Negeri Sekretariat Jendral', '2025-11-17', 'DALAM RANGKA PENATAAN RUANG DAN WILAYAH TAHUN 2025 AKAN DILAKS RAPAT KOORDINASI KURBUK RAKOR KURTUP PEMBAHASAN PENATAAN ULANG RENCANA TATA RUANG WILAYAH', 'Batik', '-', 'Semua', 'selesai', 'Admin Dinas', '2025-11-20 05:33:24', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(74, 'eksternal', 'Sosialisasi Roadmap TPAKD 2A26 - 2030 melalui Zoom Meeting', 'sosialisasi-roadmap-tpakd-2a26-2030-melalui-zoom-meeting-21-november-2025', '2025-11-21 13:30:00', '2025-11-21 16:30:00', 'Ruang Rapat Sekretaris Daerah Kabupaten Sambas', 'Sekretariat Daerah', '2025-11-20', 'Undangan Zoom Meeting', 'Batik', '-', 'Semua', 'selesai', 'Admin Dinas', '2025-11-21 07:52:00', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(75, 'eksternal', 'RAPAT KOORDINASI KURBUK\r\nRAKOR KURTUP', 'rapat-koordinasi-kurbuk-rakor-kurtup-24-november-2025', '2025-11-24 08:00:00', '2025-11-24 12:30:00', 'Secara Online Zoom meeting', 'Mentri Dalam Negeri Sekretariat Jendral', '2025-11-21', 'DALAM RANGKA PENGENDALIAN INFLASI TAHUN 2025 KMA AKAN DILAKS RAPAT KOORDINASI KURBUK\r\nRAKOR KURTUP YG DIRANGKAIKAN DGN EVALUASI DUKUNGAN PEMERINTAH DAERAH', 'PDH/Seragam Dinas', '-', 'Semua', 'selesai', 'Admin Dinas', '2025-11-25 02:15:45', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(78, 'eksternal', ' Pengharmonisasian, pembulatan dan pemantapan konsepsi\r\n Rancangan Peraturan Bupati Sambas tentang Tambahan\r\n Penghasilan Aparatur Sipil Negara di Lingkungan Pemerintah\r\n Kabupaten Sambas', 'pengharmonisasian-pembulatan-dan-pemantapan-konsepsi-rancangan-peraturan-bupati-sambas-tentang-tambahan-penghasilan-aparatur-sipil-negara-di-lingkungan-pemerintah-kabupaten-sambas-02-desember-2025', '2025-12-02 09:00:00', '2025-12-02 12:00:00', 'Aula BKPSDM kab. Sambas', 'Kanwil Kemenhum Kalbar', '2025-12-27', ' Rapat Pengharmonisasian, Pembulatan dan\r\n Pemantapan Konsepsi Rancangan Peraturan\r\n Daerah/Peraturan Kepala Daerah Tahun 2025', 'PDH/Seragam Dinas', 'TL surat ini', 'Tri Nugroho', 'selesai', 'Admin Dinas', '2025-12-02 08:37:55', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', NULL, NULL),
(97, 'internal', 'apasajaboleh', 'apasajaboleh-17-mei-2026', '2026-05-17 23:30:00', '2026-05-18 00:30:00', 'Ruang Rapat Diskominfo', 'Kepala Dinas', '2026-05-14', 'qwertyujnbvcx', 'Baju Dinas', 'asddf', 'Semua Kepala Bidang', 'selesai', 'Admin Dinas', '2026-05-17 13:28:16', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', '[{\"phone\":\"085346807190\",\"channel\":\"both\"}]', NULL),
(98, 'internal', 'qwert123sdfr', 'qwert123sdfr-18-mei-2026', '2026-05-18 01:01:00', '2026-05-18 02:02:00', 'Ruang Rapat Diskominfo', 'Kepala Dinas', '2026-05-14', 'aswqwqwertyasd', 'Baju Dinas', 'qwsssdd', 'Semua Kepala Bidang', 'selesai', 'Admin Dinas', '2026-05-17 16:03:26', '2026-05-19 09:54:42', NULL, NULL, NULL, 'email', '[{\"phone\":\"085346807190\",\"channel\":\"both\"}]', NULL),
(99, 'internal', 'Rapat Test Notifikasi WhatsApp dan FCM', 'rapat-test-notifikasi-whatsapp-dan-fcm-20-mei-2026', '2026-05-20 10:00:12', '2026-05-20 12:00:12', 'Ruang Rapat Diskominfo Sambas', 'Dinas Kominfo', '2026-05-19', 'Agenda untuk test notifikasi', 'Bebas Rapi', '-', 'Tim IT', 'terjadwal', 'Admin', '2026-05-19 09:58:12', '2026-05-19 09:58:12', NULL, NULL, NULL, 'email', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `agenda_reminders`
--

CREATE TABLE `agenda_reminders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(100) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `channel` enum('whatsapp','fcm') NOT NULL DEFAULT 'whatsapp',
  `is_sent` tinyint(1) NOT NULL DEFAULT 0,
  `sent_at` timestamp NULL DEFAULT NULL,
  `agenda_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `agenda_reminders`
--

INSERT INTO `agenda_reminders` (`id`, `nama`, `phone_number`, `channel`, `is_sent`, `sent_at`, `agenda_id`, `created_at`, `updated_at`) VALUES
(1, 'Semua Kepala Bidang', '628534687190', 'whatsapp', 1, '2026-05-17 15:38:20', 97, '2026-05-17 13:28:16', '2026-05-17 15:38:20'),
(2, 'IQBAL', '6285135760981', 'whatsapp', 1, '2026-05-17 15:38:19', 97, '2026-05-17 13:48:44', '2026-05-17 15:38:19'),
(3, 'Semua Kepala Bidang', '6285346807190', 'whatsapp', 1, '2026-05-17 15:53:20', 97, '2026-05-17 15:45:11', '2026-05-17 15:53:20'),
(4, 'Semua Kepala Bidang', '6285346807190', 'whatsapp', 1, '2026-05-17 17:08:19', 98, '2026-05-17 16:03:26', '2026-05-17 17:08:19'),
(5, 'ballll', '6285135760981', 'whatsapp', 0, NULL, 98, '2026-05-19 16:03:47', '2026-05-19 16:03:47');

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `dokumen_agenda`
--

CREATE TABLE `dokumen_agenda` (
  `id` int(11) NOT NULL,
  `agenda_id` int(11) NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `content_hash` varchar(64) DEFAULT NULL,
  `original_name` varchar(255) DEFAULT NULL,
  `tanggal_upload` datetime DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `dokumen_agenda`
--

INSERT INTO `dokumen_agenda` (`id`, `agenda_id`, `nama_file`, `content_hash`, `original_name`, `tanggal_upload`, `created_at`, `updated_at`) VALUES
(12, 15, 'fasilitasi zoom PUPR.pdf', NULL, NULL, '2025-10-29 21:42:57', '2025-10-29 14:42:57', '2026-05-19 09:54:42'),
(13, 19, 'Surat_359_Egov_permintaan data sekolah_signed.pdf', NULL, NULL, '2025-10-30 10:16:37', '2025-10-30 03:16:37', '2026-05-19 09:54:42'),
(14, 20, '20251024145401surat tindak lanjut rapat website revisi-SRIKANDI_2.pdf', NULL, NULL, '2025-10-30 10:36:09', '2025-10-30 03:36:09', '2026-05-19 09:54:42'),
(15, 21, 'Surat_339_Undangan pengawasan subdomain revisi_egov_signed.pdf (1) (1).pdf', NULL, NULL, '2025-10-30 10:49:57', '2025-10-30 03:49:57', '2026-05-19 09:54:42'),
(22, 24, 'Permohonan Fasilitas Zoomeeting Bagian Hukum Setda Kab sbs.pdf', NULL, NULL, '2025-10-30 15:15:35', '2025-10-30 08:15:35', '2026-05-19 09:54:42'),
(23, 25, '400.7.11_8367_KESMAS-DKS.pdf', NULL, NULL, '2025-10-31 07:54:58', '2025-10-31 00:54:58', '2026-05-19 09:54:42'),
(24, 26, '9. Surat Undangan Vicon Groundbreaking Koperasi Merah Putih - NINO NUGROHO.pdf', NULL, NULL, '2025-10-31 13:46:51', '2025-10-31 06:46:51', '2026-05-19 09:54:42'),
(25, 27, '216_Srt Undangan zoom Kesediaan Menjadi Dewan Pengurus 2025-2030-1 - NINO NUGROHO.pdf', NULL, NULL, '2025-10-31 13:54:08', '2025-10-31 06:54:08', '2026-05-19 09:54:42'),
(26, 28, '500.2.3 7410 SJ Rakor Pengendalian Inflasi  - NINO NUGROHO.pdf', NULL, NULL, '2025-10-31 14:01:29', '2025-10-31 07:01:29', '2026-05-19 09:54:42'),
(27, 29, '2704 - Und. Pelantikan PPPK Nakes dan Guru serta PPPK Teknis Khusus - NINO NUGROHO.pdf', NULL, NULL, '2025-10-31 14:05:58', '2025-10-31 07:05:58', '2026-05-19 09:54:42'),
(28, 30, '25207 (200.6.5 e-413 POLPUM  Toleransi Umaat Beragama - Dinas Kominfo Sambas.pdf', NULL, NULL, '2025-10-31 14:15:11', '2025-10-31 07:15:11', '2026-05-19 09:54:42'),
(29, 31, '25372 (500.2.3 5049 SJ Rakor Pengendalian Inflasi - NINO NUGROHO.pdf', NULL, NULL, '2025-10-31 14:20:22', '2025-10-31 07:20:22', '2026-05-19 09:54:42'),
(30, 32, '20250825125441Undangan Rapat KK III Sosek Malindo 2025 - Dinas Kominfo Sambas.pdf', NULL, NULL, '2025-10-31 14:24:55', '2025-10-31 07:24:55', '2026-05-19 09:54:42'),
(31, 33, 'B 5297 - TL KONSOLIDASI PBJ KALBAR (KADA) - Dinas Kominfo Sambas.pdf', NULL, NULL, '2025-10-31 14:29:24', '2025-10-31 07:29:24', '2026-05-19 09:54:42'),
(32, 34, 'B-10304 Surat Undangan Sosialisasi Inpres JD 2025_4 Juli 25.signed - Othman Alydrus.pdf', NULL, NULL, '2025-10-31 14:34:24', '2025-10-31 07:34:24', '2026-05-19 09:54:42'),
(33, 36, 'RDG Rakor Inflasi 041125 - NINO NUGROHO.pdf', NULL, NULL, '2025-11-05 09:18:58', '2025-11-05 02:18:58', '2026-05-19 09:54:42'),
(34, 37, 'CamScanner 04-08-2025 10.09 - Dinas Kominfo Sambas.pdf', NULL, NULL, '2025-11-05 09:40:58', '2025-11-05 02:40:58', '2026-05-19 09:54:42'),
(35, 38, 'CamScanner 26-08-25 12.22 - Dinas Kominfo Sambas.pdf', NULL, NULL, '2025-11-05 09:46:12', '2025-11-05 02:46:12', '2026-05-19 09:54:42'),
(36, 39, 'DOC-20251028-WA0005. - NINO NUGROHO.pdf', NULL, NULL, '2025-11-05 09:58:05', '2025-11-05 02:58:05', '2026-05-19 09:54:42'),
(37, 40, 'FIX_Penerusan Surat ke Daerah Webinar 09072025 - NINO NUGROHO.pdf', NULL, NULL, '2025-11-05 10:05:05', '2025-11-05 03:05:05', '2026-05-19 09:54:42'),
(38, 41, 'https_0!00!0esign.kemendagri.go.id0!0DS0!020250!0radiogram0!01760225757ayhtcl - NINO NUGROHO.pdf', NULL, NULL, '2025-11-05 10:25:03', '2025-11-05 03:25:03', '2026-05-19 09:54:42'),
(39, 43, 'IMG-20250624-WA0000 - Dinas Kominfo Sambas.jpg', NULL, NULL, '2025-11-05 12:34:42', '2025-11-05 05:34:42', '2026-05-19 09:54:42'),
(40, 44, 'Kurasi produk pameran kriyanusa via zoom - Dinas Kominfo Sambas.pdf', NULL, NULL, '2025-11-05 12:46:54', '2025-11-05 05:46:54', '2026-05-19 09:54:42'),
(41, 45, 'Permohonan Fasilitas Zoom Meeting - NINO NUGROHO.pdf', NULL, NULL, '2025-11-05 13:23:39', '2025-11-05 06:23:39', '2026-05-19 09:54:42'),
(42, 46, 'Radiogram Penyesuaian Waktu Rakor Antisipasi Situasi dan Kondisi Terkini pada Pemda - Dinas Kominfo Sambas.pdf', NULL, NULL, '2025-11-05 13:36:42', '2025-11-05 06:36:42', '2026-05-19 09:54:42'),
(43, 47, 'Radiogram PPM 2025 bagi Gubernur,Bupati,dan Walikota Seluruh Indonesia - NINO NUGROHO.pdf', NULL, NULL, '2025-11-06 08:18:52', '2025-11-06 01:18:52', '2026-05-19 09:54:42'),
(44, 48, 'Radiogram Rakorpusda Kebencanaan - NINO NUGROHO.pdf', NULL, NULL, '2025-11-06 08:33:24', '2025-11-06 01:33:24', '2026-05-19 09:54:42'),
(45, 49, 'RADIOGRAM SOSIALISASI OPPKPKE 7 JULI 2025 - NINO NUGROHO.pdf', NULL, NULL, '2025-11-06 08:56:28', '2025-11-06 01:56:28', '2026-05-19 09:54:42'),
(46, 50, 'Undangan Penilaian Frambusia .pdf', NULL, NULL, '2025-11-06 09:04:39', '2025-11-06 02:04:39', '2026-05-19 09:54:42'),
(47, 51, 'Undangan Rapat 2 Okt 2025 - NINO NUGROHO.pdf', NULL, NULL, '2025-11-06 09:16:38', '2025-11-06 02:16:38', '2026-05-19 09:54:42'),
(49, 53, 'Permohonan Fasilitasi Zoom Meeting.pdf', NULL, NULL, '2025-11-07 10:09:31', '2025-11-07 03:09:31', '2026-05-19 09:54:42'),
(50, 54, 'Surat Fasilitasi ttd stempel - NINO NUGROHO.pdf', NULL, NULL, '2025-11-10 14:02:51', '2025-11-10 07:02:51', '2026-05-19 09:54:42'),
(51, 55, 'Surat Percepatan Posbankum September - Othman Alydrus.pdf', NULL, NULL, '2025-11-10 14:07:07', '2025-11-10 07:07:07', '2026-05-19 09:54:42'),
(52, 56, 'Surat Permohonan Fasilitasi Zoom Meeting Dinas Kesehatan - Othman Alydrus.pdf', NULL, NULL, '2025-11-10 14:34:49', '2025-11-10 07:34:49', '2026-05-19 09:54:42'),
(53, 57, 'SURAT PERMOHONAN KOMINFO PRAMUSCAB_250901_085234 - NINO NUGROHO.pdf', NULL, NULL, '2025-11-10 14:38:51', '2025-11-10 07:38:51', '2026-05-19 09:54:42'),
(54, 58, 'Und Ekspose Sambas Melawi Kkr - NINO NUGROHO.pdf', NULL, NULL, '2025-11-10 14:45:20', '2025-11-10 07:45:20', '2026-05-19 09:54:42'),
(55, 59, 'Und LHA DPRD Sambas - Dinas Kominfo Kabupaten Sambas (Bidang E-Government).pdf', NULL, NULL, '2025-11-10 14:51:07', '2025-11-10 07:51:07', '2026-05-19 09:54:42'),
(56, 60, 'UND PESERTA RAPAT 16 OKT 2025 - NINO NUGROHO.pdf', NULL, NULL, '2025-11-10 14:56:53', '2025-11-10 07:56:53', '2026-05-19 09:54:42'),
(57, 61, 'UND RAPAT 5 JUNI 2025-PERCEPATAN PEMBANGUNAN PLBN GELOMBANG KE-3 - Dinas Kominfo Sambas.pdf', NULL, NULL, '2025-11-10 15:01:33', '2025-11-10 08:01:33', '2026-05-19 09:54:42'),
(58, 62, 'UND RAPAT PESERTA 18 SEPTEMBER 2025 - Othman Alydrus.pdf', NULL, NULL, '2025-11-10 15:06:22', '2025-11-10 08:06:22', '2026-05-19 09:54:42'),
(59, 63, 'UNDANGAN RAKOR DOB KSU.pdf', NULL, NULL, '2025-11-13 08:50:10', '2025-11-13 01:50:10', '2026-05-19 09:54:42'),
(60, 64, 'Rapat Koordinasi Penyelenggaraan Nama Rupabumi  Kec. Salatiga dan Kec. Selakau Timur.pdf', NULL, NULL, '2025-11-13 08:54:04', '2025-11-13 01:54:04', '2026-05-19 09:54:42'),
(61, 65, 'RDG INFLASI 11 NOV 25 - NINO NUGROHO.pdf', NULL, NULL, '2025-11-13 09:04:16', '2025-11-13 02:04:16', '2026-05-19 09:54:42'),
(62, 66, 'Und.Rapat Monitoring Karhutla_0001 - NINO NUGROHO.pdf', NULL, NULL, '2025-11-14 08:53:29', '2025-11-14 01:53:29', '2026-05-19 09:54:42'),
(63, 67, 'undangan audiensi pertanahan sajingan 29 okt 2025 - NINO NUGROHO.pdf', NULL, NULL, '2025-11-14 08:56:55', '2025-11-14 01:56:55', '2026-05-19 09:54:42'),
(64, 68, 'Undangan Koperasi Desa - NINO NUGROHO.pdf', NULL, NULL, '2025-11-14 09:07:18', '2025-11-14 02:07:18', '2026-05-19 09:54:42'),
(65, 69, 'Undangan Ombudsman fasilitasi zoom - NINO NUGROHO.pdf', NULL, NULL, '2025-11-14 09:33:57', '2025-11-14 02:33:57', '2026-05-19 09:54:42'),
(66, 70, '500.2.3_9253_SJ - NINO NUGROHO.pdf', NULL, NULL, '2025-11-17 09:20:44', '2025-11-17 02:20:44', '2026-05-19 09:54:42'),
(67, 71, 'Undangan Rapat Koordinasi Percepatan Realisasi APBD TA 2025 - NINO NUGROHO.pdf', NULL, NULL, '2025-11-20 10:53:48', '2025-11-20 03:53:48', '2026-05-19 09:54:42'),
(68, 72, 'DOC-20251117-WA0004. - NINO NUGROHO.pdf', NULL, NULL, '2025-11-20 12:29:29', '2025-11-20 05:29:29', '2026-05-19 09:54:42'),
(69, 73, '500.12.3_9289_SJ - NINO NUGROHO.pdf', NULL, NULL, '2025-11-20 12:33:24', '2025-11-20 05:33:24', '2026-05-19 09:54:42'),
(70, 74, 'Undangan Zoom Meeting Roadmap TPAKD (Jumat, 21 November 2025) - NINO NUGROHO.pdf', NULL, NULL, '2025-11-21 14:52:00', '2025-11-21 07:52:00', '2026-05-19 09:54:42'),
(71, 75, 'Radiogram Rakor Inflasi 241125 - NINO NUGROHO.pdf', NULL, NULL, '2025-11-25 09:15:45', '2025-11-25 02:15:45', '2026-05-19 09:54:42'),
(73, 78, 'Undangan Harmon Sambas ttg Tambahan Penghasilan ASN (2).pdf', NULL, NULL, '2025-12-02 15:37:55', '2025-12-02 08:37:55', '2026-05-19 09:54:42'),
(83, 97, '20260517202816_4437-Article_Text-12194-1-10-20241222_1.pdf', NULL, NULL, '2026-05-17 20:28:16', '2026-05-17 13:28:16', '2026-05-19 09:54:42'),
(84, 98, '20260517230326_4437-Article_Text-12194-1-10-20241222_1.pdf', NULL, NULL, '2026-05-17 23:03:26', '2026-05-17 16:03:26', '2026-05-19 09:54:42');

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `fcm_device_tokens`
--

CREATE TABLE `fcm_device_tokens` (
  `id` int(11) NOT NULL,
  `device_token` varchar(500) NOT NULL,
  `user_identifier` varchar(255) DEFAULT NULL,
  `device_name` varchar(255) DEFAULT NULL,
  `device_type` enum('web','android','ios') DEFAULT 'web',
  `browser_info` varchar(500) DEFAULT NULL,
  `subscribed_agendas` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`subscribed_agendas`)),
  `is_active` tinyint(1) DEFAULT 1,
  `last_used_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `fcm_device_tokens`
--

INSERT INTO `fcm_device_tokens` (`id`, `device_token`, `user_identifier`, `device_name`, `device_type`, `browser_info`, `subscribed_agendas`, `is_active`, `last_used_at`, `created_at`, `updated_at`) VALUES
(1, 'test_device_token_1778324826', NULL, 'Test Device (CLI)', 'web', NULL, '[\"87\"]', 1, '2026-05-09 11:07:06', '2026-05-09 11:07:06', '2026-05-09 11:07:06'),
(2, 'test_device_token_1778345701', NULL, 'Test Device (CLI)', 'web', NULL, '[\"88\"]', 1, '2026-05-09 16:55:01', '2026-05-09 16:55:01', '2026-05-09 16:55:01'),
(3, 'cnEca1Yp047SLOqMt_SXi-:APA91bHvM34KMqVvnSDeTKxUv-G_NMNiq_Qq2S4nszQjTWKnnPfHE_TlFszYsn0fdpFzvu7sNV_oZAHY_LafRMI_Eg2YtlNe6Q0JKS-jA2_GjBkj4S5XJtk', NULL, 'Safari/537.36 Browser', 'web', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', NULL, 1, '2026-05-16 17:15:25', '2026-05-16 17:15:25', '2026-05-16 17:15:25'),
(4, 'eV5-RfJJvnJ25uf5X4vjN-:APA91bFNnhs-r8KXFCIxusgZfiUp7kV99ahYfm-AZKnbAqhYshTOEUTrlkK8uhPVeBNafsFiWulJBJoWT3rBGyIJzPY25DwXL8OrAFwspeiwr54A0dTGeRw', NULL, 'Safari/537.36 Browser', 'web', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', NULL, 1, '2026-05-16 17:18:57', '2026-05-16 17:18:57', '2026-05-16 17:18:57'),
(5, 'd1Q6fVwqcg_VXR6duvGs96:APA91bHRbQwWpM87QiEdkXxBX16OI5ONkkoHrSExHbyrLxS3BfxW0MbwTdo_pOeQI5FwzsJsIPAxHIbGehhlUxPQHgzIsIi368512sFxG8UC4k_SkspK3HY', NULL, 'Safari/537.36 Browser', 'web', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '[97]', 1, '2026-05-17 13:48:44', '2026-05-17 00:38:36', '2026-05-17 13:48:44'),
(6, 'd1Q6fVwqcg_VXR6duvGs96:APA91bF_QLCtECGFq1vWOp_bxN9PzWblDnPj53FCvLvSTgwL4T-iIMzNewVJl1FGsrhDibajD8oy1JPQrish6BNitdJQ9hxUUzg2N_4REhYjP3aM6p2u83k', NULL, 'Web Browser', 'web', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '[47,98]', 1, '2026-05-19 16:03:47', '2026-05-17 16:03:12', '2026-05-19 16:03:47');

-- --------------------------------------------------------

--
-- Struktur dari tabel `fcm_notif_sent`
--

CREATE TABLE `fcm_notif_sent` (
  `id` int(11) NOT NULL,
  `agenda_id` int(11) NOT NULL,
  `sent_date` date NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `fcm_count` int(11) DEFAULT 0,
  `wa_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `fcm_tokens`
--

CREATE TABLE `fcm_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `token` varchar(500) NOT NULL,
  `device_name` varchar(100) DEFAULT NULL,
  `subscribed_agendas` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`subscribed_agendas`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `fcm_tokens`
--

INSERT INTO `fcm_tokens` (`id`, `token`, `device_name`, `subscribed_agendas`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'test_device_token_1778324826', 'Test Device (CLI)', '[\"87\"]', 1, '2026-05-09 11:07:06', '2026-05-09 11:07:06'),
(2, 'test_device_token_1778345701', 'Test Device (CLI)', '[\"88\"]', 1, '2026-05-09 16:55:01', '2026-05-09 16:55:01'),
(3, 'cnEca1Yp047SLOqMt_SXi-:APA91bHvM34KMqVvnSDeTKxUv-G_NMNiq_Qq2S4nszQjTWKnnPfHE_TlFszYsn0fdpFzvu7sNV_oZAHY_LafRMI_Eg2YtlNe6Q0JKS-jA2_GjBkj4S5XJtk', 'Safari/537.36 Browser', NULL, 1, '2026-05-16 17:15:25', '2026-05-16 17:15:25'),
(4, 'eV5-RfJJvnJ25uf5X4vjN-:APA91bFNnhs-r8KXFCIxusgZfiUp7kV99ahYfm-AZKnbAqhYshTOEUTrlkK8uhPVeBNafsFiWulJBJoWT3rBGyIJzPY25DwXL8OrAFwspeiwr54A0dTGeRw', 'Safari/537.36 Browser', NULL, 1, '2026-05-16 17:18:57', '2026-05-16 17:18:57'),
(5, 'd1Q6fVwqcg_VXR6duvGs96:APA91bHRbQwWpM87QiEdkXxBX16OI5ONkkoHrSExHbyrLxS3BfxW0MbwTdo_pOeQI5FwzsJsIPAxHIbGehhlUxPQHgzIsIi368512sFxG8UC4k_SkspK3HY', 'Safari/537.36 Browser', '[97]', 1, '2026-05-17 00:38:36', '2026-05-17 13:48:44'),
(6, 'd1Q6fVwqcg_VXR6duvGs96:APA91bF_QLCtECGFq1vWOp_bxN9PzWblDnPj53FCvLvSTgwL4T-iIMzNewVJl1FGsrhDibajD8oy1JPQrish6BNitdJQ9hxUUzg2N_4REhYjP3aM6p2u83k', 'Web Browser', '[47,98]', 1, '2026-05-17 16:03:12', '2026-05-19 16:03:47');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_05_08_100000_create_agenda_table', 1),
(5, '2026_05_08_100241_add_slug_to_agendas_table', 2),
(6, '2026_05_08_122620_create_dokumen_agenda_table', 2),
(7, '2026_05_08_200000_add_performance_indexes_to_agenda_table', 2),
(8, '2026_05_08_210000_add_username_to_users_table', 2),
(9, '2026_05_08_220000_create_agenda_reminders_table', 2),
(10, '2026_05_08_230000_add_content_hash_to_dokumen_agenda_table', 3),
(11, '2026_05_19_100000_refactor_agenda_reminders_for_multichannel', 3),
(12, '2026_05_19_100001_create_fcm_tokens_table', 3),
(13, '2026_05_19_200000_import_legacy_notification_data', 4);

-- --------------------------------------------------------

--
-- Struktur dari tabel `notifikasi_pendaftar`
--

CREATE TABLE `notifikasi_pendaftar` (
  `id` int(11) NOT NULL,
  `agenda_id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `channel_preference` enum('email','whatsapp','both') NOT NULL DEFAULT 'email',
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `email_sent` tinyint(1) NOT NULL DEFAULT 0,
  `email_sent_at` datetime DEFAULT NULL,
  `whatsapp_sent` tinyint(1) NOT NULL DEFAULT 0,
  `whatsapp_sent_at` datetime DEFAULT NULL,
  `sudah_dikirim` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `notifikasi_pendaftar`
--

INSERT INTO `notifikasi_pendaftar` (`id`, `agenda_id`, `nama`, `email`, `phone_number`, `channel_preference`, `status`, `email_sent`, `email_sent_at`, `whatsapp_sent`, `whatsapp_sent_at`, `sudah_dikirim`, `created_at`, `updated_at`) VALUES
(14, 97, 'Semua Kepala Bidang', NULL, '628534687190', 'whatsapp', 'aktif', 0, NULL, 1, '2026-05-17 22:38:20', 0, '2026-05-17 13:28:16', '2026-05-17 15:38:20'),
(15, 97, 'IQBAL', NULL, '6285135760981', 'both', 'aktif', 0, NULL, 1, '2026-05-17 22:38:19', 0, '2026-05-17 13:48:44', '2026-05-17 15:38:19'),
(16, 97, 'Semua Kepala Bidang', NULL, '6285346807190', 'both', 'aktif', 0, NULL, 1, '2026-05-17 22:53:20', 0, '2026-05-17 15:45:11', '2026-05-17 15:53:20'),
(17, 98, 'Semua Kepala Bidang', NULL, '6285346807190', 'both', 'aktif', 0, NULL, 1, '2026-05-18 00:08:19', 0, '2026-05-17 16:03:26', '2026-05-17 17:08:19'),
(18, 98, 'ballll', NULL, '6285135760981', 'both', 'aktif', 0, NULL, 0, NULL, 0, '2026-05-19 16:03:47', '2026-05-19 16:03:47');

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `peserta_notif`
--

CREATE TABLE `peserta_notif` (
  `id` int(11) NOT NULL,
  `id_agenda` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `status_kirim` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'user',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'admin', 'admin@agenda-egov.local', '2026-05-19 09:59:46', '$2y$12$xJkFGoY2gHAnjVu1tKnzgu/JdsbFygfvlIR/TR4PTeSxwDWJptzw.', 'admin', NULL, '2026-05-19 09:59:46', '2026-05-19 10:04:40'),
(2, 'User Biasa', 'user', 'user@agenda-egov.local', '2026-05-19 09:59:46', '$2y$12$YkH4uD/MMGXkI1ZTq7Dy5uMdVyKXIx04C0ryvg/0rfJoZ055Yv2v2', 'user', NULL, '2026-05-19 09:59:46', '2026-05-19 10:05:21');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `agenda`
--
ALTER TABLE `agenda`
  ADD PRIMARY KEY (`id`),
  ADD KEY `agenda_created_by_foreign` (`created_by`),
  ADD KEY `agenda_status_idx` (`status`),
  ADD KEY `agenda_waktu_mulai_idx` (`waktu_mulai`),
  ADD KEY `agenda_status_waktu_mulai_idx` (`status`,`waktu_mulai`);

--
-- Indeks untuk tabel `agenda_reminders`
--
ALTER TABLE `agenda_reminders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `agenda_reminders_phone_number_agenda_id_unique` (`phone_number`,`agenda_id`),
  ADD KEY `agenda_reminders_agenda_id_index` (`agenda_id`),
  ADD KEY `agenda_reminders_phone_number_index` (`phone_number`);

--
-- Indeks untuk tabel `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indeks untuk tabel `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indeks untuk tabel `dokumen_agenda`
--
ALTER TABLE `dokumen_agenda`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dokumen_agenda_content_hash_unique` (`content_hash`),
  ADD KEY `agenda_id` (`agenda_id`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `fcm_device_tokens`
--
ALTER TABLE `fcm_device_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `device_token` (`device_token`),
  ADD KEY `idx_device_token` (`device_token`),
  ADD KEY `idx_is_active` (`is_active`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indeks untuk tabel `fcm_notif_sent`
--
ALTER TABLE `fcm_notif_sent`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_agenda_date` (`agenda_id`,`sent_date`);

--
-- Indeks untuk tabel `fcm_tokens`
--
ALTER TABLE `fcm_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fcm_tokens_token_unique` (`token`),
  ADD KEY `fcm_tokens_is_active_index` (`is_active`);

--
-- Indeks untuk tabel `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indeks untuk tabel `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `notifikasi_pendaftar`
--
ALTER TABLE `notifikasi_pendaftar`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_email_per_agenda` (`agenda_id`,`email`),
  ADD UNIQUE KEY `unique_phone_per_agenda` (`agenda_id`,`phone_number`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_phone` (`phone_number`),
  ADD KEY `idx_channel` (`channel_preference`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_agenda_id` (`agenda_id`);

--
-- Indeks untuk tabel `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indeks untuk tabel `peserta_notif`
--
ALTER TABLE `peserta_notif`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_agenda` (`id_agenda`);

--
-- Indeks untuk tabel `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD KEY `users_role_index` (`role`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `agenda`
--
ALTER TABLE `agenda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT untuk tabel `agenda_reminders`
--
ALTER TABLE `agenda_reminders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `dokumen_agenda`
--
ALTER TABLE `dokumen_agenda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `fcm_device_tokens`
--
ALTER TABLE `fcm_device_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `fcm_notif_sent`
--
ALTER TABLE `fcm_notif_sent`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `fcm_tokens`
--
ALTER TABLE `fcm_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `notifikasi_pendaftar`
--
ALTER TABLE `notifikasi_pendaftar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `peserta_notif`
--
ALTER TABLE `peserta_notif`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `agenda`
--
ALTER TABLE `agenda`
  ADD CONSTRAINT `agenda_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `agenda_reminders`
--
ALTER TABLE `agenda_reminders`
  ADD CONSTRAINT `agenda_reminders_agenda_id_foreign` FOREIGN KEY (`agenda_id`) REFERENCES `agenda` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `dokumen_agenda`
--
ALTER TABLE `dokumen_agenda`
  ADD CONSTRAINT `dokumen_agenda_ibfk_1` FOREIGN KEY (`agenda_id`) REFERENCES `agenda` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `notifikasi_pendaftar`
--
ALTER TABLE `notifikasi_pendaftar`
  ADD CONSTRAINT `notifikasi_pendaftar_ibfk_1` FOREIGN KEY (`agenda_id`) REFERENCES `agenda` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `peserta_notif`
--
ALTER TABLE `peserta_notif`
  ADD CONSTRAINT `peserta_notif_ibfk_1` FOREIGN KEY (`id_agenda`) REFERENCES `agenda` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
