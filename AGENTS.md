# Repository Guidelines

## Project Overview

**Agenda eGov** — aplikasi manajemen agenda kegiatan pemerintahan untuk Dinas Komunikasi dan Informatika Kabupaten Sambas.

- **Framework**: Laravel 12, Blade, Tailwind CSS v3, Alpine.js
- **Database**: MySQL 8.0+ / MariaDB 10.4+
- **Notifications**: WhatsApp (Fonnte), Push (Firebase Cloud Messaging)
- **Deployment**: Docker, Railway

> `native_old/` adalah arsip aplikasi PHP native lama — tidak aktif, hanya referensi.

---

## Project Structure

```
app/
  Console/Commands/
    SendAgendaReminders.php         # Artisan command kirim notifikasi
  Http/Controllers/
    Admin/
      AgendaController.php          # CRUD agenda + upload/hapus dokumen
      NotificationTestController.php # Panel test WA & FCM
      UserController.php            # Manajemen user
    Api/WeatherController.php       # Proxy BMKG/Open-Meteo
    DocumentController.php          # Serve/download file via route
    NotificationController.php      # Subscribe notifikasi + FCM token
    PublicAgendaController.php      # Halaman publik
  Http/Middleware/
    EnsureUserRole.php              # RBAC — alias middleware: role
  Models/
    Agenda.php                      # Accessor: effective_status, badge_class; auto-slug
    AgendaDocument.php              # Accessor: url, download_url, type, extension
    AgendaReminder.php              # Antrian pengingat (channel: whatsapp/fcm)
    FcmToken.php                    # FCM device tokens
    NotifikasiPendaftar.php         # Pendaftar notifikasi publik
  Services/
    AgendaReminderService.php       # Orchestrator multi-channel
    FcmSender.php                   # Firebase Cloud Messaging sender
    FonnteSender.php                # WhatsApp sender via Fonnte API
resources/
  css/app.css                       # Tailwind + komponen kustom
  js/app.js                         # Sidebar toggle, toast, modal, Lucide init
  js/firebase-init.js               # Client-side FCM initialization
  views/
    admin/                          # Admin views (agendas, users, notifications)
    agenda/                         # Public agenda (index, show)
    auth/                           # Login, register, forgot-password
    layouts/                        # app.blade.php, guest.blade.php
    partials/                       # sidebar, toast, public-footer
```

---

## Architecture Notes

### Dokumen Access
- Dokumen diakses via `/documents/{id}` (`DocumentController`), bukan `public/storage` langsung
- Solusi untuk APP_URL port mismatch di lokal dan pemblokiran IDM
- Download via fetch → Blob → `<a download>`

### Status Agenda
- Status tersimpan di DB: `terjadwal`, `selesai`, `dibatalkan`
- Status `berlangsung` adalah computed state (tidak disimpan)
- Dihitung dari timestamps di runtime via `effective_status` accessor

### Role-Based Access
- Kolom `users.role`: `admin` atau `user`
- Middleware: `role:admin` di routes admin
- Alias di `bootstrap/app.php`

### Slug Auto-Generate
- Slug dibuat otomatis dari perihal + tanggal di `Agenda::boot()`
- Digunakan sebagai route key (`getRouteKeyName()`)

### Frontend Build
- Tailwind CSS dikompilasi via Vite — tidak ada CDN runtime
- Lucide icons via unpkg CDN (`lucide.createIcons()`)

---

## Build & Development Commands

### Quick Setup

```bash
composer run setup
```

Menjalankan: `composer install`, copy `.env`, key generate, migrate, `npm install`, `npm run build`.

### Development Server

```bash
composer run dev
```

Menjalankan `php artisan serve` + `npm run dev` secara bersamaan.

### Build Frontend

```bash
npm run build
```

### Testing

```bash
# All tests
composer test
# atau
php artisan test

# Single test
php artisan test --filter=AgendaEgovFlowTest
php artisan test tests/Feature/AgendaEgovFlowTest.php
```

Test menggunakan database MySQL terpisah: `agenda_egov_test` (lihat `phpunit.xml`).

### Verifikasi

```bash
php artisan route:list --no-ansi
php artisan optimize:clear
```

### Windows Dev Utility

```
manage.bat
  [1] Fresh migrate + clear cache
  [2] Fresh migrate + full reseed
  [3] Clear semua cache
```

---

## Coding Style

- **PHP**: 4 spasi indent, LF line endings, UTF-8, final newline (EditorConfig)
- **Formatter**: Laravel Pint — `./vendor/bin/pint`
- **PSR-4**: `App\` → `app/`, `Tests\` → `tests/`
- **Blade**: komponen di `app/View/Components/`, partials di `views/partials/`
- **Migration**: jangan rename file migration secara manual

---

## Key Environment Variables

```env
# App
APP_NAME="Agenda eGov Sambas"
APP_URL=http://127.0.0.1:8000
APP_DEBUG=false

# Database
DB_CONNECTION=mysql
DB_DATABASE=agenda_egov

# Admin Seeder
ADMIN_NAME="Administrator"
ADMIN_EMAIL=admin@agenda-egov.local
ADMIN_PASSWORD=password

# WhatsApp (Fonnte)
FONNTE_TOKEN=your-fonnte-api-token
FONNTE_DEVICE=               # Optional

# Firebase Cloud Messaging
FIREBASE_PROJECT_ID=your-project-id
FIREBASE_CREDENTIALS_PATH=storage/app/firebase-credentials.json

# Firebase Client (Frontend)
VITE_FIREBASE_API_KEY=
VITE_FIREBASE_AUTH_DOMAIN=
VITE_FIREBASE_PROJECT_ID=
VITE_FIREBASE_STORAGE_BUCKET=
VITE_FIREBASE_MESSAGING_SENDER_ID=
VITE_FIREBASE_APP_ID=
VITE_FIREBASE_VAPID_KEY=
```

> `APP_URL` harus diset dengan benar — `DocumentController` menggunakannya untuk generate URL file.

---

## Notification System

### Architecture

```
Services/
  FonnteSender.php              # Kirim WhatsApp via Fonnte API
  FcmSender.php                 # Kirim push notification via FCM
  AgendaReminderService.php     # Orchestrator multi-channel

Models/
  NotifikasiPendaftar.php       # Pendaftar (phone/fcm_token, channel_preference)
  FcmToken.php                  # FCM device tokens
  AgendaReminder.php            # Antrian pengingat (channel: whatsapp/fcm)

Commands/
  SendAgendaReminders.php       # php artisan agenda:send-reminders
```

### Flow

1. User subscribe via modal di halaman publik (pilih channel: WA/Push/Gabungan)
2. Data disimpan ke `notifikasi_pendaftar` + `agenda_reminders` + `fcm_tokens`
3. Scheduler menjalankan `php artisan agenda:send-reminders` setiap 5 menit
4. `AgendaReminderService` dispatch ke channel yang sesuai

### Commands

```bash
# Kirim notifikasi pending
php artisan agenda:send-reminders

# Scheduler (cron production)
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

### Admin Test Panel

Route: `/admin/notifications/test` (admin only)

Fitur:
- Test kirim WhatsApp ke nomor tertentu
- Test kirim push notification dengan FCM token
- Broadcast ke FCM topic `agenda-updates`
- Status konfigurasi Fonnte dan FCM

---

## Database Schema

### Tables

| Table | Keterangan |
|-------|------------|
| `users` | User accounts dengan role |
| `agenda` | Data agenda utama |
| `dokumen_agenda` | Dokumen/attachment agenda |
| `agenda_reminders` | Antrian pengingat multi-channel |
| `fcm_tokens` | FCM device tokens |
| `notifikasi_pendaftar` | Pendaftar notifikasi publik |
| `sessions` | Laravel sessions |
| `cache` | Laravel cache |
| `jobs` | Laravel queue jobs |

### Key Columns

**agenda**:
- `jenis_agenda`: enum (`internal`, `eksternal`)
- `status`: enum (`terjadwal`, `selesai`, `dibatalkan`)
- `slug`: unique, auto-generated
- `waktu_mulai`, `waktu_selesai`: datetime

**agenda_reminders**:
- `channel`: enum (`whatsapp`, `fcm`)
- `is_sent`: boolean
- `phone_number`: untuk WhatsApp

**notifikasi_pendaftar**:
- `channel_preference`: enum (`whatsapp`, `fcm`, `both`)
- `fcm_token_id`: FK ke fcm_tokens

---

## Railway Deployment

### Docker Files

```
Dockerfile              # Multi-stage build (PHP 8.2 + Nginx + Supervisor)
docker/
  nginx.conf            # Nginx config untuk Laravel
  supervisord.conf      # Process manager (PHP-FPM + Nginx + Scheduler)
  php.ini               # PHP production settings
  www.conf              # PHP-FPM pool config
  entrypoint.sh         # Container startup (migrate, cache, optimize)
railway.json            # Railway deployment config
.dockerignore           # Exclude files from Docker build
```

### Deploy Steps

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

4. Railway auto-deploy dari Dockerfile

---

## Testing Guidelines

- **Framework**: PHPUnit 11.x
- **Suite**: `Unit` (`tests/Unit/`) dan `Feature` (`tests/Feature/`)
- **DB Testing**: buat database `agenda_egov_test` di MySQL lokal
- **Default Credentials**: `admin@agenda-egov.local` / `password`

---

## Common Tasks

### Fresh Database Reset

```bash
php artisan migrate:fresh --seed
```

### Clear All Caches

```bash
php artisan optimize:clear
```

### Add New Migration

```bash
php artisan make:migration add_column_to_table
```

### Format Code

```bash
./vendor/bin/pint
```

### Check Routes

```bash
php artisan route:list --no-ansi
```

---

## Troubleshooting

### Dokumen tidak bisa diakses
- Pastikan `php artisan storage:link` sudah dijalankan
- Cek permission folder `storage/app/public`

### Notifikasi tidak terkirim
- Cek `FONNTE_TOKEN` dan `FIREBASE_PROJECT_ID` di `.env`
- Pastikan scheduler berjalan: `php artisan schedule:run`
- Cek logs di `storage/logs/laravel.log`

### FCM tidak bekerja
- Download service account JSON dari Firebase Console
- Simpan di `storage/app/firebase-credentials.json`
- Set `FIREBASE_CREDENTIALS_PATH` di `.env`
