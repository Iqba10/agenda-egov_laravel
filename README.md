# Agenda eGov — Diskominfo Kabupaten Sambas

Aplikasi agenda kegiatan pemerintahan berbasis **Laravel 12**, **Blade**, dan **Tailwind CSS** untuk Dinas Komunikasi dan Informatika Kabupaten Sambas.

Aplikasi PHP native lama tersimpan di `native_old/` sebagai arsip referensi.

---

## Fitur Utama

### Public Site
- Daftar agenda publik di `/` dengan filter status, live-search debounce, dan stat cards berwarna.
- Detail agenda di `/agenda/{slug}` — layout 2-kolom desktop-aware.
- Embed dokumen (PDF iframe, image preview) via blob URL, anti-IDM.
- Footer Diskominfo Sambas dengan informasi kontak resmi.
- Widget cuaca real-time (suhu, kondisi, kelembapan, kecepatan angin) dari BMKG/Open-Meteo.
- Jam digital live.

### Sistem Notifikasi Multi-Channel
- **WhatsApp** (via Fonnte API) — notifikasi langsung ke nomor HP.
- **Push Notification** (via Firebase Cloud Messaging) — notifikasi browser.
- **Gabungan** — kirim ke kedua channel sekaligus.
- Pengingat dikirim 3x: saat subscribe, 24 jam sebelum, dan 6 jam sebelum agenda.
- Modal subscribe modern dengan pilihan channel dan validasi real-time.
- Scheduler otomatis via `php artisan agenda:send-reminders`.

### Autentikasi
- Login, Register, Forgot Password dengan UI split-panel responsif.
- Remember device toggle.
- Password visibility toggle.
- Pesan error inline di bawah field password.
- Reset password via email (Laravel Breeze).

### Role-Based Access Control

| Role | Akses |
|------|-------|
| `user` | Profil, public site |
| `admin` | Semua + CRUD agenda, kelola dokumen, manajemen user |

Role disimpan di kolom `users.role`. Middleware: `App\Http\Middleware\EnsureUserRole` (alias `role`).

### Agenda
- CRUD lengkap oleh admin.
- Field: jenis, perihal, waktu mulai/selesai, tempat, asal surat, tanggal surat, pakaian, disposisi, petugas, status, keterangan.
- Status tersimpan: `terjadwal`, `selesai`, `dibatalkan`.
- Status efektif dihitung otomatis dari waktu:
  - Sebelum waktu mulai → `terjadwal`
  - Antara mulai dan selesai → `berlangsung` *(computed, tidak disimpan)*
  - Setelah waktu selesai → `selesai`
  - Jika manual `dibatalkan` → tetap `dibatalkan`
- Routing agenda via `slug` (auto-generate dari perihal + tanggal).
- Badge status berwarna di tabel dan detail.

### Dokumen Agenda
- Upload multi-file: PDF, JPG, JPEG, PNG, DOCX, XLSX (maks. 5 MB/file).
- File disimpan di `storage/app/public/agendas/documents/`.
- Akses file via `DocumentController` (bypass APP_URL port mismatch).
- Embed inline: PDF → `<iframe>` via Blob URL; Gambar → `<img>` via Blob URL.
- Download via fetch → Blob → `<a download>` (anti-IDM, tidak ada request HTTP langsung).
- Hapus dokumen via AJAX dengan modal konfirmasi custom (tanpa nested form).

### Admin Panel
- Dashboard `/admin` — list agenda + stat cards + live search + filter status.
- CRUD agenda di `/admin/agendas/*`.
- Manajemen user di `/admin/users`.
- Sidebar dengan grup menu (Menu Utama / Menu Lainnya) dan card profil user di bawah.
- Hamburger sidebar mobile-friendly.
- Print agenda via halaman cetak khusus `/admin/agendas/print`.

### UI/UX
- Font: **Figtree** (admin layout) / **Outfit** (public layout) — Google Fonts.
- Icons: **Lucide** (CDN, `lucide.createIcons()`).
- Tailwind CSS v3 via Vite build — tidak ada runtime CDN.
- Toast notification dengan animasi.
- Modal konfirmasi custom (hapus agenda, hapus dokumen).
- Status badge pill berwarna sesuai status.
- Responsive untuk mobile (hamburger sidebar, filter bar wrap).

---

## Stack Teknis

| Layer | Teknologi |
|-------|-----------|
| Backend | PHP `^8.2`, Laravel `12.x` |
| Database | MySQL / MariaDB |
| Frontend | Blade, Tailwind CSS v3, Vite |
| Auth | Laravel Breeze foundation |
| Icons | Lucide (unpkg CDN) |
| Font | Google Fonts (Figtree, Outfit) |
| Dev tool | `manage.bat` (Windows) |

---

## Struktur Folder Penting

```
app/
  Console/Commands/
    SendAgendaReminders.php        Artisan command kirim notifikasi
  Http/Controllers/
    Admin/AgendaController.php     CRUD agenda + dokumen admin
    Admin/UserController.php       Manajemen user
    Api/WeatherController.php      Weather API proxy
    Auth/                          Breeze auth controllers
    DocumentController.php         Serve/download dokumen via route
    NotificationController.php     Subscribe notifikasi + FCM token
    ProfileController.php          Profile edit/delete
    PublicAgendaController.php     Public agenda list + detail
  Http/Middleware/
    EnsureUserRole.php             RBAC middleware
  Http/Requests/
    StoreAgendaRequest.php
    UpdateAgendaRequest.php
  Models/
    Agenda.php                     Slug, effective_status, badge class
    AgendaDocument.php             url, download_url, type, extension attributes
    AgendaReminder.php             Antrian pengingat multi-channel
    FcmToken.php                   FCM device tokens
    NotifikasiPendaftar.php        Pendaftar notifikasi
    User.php
  Services/
    AgendaReminderService.php      Orchestrator notifikasi multi-channel
    FcmSender.php                  Firebase Cloud Messaging sender
    FonnteSender.php               WhatsApp sender via Fonnte API

database/
  migrations/
    0001_01_01_000000_create_users_table.php
    2026_05_08_100000_create_agenda_table.php
    2026_05_08_100241_add_slug_to_agendas_table.php
    2026_05_08_122620_create_dokumen_agenda_table.php
  seeders/
    DatabaseSeeder.php
    UserSeeder.php
    AgendaSeeder.php

docs/
  DEPLOYMENT.md
  API_ROUTING_FEATURES.md

native_old/
  Arsip aplikasi PHP native lama

public/
  firebase-messaging-sw.js   Service worker untuk FCM background

resources/
  css/app.css        Tailwind + komponen kustom (dashboard-*, badge-*, btn-*)
  js/app.js          Sidebar toggle, toast, data-confirm modal, Lucide init
  js/firebase-init.js   Client-side FCM initialization
  views/
    agenda/           Public agenda (index, show)
    admin/agendas/    Admin CRUD (index, show, create, edit, _form, print)
    admin/            Dashboard admin, user management
    auth/             Login, register, forgot-password, reset-password
    layouts/          app.blade.php, guest.blade.php
    partials/         sidebar, toast, public-footer
    profile/          Edit profil

routes/
  web.php            Semua route web
  auth.php           Route auth Breeze

manage.bat           Dev utility (Windows): migrate fresh, reseed, clear cache
```

---

## Setup Lokal

### 1. Install dependency PHP

```bash
composer install
```

### 2. Install dependency frontend

```bash
npm install
```

### 3. Buat `.env`

```bash
cp .env.example .env
```

Sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=agenda_egov
DB_USERNAME=root
DB_PASSWORD=

ADMIN_NAME="Administrator"
ADMIN_EMAIL=admin@agenda-egov.local
ADMIN_PASSWORD=password

# Notifikasi (opsional)
FONNTE_TOKEN=your-fonnte-api-token
FIREBASE_PROJECT_ID=your-project-id
FIREBASE_CREDENTIALS_PATH=storage/app/firebase-credentials.json
```

### 4. Generate app key

```bash
php artisan key:generate
```

### 5. Migrasi database dan seed

```bash
php artisan migrate --force
php artisan db:seed --force
```

Seeder membuat dua akun default:

```
admin@agenda-egov.local  / password  (role: admin)
user@agenda-egov.local   / password  (role: user)
```

**Ganti password sebelum production.**

### 6. Link storage publik

```bash
php artisan storage:link
```

### 7. Build asset

```bash
npm run build
```

### 8. Jalankan server lokal

```bash
php artisan serve
```

Buka: `http://127.0.0.1:8000`

### 9. Jalankan scheduler (untuk notifikasi)

```bash
# Development (jalankan manual)
php artisan agenda:send-reminders

# Production (cron job)
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

### Atau gunakan manage.bat (Windows)

```
manage.bat
> [1] Clear DB & Cache (Fresh Migrate)
> [2] Reset DB + Full Reseed
> [3] Clear All Caches Only
```

---

## Command Verifikasi

```bash
php artisan route:list --no-ansi
php artisan view:cache --no-ansi
npm run build
```

---

## Dokumentasi Lanjutan

- Deployment: [`docs/DEPLOYMENT.md`](docs/DEPLOYMENT.md)
- Routes, API, fitur, DB schema: [`docs/API_ROUTING_FEATURES.md`](docs/API_ROUTING_FEATURES.md)
- Changelog: [`CHANGELOG.md`](CHANGELOG.md)
- Changelog native lama: [`native_old/CHANGELOG.md`](native_old/CHANGELOG.md)

---

## Catatan Penting

- Aplikasi lama berada di `native_old/` — hanya arsip, tidak aktif.
- Root project adalah Laravel 12; tidak ada backward compatibility ke entrypoint PHP native lama.
- File dokumen diakses via route `/documents/{id}` bukan `public/storage` langsung — ini mengatasi masalah APP_URL port mismatch di lokal dan pemblokiran IDM.
- `manage.bat` hanya untuk Windows dev environment.
