# Changelog

Semua perubahan penting pada Agenda eGov didokumentasikan di file ini.

Format mengikuti prinsip [Keep a Changelog](https://keepachangelog.com/id-ID/1.0.0/).

---

## [3.2.0] - 2026-05-08

### Added
- **Blob-based document serving** — `DocumentController` melayani file via route `/documents/{id}` dan `/documents/{id}/download`, menghindari masalah APP_URL port mismatch dan pemblokiran IDM.
- **IDM-proof document embed** — PDF dan gambar dimuat via JavaScript `fetch → blob URL` sebelum diset sebagai `iframe.src` atau `img.src`. Download menggunakan `fetch → blob → <a download>` invisible.
- **Custom delete document modal** — modal konfirmasi hapus dokumen dengan nama file ditampilkan, tombol Batal/Hapus, loading state, dan AJAX `fetch` + `FormData(_method=DELETE)`. Menggantikan nested form (invalid HTML) dan `confirm()` browser.
- `AgendaDocument::getUrlAttribute()` menggunakan `route('documents.show', $this)`.
- `AgendaDocument::getDownloadUrlAttribute()` menggunakan `route('documents.download', $this)`.
- `DocumentController::show()` — serve file inline dengan MIME type yang benar.
- `DocumentController::download()` — force-download file.
- Route `documents.show` dan `documents.download` di `web.php`.

### Changed
- `destroyDocument` controller method kini mengembalikan `JsonResponse` untuk request AJAX dan `RedirectResponse` untuk form biasa.
- Tombol Unduh di halaman detail (public dan admin) dari `<a href>` menjadi `<button onclick="docDownload()">`.

### Fixed
- **SQL ENUM error `berlangsung`** — status `berlangsung` dihapus dari dropdown form dan validasi `StoreAgendaRequest`. DB ENUM hanya menerima `terjadwal`, `selesai`, `dibatalkan`. `berlangsung` adalah computed value dari `getEffectiveStatusAttribute()`.
- **Fatal bug: hapus dokumen malah menghapus agenda** — nested `<form>` di dalam `<form>` adalah invalid HTML. Browser membuang inner form tags dan mencampur field `_method=DELETE` ke outer form, menyebabkan agenda ikut terhapus. Solusi: hapus nested form, gunakan AJAX fetch.

---

## [3.1.0] - 2026-05-08

### Added
- **Split-panel auth UI** — Login, Register, Forgot Password didesain ulang dengan layout dua kolom: panel kiri branding (judul, deskripsi, fitur key, link publik) + panel kanan form. Responsif untuk mobile (panel kiri tersembunyi di layar kecil).
- **Password visibility toggle** — ikon mata pada semua field password di auth.
- **Inline error message** — pesan error autentikasi tampil di bawah field password, bukan di card tersendiri.
- **Remember device checkbox** — tersedia di halaman login.
- **Weather widget lanjutan** — menampilkan kelembapan dan kecepatan angin di samping suhu dan kondisi.
- **Live debounce search** — pencarian agenda berjalan otomatis 400ms setelah user berhenti mengetik, tanpa perlu tekan Enter.
- **Status badge berwarna di tabel** — pill badge dengan warna sesuai status (amber=terjadwal, blue=berlangsung, emerald=selesai, rose=dibatalkan) dari `Agenda::getStatusBadgeClassAttribute()`. Model ditambahkan ke Tailwind content scan agar class tidak di-purge.
- **Stat cards berwarna** — card Total (biru), Terjadwal (amber), Selesai (hijau), Dibatalkan (merah) di dashboard public dan admin.
- **Panel Admin button** — tombol akses panel admin dipindah ke dalam panel header (kanan), bukan di header utama halaman.
- **Visible table row dividers** — `divide-slate-200` menggantikan `divide-slate-100` di semua tabel untuk garis yang lebih terlihat.
- **Desktop-aware detail agenda** — halaman detail `/agenda/{slug}` menggunakan layout 2-kolom di desktop: konten utama kiri, sidebar info + dokumen + agenda lain kanan.
- **Footer Diskominfo Sambas** — footer publik berisi informasi resmi Diskominfo Kabupaten Sambas (alamat: Jl. Gusti Hamzah No.5, Sambas; telepon; website; link Situs Utama / Beranda / Login Admin).
- **Sidebar grup menu admin** — sidebar dibagi menjadi "Menu Utama" (Agenda) dan "Menu Lainnya" (Pengguna, Profil) dengan garis pemisah.
- **Card profil user di sidebar** — nama, email, role ditampilkan di bagian bawah kiri sidebar. Klik untuk toggle tombol Beranda dan Logout.
- **Hamburger sidebar mobile** — sidebar admin menjadi overlay slide-in di layar kecil, dilengkapi tombol X dan backdrop.
- **Print agenda** — halaman cetak `/admin/agendas/print` untuk laporan agenda dengan filter status/bulan/tahun.
- **Multi-file upload dokumen** — upload beberapa file sekaligus di form create/edit agenda.
- **Embed dokumen di detail** — tampilan PDF (`<iframe>`) dan gambar (`<img>`) langsung di halaman detail admin dan publik.
- **manage.bat** — utility Windows untuk: [1] Clear DB & Cache (Fresh Migrate), [2] Reset DB + Full Reseed, [3] Clear All Caches Only.
- **AgendaSeeder** — data demo agenda dengan berbagai status.
- **UserSeeder** — akun admin dan user demo.
- **Slug routing** — agenda diakses via slug (`/agenda/{slug}`), auto-generate dari perihal + tanggal.
- `tailwind.config.js` content scan mencakup `app/Models/**/*.php` untuk class dinamis dari PHP.

### Changed
- **Role `operator` dihapus** — sistem sekarang hanya punya `user` dan `admin`. Semua fungsionalitas operator digabung ke admin.
- **Admin dashboard = daftar agenda** — `/admin` langsung menampilkan list agenda (bukan halaman kosong atau redirect).
- **Route admin prefix** — semua route admin memakai prefix `/admin` dan name prefix `admin.` tanpa route operator terpisah.
- **Menu Laporan dihapus** — fitur laporan terpisah dihapus; cetak langsung dari halaman admin agendas via `/admin/agendas/print`.
- **Public `/agenda` memakai UI yang sama dengan admin** — komponen CSS `dashboard-*` digunakan di kedua halaman untuk konsistensi visual.
- **Teks error login** — pesan "auth.failed" dihapus dari card tersendiri, diganti pesan singkat inline di bawah field password.
- **Font global** — Figtree di admin layout, Outfit di public layout (Google Fonts).
- **Lucide icons** — menggantikan semua emoji dan ikon lain secara konsisten.
- **Hapus gradient dan glassmorphism** — tampilan lebih clean dan flat sesuai permintaan.
- Status `berlangsung` hanya sebagai computed value, tidak tersimpan di DB dan tidak ada di dropdown form.

### Removed
- Role `operator` dan semua route/view terkait.
- Modul redeem code (route, controller, model, view, migration).
- Modul laporan admin (`/admin/reports`).
- `DashboardController` user (user hanya punya profil).
- Teks "Agenda eGov" dari footer publik — diganti informasi Diskominfo Sambas.
- Gradient dan glassmorphism dari UI.
- Opsi `berlangsung` dari dropdown status di form agenda.

---

## [3.0.0] - 2026-05-08

### Added
- Rebuild penuh aplikasi ke **Laravel 12** dari PHP native.
- Autentikasi berbasis Laravel Breeze.
- Role-based access control dengan kolom `users.role`.
- Middleware `role` (`App\Http\Middleware\EnsureUserRole`).
- CRUD agenda, upload/download dokumen, laporan admin.
- Seeder awal admin.
- Dokumentasi deployment, routing, fitur.

### Changed
- Root project menjadi aplikasi Laravel 12.
- Aplikasi PHP native dipindahkan ke `native_old/`.
- Asset frontend memakai Vite build.
- Database schema via Laravel migrations.

### Removed
- Entrypoint PHP native (`index.php`, `detail_agenda.php`, `admin/*.php`, `api/*.php`).

---

## [2.1.0] - 2025-12-19

Versi PHP native lama. Detail di `native_old/CHANGELOG.md`.

## [2.0.0] - 2025-12-18

Versi PHP native lama dengan restrukturisasi MVC-like. Detail di `native_old/CHANGELOG.md`.

## [1.0.0] - 2025-12-01

Versi awal PHP native lama. Detail di `native_old/CHANGELOG.md`.
