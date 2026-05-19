# Repository Guidelines

## Project Overview

Agenda eGov — aplikasi agenda kegiatan pemerintahan untuk Dinas Komunikasi dan Informatika Kabupaten Sambas. Dibangun dengan **Laravel 12**, **Blade**, **Tailwind CSS v3**, dan **Alpine.js**.

`native_old/` adalah arsip aplikasi PHP native lama — tidak aktif, hanya referensi.

---

## Project Structure & Module Organization

```
app/Http/Controllers/
    Admin/AgendaController.php      CRUD agenda + upload/hapus dokumen (admin)
    Admin/UserController.php        Manajemen user
    Api/WeatherController.php       Proxy BMKG/Open-Meteo untuk widget cuaca
    DocumentController.php          Serve/download file via route (bukan public/storage)
    PublicAgendaController.php      Halaman publik: list + detail agenda
app/Http/Middleware/
    EnsureUserRole.php              RBAC — alias middleware: role
app/Models/
    Agenda.php                      Accessor: effective_status, badge_class; auto-slug
    AgendaDocument.php              Accessor: url, download_url, type, extension
resources/
    css/app.css                     Tailwind + komponen kustom (badge-*, btn-*, dashboard-*)
    js/app.js                       Sidebar toggle, toast, modal konfirmasi, Lucide init
    views/                          Blade templates (layouts: app.blade.php, guest.blade.php)
```

**Arsitektur penting:**

- **Dokumen** diakses via `/documents/{id}` (`DocumentController`), bukan `public/storage` langsung — solusi untuk APP_URL port mismatch di lokal dan pemblokiran IDM.
- **Status agenda** efektif dihitung dari timestamps di runtime (`effective_status` accessor). Hanya `terjadwal`, `selesai`, `dibatalkan` yang disimpan di DB; `berlangsung` adalah computed state.
- **Role-based access**: kolom `users.role` (`admin` / `user`). Gunakan middleware `role:admin` di routes.
- **Slug** auto-generate dari perihal + tanggal di `Agenda::boot()`.
- Tailwind CSS dikompilasi via Vite — tidak ada CDN runtime. Lucide icons via unpkg CDN (`lucide.createIcons()`).

---

## Build, Test, and Development Commands

### Setup awal

```bash
composer run setup
```

Menjalankan: `composer install`, copy `.env`, key generate, migrate, `npm install`, `npm run build`.

### Development server

```bash
composer run dev
```

Menjalankan `php artisan serve` + `npm run dev` secara bersamaan via `concurrently`.

### Build frontend

```bash
npm run build
```

### Testing

```bash
composer test
# atau
php artisan test

# Single test
php artisan test --filter=AgendaEgovFlowTest
php artisan test tests/Feature/AgendaEgovFlowTest.php
```

Test menggunakan database **MySQL** terpisah: `agenda_egov_test` (lihat `phpunit.xml`).

### Verifikasi routes & cache

```bash
php artisan route:list --no-ansi
php artisan optimize:clear
```

### Windows dev utility

```
manage.bat
  [1] Fresh migrate + clear cache
  [2] Fresh migrate + full reseed
  [3] Clear semua cache
```

---

## Coding Style & Naming Conventions

- **PHP**: 4 spasi indent, LF line endings, UTF-8, final newline (EditorConfig).
- **PHP formatter**: [Laravel Pint](https://laravel.com/docs/pint) — `./vendor/bin/pint`
- **PSR-4** autoloading: `App\` → `app/`, `Tests\` → `tests/`
- **Blade**: komponen kustom di `app/View/Components/`, partials di `resources/views/partials/`.
- Nama file migration mengikuti konvensi Laravel timestamp — jangan diubah manual.

---

## Testing Guidelines

- Framework: **PHPUnit 11.x**
- Suite: `Unit` (`tests/Unit/`) dan `Feature` (`tests/Feature/`)
- DB testing: buat database `agenda_egov_test` di MySQL lokal (bukan SQLite).
- Credentials default seeder untuk testing: `admin@agenda-egov.local` / `password` dan `user@agenda-egov.local` / `password`.

---

## Key Environment Variables

Variabel penting di `.env` (lihat `.env.example`):

```env
DB_CONNECTION=mysql
DB_DATABASE=agenda_egov
ADMIN_NAME=
ADMIN_EMAIL=
ADMIN_PASSWORD=
APP_URL=http://127.0.0.1:8000
```

`APP_URL` harus diset dengan benar karena `DocumentController` menggunakannya untuk generate URL file.

---

## Notification System (Multi-Channel)

Sistem notifikasi pengingat agenda mendukung **WhatsApp** (via Fonnte) dan **Push Notification** (via Firebase Cloud Messaging).

### Arsitektur

```
app/Services/
    FonnteSender.php              Kirim WhatsApp via Fonnte API
    FcmSender.php                 Kirim push notification via FCM
    AgendaReminderService.php     Orchestrator multi-channel
app/Models/
    NotifikasiPendaftar.php       Pendaftar notifikasi (phone/fcm_token)
    FcmToken.php                  FCM device tokens
    AgendaReminder.php            Antrian pengingat (channel: whatsapp/push/both)
app/Console/Commands/
    SendAgendaReminders.php       Artisan command untuk kirim notifikasi
public/
    firebase-messaging-sw.js      Service worker untuk background FCM
resources/js/
    firebase-init.js              Client-side FCM initialization
```

### Flow

1. User subscribe via modal di halaman publik (pilih channel: WA/Push/Gabungan)
2. Data disimpan ke `notifikasi_pendaftar` + `agenda_reminders` + `fcm_tokens` (jika push)
3. Scheduler menjalankan `php artisan agenda:send-reminders` setiap 5 menit
4. `AgendaReminderService` dispatch ke channel yang sesuai

### Konfigurasi Environment

```env
# Fonnte (WhatsApp Gateway)
FONNTE_TOKEN=your-fonnte-api-token
FONNTE_DEVICE=               # Optional, untuk multi-device

# Firebase Cloud Messaging (Server)
FIREBASE_PROJECT_ID=your-project-id
FIREBASE_CREDENTIALS_PATH=storage/app/firebase-credentials.json

# Firebase (Client - untuk frontend)
VITE_FIREBASE_API_KEY=your-api-key
VITE_FIREBASE_AUTH_DOMAIN=your-project.firebaseapp.com
VITE_FIREBASE_PROJECT_ID=your-project-id
VITE_FIREBASE_STORAGE_BUCKET=your-project.appspot.com
VITE_FIREBASE_MESSAGING_SENDER_ID=123456789
VITE_FIREBASE_APP_ID=1:123456789:web:abcdef
VITE_FIREBASE_VAPID_KEY=your-vapid-key
```

> **Note**: Untuk FCM, download service account JSON dari Firebase Console dan simpan di `storage/app/firebase-credentials.json`

### Commands

```bash
# Kirim notifikasi pending
php artisan agenda:send-reminders

# Scheduler (di cron production)
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

### Admin Panel - Test Notifikasi

Route: `/admin/notifications/test` (admin only)

Fitur:
- Test kirim WhatsApp ke nomor tertentu
- Test kirim push notification dengan FCM token
- Broadcast ke FCM topic `agenda-updates`
- Status konfigurasi Fonnte dan FCM

---

## Railway Deployment

### File Konfigurasi Docker

```
Dockerfile              Multi-stage build (PHP 8.2 + Nginx + Supervisor)
docker/
    nginx.conf          Nginx config untuk Laravel
    supervisord.conf    Process manager (PHP-FPM + Nginx + Scheduler)
    php.ini             PHP production settings (opcache, upload limits)
    entrypoint.sh       Container startup (migrate, cache, optimize)
railway.json            Railway deployment config
.dockerignore           Exclude files from Docker build
```

### Deploy ke Railway

1. Push ke GitHub repository
2. Connect repository di Railway dashboard
3. Set environment variables:

```env
APP_NAME="Agenda eGov"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app.up.railway.app

DB_CONNECTION=mysql
DB_HOST=<railway-mysql-host>
DB_PORT=3306
DB_DATABASE=<db-name>
DB_USERNAME=<db-user>
DB_PASSWORD=<db-pass>

FONNTE_TOKEN=<your-fonnte-token>
FIREBASE_PROJECT_ID=<your-firebase-project>
```

4. Railway akan auto-deploy dari Dockerfile
