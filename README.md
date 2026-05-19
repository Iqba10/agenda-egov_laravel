# Agenda eGov — Diskominfo Kabupaten Sambas

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v3-06B6D4?logo=tailwindcss)
![License](https://img.shields.io/badge/License-MIT-green)

Aplikasi manajemen agenda kegiatan pemerintahan berbasis **Laravel 12**, **Blade**, dan **Tailwind CSS** untuk Dinas Komunikasi dan Informatika Kabupaten Sambas.

  
---

## Fitur Utama

### Public Site
- Daftar agenda publik dengan filter status, live-search debounce, dan statistik cards
- Detail agenda dengan layout 2-kolom responsive
- Embed dokumen (PDF iframe, image preview) via blob URL — anti-IDM
- Widget cuaca real-time (suhu, kondisi, kelembapan, angin) dari Open-Meteo
- Jam digital live
- Footer Diskominfo Sambas dengan kontak resmi

### Sistem Notifikasi Multi-Channel
- **WhatsApp** via Fonnte API — notifikasi langsung ke nomor HP
- **Push Notification** via Firebase Cloud Messaging (FCM) — notifikasi browser
- **Gabungan** — kirim ke kedua channel sekaligus
- Pengingat otomatis: saat subscribe, 24 jam sebelum, dan 6 jam sebelum agenda
- Modal subscribe modern dengan validasi real-time
- Scheduler: `php artisan agenda:send-reminders`

### Admin Panel
- Dashboard dengan statistik dan filter agenda
- CRUD lengkap agenda + upload dokumen (PDF, gambar, Office)
- Manajemen user dengan role assignment
- Halaman cetak laporan agenda
- **Test Notifikasi** — panel untuk testing WhatsApp dan FCM

### Autentikasi & RBAC
- Login, Register, Forgot Password dengan UI split-panel responsive
- Role-Based Access Control: `admin` dan `user`
- Middleware: `role:admin` untuk proteksi area admin

### Dokumen Agenda
- Upload multi-file: PDF, JPG, JPEG, PNG, DOCX, XLSX (max 5 MB/file)
- Akses via route Laravel (bypass APP_URL mismatch)
- Download anti-IDM via fetch → blob

---

## Tech Stack

| Layer | Teknologi |
|-------|-----------|
| Backend | PHP 8.2+, Laravel 12.x |
| Database | MySQL 8.0+ / MariaDB 10.4+ |
| Frontend | Blade, Tailwind CSS v3, Vite |
| Auth | Laravel Breeze |
| Icons | Lucide (CDN) |
| Fonts | Google Fonts (Figtree, Outfit) |
| Notifications | Fonnte (WhatsApp), Firebase Cloud Messaging |
| Deployment | Docker, Railway |

---

## Quick Start

### Prerequisites

- PHP 8.2+
- Composer 2.x
- Node.js 18+ & npm
- MySQL 8.0+ / MariaDB 10.4+

### Installation

```bash
# 1. Clone repository
git clone https://github.com/Iqba10/agenda-egov_laravel.git
cd agenda-egov_laravel

# 2. Install dependencies
composer install
npm install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Configure .env
# Edit DB_DATABASE, DB_USERNAME, DB_PASSWORD sesuai environment

# 5. Database
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link

# 6. Build frontend
npm run build

# 7. Run server
php artisan serve
```

Buka: `http://127.0.0.1:8000`

### Default Accounts

| Email | Password | Role |
|-------|----------|------|
| `admin@agenda-egov.local` | `password` | admin |
| `user@agenda-egov.local` | `password` | user |

> **Penting**: Ganti password default sebelum production!

---

## Development

### One-Command Setup

```bash
composer run setup
```

### Development Server (Hot Reload)

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
# Run all tests
composer test

# Single test
php artisan test --filter=AgendaEgovFlowTest
```

### Windows Dev Utility

```
manage.bat
  [1] Fresh migrate + clear cache
  [2] Fresh migrate + full reseed
  [3] Clear all caches
```

---

## Configuration

### Environment Variables

```env
# App
APP_NAME="Agenda eGov Sambas"
APP_URL=http://127.0.0.1:8000
APP_DEBUG=false

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=agenda_egov
DB_USERNAME=root
DB_PASSWORD=

# Admin Seeder
ADMIN_NAME="Administrator"
ADMIN_EMAIL=admin@agenda-egov.local
ADMIN_PASSWORD=password

# WhatsApp (Fonnte)
FONNTE_TOKEN=your-fonnte-api-token
FONNTE_DEVICE=               # Optional, untuk multi-device

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

### Notification Setup

#### WhatsApp (Fonnte)
1. Daftar di [fonnte.com](https://fonnte.com)
2. Dapatkan API token
3. Set `FONNTE_TOKEN` di `.env`

#### Firebase Cloud Messaging
1. Buat project di [Firebase Console](https://console.firebase.google.com)
2. Enable Cloud Messaging
3. Download service account JSON → simpan di `storage/app/firebase-credentials.json`
4. Set `FIREBASE_PROJECT_ID` dan `FIREBASE_CREDENTIALS_PATH`
5. Untuk frontend push: isi semua `VITE_FIREBASE_*` variables

### Scheduler (Production)

Tambahkan cron job:

```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

---

## Deployment

### Railway (Recommended)

1. Push ke GitHub
2. Connect repository di [Railway Dashboard](https://railway.app)
3. Set environment variables
4. Railway auto-deploy dari `Dockerfile`

Environment variables untuk Railway:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app.up.railway.app

DB_CONNECTION=mysql
DB_HOST=<railway-mysql-host>
DB_PORT=3306
DB_DATABASE=<db-name>
DB_USERNAME=<db-user>
DB_PASSWORD=<db-pass>

FONNTE_TOKEN=<your-token>
FIREBASE_PROJECT_ID=<your-project>
```

### Docker

```bash
# Build
docker build -t agenda-egov .

# Run
docker run -p 8080:80 \
  -e APP_KEY=base64:... \
  -e DB_HOST=host.docker.internal \
  agenda-egov
```

### Manual Deployment

Lihat [docs/DEPLOYMENT.md](docs/DEPLOYMENT.md) untuk panduan lengkap.

---

## Project Structure

```
app/
  Console/Commands/
    SendAgendaReminders.php        # Artisan command notifikasi
  Http/Controllers/
    Admin/
      AgendaController.php         # CRUD agenda + dokumen
      NotificationTestController.php # Test WA & FCM
      UserController.php           # Manajemen user
    Api/WeatherController.php      # Weather proxy
    DocumentController.php         # Serve/download dokumen
    NotificationController.php     # Subscribe notifikasi
    PublicAgendaController.php     # Public agenda pages
  Models/
    Agenda.php                     # Accessor: effective_status, slug
    AgendaDocument.php             # Accessor: url, download_url, type
    AgendaReminder.php             # Antrian pengingat
    FcmToken.php                   # FCM device tokens
    NotifikasiPendaftar.php        # Pendaftar notifikasi
  Services/
    AgendaReminderService.php      # Orchestrator multi-channel
    FcmSender.php                  # Firebase sender
    FonnteSender.php               # WhatsApp sender

database/migrations/
  2026_05_08_100000_create_agenda_table.php
  2026_05_08_100241_add_slug_to_agendas_table.php
  2026_05_08_122620_create_dokumen_agenda_table.php
  2026_05_20_000001_refactor_notifications_multichannel.php

docker/
  entrypoint.sh                    # Container startup
  nginx.conf                       # Nginx config
  php.ini                          # PHP production settings
  supervisord.conf                 # Process manager
  www.conf                         # PHP-FPM config

docs/
  DEPLOYMENT.md                    # Deployment guide
  API_ROUTING_FEATURES.md          # Routes & features detail

public/
  firebase-messaging-sw.js         # FCM service worker

resources/
  css/app.css                      # Tailwind + custom components
  js/app.js                        # Sidebar, toast, modal, Lucide
  js/firebase-init.js              # FCM client initialization
  views/
    admin/                         # Admin views
    agenda/                        # Public agenda views
    auth/                          # Auth views
    layouts/                       # app.blade.php, guest.blade.php
    partials/                      # sidebar, toast, footer
```

---

## Documentation

- [AGENTS.md](AGENTS.md) — Repository guidelines untuk AI/developer
- [docs/DEPLOYMENT.md](docs/DEPLOYMENT.md) — Panduan deployment lengkap
- [docs/API_ROUTING_FEATURES.md](docs/API_ROUTING_FEATURES.md) — Routes, features, database schema

---

## License

props
---

## Credits

Dikembangkan untuk **Dinas Komunikasi dan Informatika Kabupaten Sambas**.

Built with Laravel, Tailwind CSS, Fonnte, and Firebase.
