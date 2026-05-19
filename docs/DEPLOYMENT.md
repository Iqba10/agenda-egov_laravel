# Deployment Guide — Agenda eGov

Dokumen ini menjelaskan cara deploy Agenda eGov (Laravel 12) ke berbagai environment.

---

## Table of Contents

1. [Server Requirements](#1-server-requirements)
2. [Environment Configuration](#2-environment-configuration)
3. [Manual Deployment](#3-manual-deployment)
4. [Railway Deployment](#4-railway-deployment)
5. [Docker Deployment](#5-docker-deployment)
6. [Notification Setup](#6-notification-setup)
7. [Production Checklist](#7-production-checklist)
8. [Troubleshooting](#8-troubleshooting)

---

## 1. Server Requirements

| Requirement | Minimum |
|-------------|---------|
| PHP | 8.2+ |
| Composer | 2.x |
| Node.js | 18+ (untuk build frontend) |
| Database | MySQL 8.0+ / MariaDB 10.4+ |
| Web Server | Nginx atau Apache |

### PHP Extensions Required

```
pdo_mysql
mbstring
openssl
tokenizer
xml
ctype
json
fileinfo
curl
gd (untuk image processing)
```

---

## 2. Environment Configuration

### Copy dan Edit .env

```bash
cp .env.example .env
```

### Production Settings

```env
# App
APP_NAME="Agenda eGov - Diskominfo Sambas"
APP_ENV=production
APP_KEY=                    # Generate dengan php artisan key:generate
APP_DEBUG=false
APP_URL=https://domain-anda.example

APP_LOCALE=id
APP_FALLBACK_LOCALE=id

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=agenda_egov
DB_USERNAME=agenda_user
DB_PASSWORD=strong-password

# Cache & Session
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=public

# Admin Seeder
ADMIN_NAME="Administrator Agenda eGov"
ADMIN_EMAIL=admin@agenda-egov.local
ADMIN_PASSWORD=ganti-password-ini

# WhatsApp (Fonnte) - Optional
FONNTE_TOKEN=your-fonnte-api-token
FONNTE_DEVICE=

# Firebase Cloud Messaging - Optional
FIREBASE_PROJECT_ID=your-project-id
FIREBASE_CREDENTIALS_PATH=storage/app/firebase-credentials.json
```

### Generate App Key

```bash
php artisan key:generate --force
```

> **Penting**: `APP_URL` harus sesuai domain production termasuk scheme (`https://`).

---

## 3. Manual Deployment

### Step 1: Install PHP Dependencies

```bash
composer install --no-dev --optimize-autoloader
```

### Step 2: Build Frontend

```bash
npm ci
npm run build
```

Pastikan folder `public/build/` tersedia setelah build.

### Step 3: Setup Permissions

```bash
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rw storage bootstrap/cache
```

### Step 4: Database Migration

```bash
php artisan migrate --force
php artisan db:seed --force
```

### Step 5: Storage Link

```bash
php artisan storage:link
```

### Step 6: Optimize Laravel

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 7: Setup Scheduler (Cron)

Tambahkan ke crontab:

```bash
* * * * * cd /var/www/agenda-egov && php artisan schedule:run >> /dev/null 2>&1
```

### Web Server Configuration

#### Nginx

```nginx
server {
    listen 80;
    server_name domain-anda.example;
    root /var/www/agenda-egov/public;

    index index.php index.html;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

#### Apache

- Arahkan `DocumentRoot` ke `public/`
- Aktifkan `mod_rewrite`
- File `public/.htaccess` Laravel harus tersedia

---

## 4. Railway Deployment

Railway adalah platform cloud yang mendukung auto-deploy dari Docker.

### Files yang Diperlukan

```
Dockerfile              # Multi-stage build
railway.json            # Railway config
docker/
  entrypoint.sh         # Container startup script
  nginx.conf            # Nginx config
  php.ini               # PHP settings
  supervisord.conf      # Process manager
  www.conf              # PHP-FPM config
.dockerignore           # Exclude dari build
```

### Deploy Steps

1. **Push ke GitHub**
   ```bash
   git push origin main
   ```

2. **Connect di Railway Dashboard**
   - Buka [railway.app](https://railway.app)
   - New Project → Deploy from GitHub repo
   - Pilih repository `agenda-egov_laravel`

3. **Add MySQL Database**
   - New → Database → MySQL
   - Copy connection variables

4. **Set Environment Variables**

   ```env
   APP_NAME=Agenda eGov
   APP_ENV=production
   APP_DEBUG=false
   APP_KEY=base64:...     # Generate locally, paste here
   APP_URL=https://your-app.up.railway.app

   DB_CONNECTION=mysql
   DB_HOST=${{MySQL.MYSQLHOST}}
   DB_PORT=${{MySQL.MYSQLPORT}}
   DB_DATABASE=${{MySQL.MYSQLDATABASE}}
   DB_USERNAME=${{MySQL.MYSQLUSER}}
   DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}

   ADMIN_NAME=Administrator
   ADMIN_EMAIL=admin@agenda-egov.local
   ADMIN_PASSWORD=secure-password

   FONNTE_TOKEN=your-fonnte-token
   FIREBASE_PROJECT_ID=your-firebase-project
   ```

5. **Deploy**
   - Railway akan auto-build dari Dockerfile
   - Health check: `GET /up`

### Generate APP_KEY Locally

```bash
php -r "echo 'base64:' . base64_encode(random_bytes(32)) . PHP_EOL;"
```

---

## 5. Docker Deployment

### Build Image

```bash
docker build -t agenda-egov:latest .
```

### Run Container

```bash
docker run -d \
  --name agenda-egov \
  -p 8080:80 \
  -e APP_KEY=base64:... \
  -e APP_URL=http://localhost:8080 \
  -e DB_HOST=host.docker.internal \
  -e DB_DATABASE=agenda_egov \
  -e DB_USERNAME=root \
  -e DB_PASSWORD=secret \
  agenda-egov:latest
```

### Docker Compose

```yaml
version: '3.8'

services:
  app:
    build: .
    ports:
      - "8080:80"
    environment:
      - APP_KEY=base64:...
      - APP_URL=http://localhost:8080
      - DB_HOST=db
      - DB_DATABASE=agenda_egov
      - DB_USERNAME=root
      - DB_PASSWORD=secret
    depends_on:
      - db

  db:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: secret
      MYSQL_DATABASE: agenda_egov
    volumes:
      - mysql_data:/var/lib/mysql

volumes:
  mysql_data:
```

---

## 6. Notification Setup

### WhatsApp (Fonnte)

1. Daftar di [fonnte.com](https://fonnte.com)
2. Dapatkan API token dari dashboard
3. Set environment variable:
   ```env
   FONNTE_TOKEN=your-api-token
   FONNTE_DEVICE=            # Optional, untuk multi-device
   ```
4. Test di `/admin/notifications/test`

### Firebase Cloud Messaging

1. Buat project di [Firebase Console](https://console.firebase.google.com)
2. Enable Cloud Messaging
3. **Service Account** (Server-side):
   - Project Settings → Service Accounts → Generate new private key
   - Download JSON file
   - Upload ke server: `storage/app/firebase-credentials.json`
   - Set environment:
     ```env
     FIREBASE_PROJECT_ID=your-project-id
     FIREBASE_CREDENTIALS_PATH=storage/app/firebase-credentials.json
     ```

4. **Web Push** (Client-side):
   - Project Settings → General → Your apps → Add web app
   - Copy config values ke environment:
     ```env
     VITE_FIREBASE_API_KEY=...
     VITE_FIREBASE_AUTH_DOMAIN=...
     VITE_FIREBASE_PROJECT_ID=...
     VITE_FIREBASE_STORAGE_BUCKET=...
     VITE_FIREBASE_MESSAGING_SENDER_ID=...
     VITE_FIREBASE_APP_ID=...
     VITE_FIREBASE_VAPID_KEY=...
     ```
   - Rebuild frontend: `npm run build`

5. Test di `/admin/notifications/test`

---

## 7. Production Checklist

### Pre-Deployment

- [ ] `.env` production lengkap dan benar
- [ ] `APP_DEBUG=false`
- [ ] `APP_KEY` sudah digenerate
- [ ] `APP_URL` sesuai domain production
- [ ] Database production tersedia dan dapat diakses
- [ ] Password admin default sudah diganti di `.env`

### Deployment

- [ ] `composer install --no-dev --optimize-autoloader` berhasil
- [ ] `npm run build` berhasil atau `public/build/` tersedia
- [ ] `php artisan migrate --force` berhasil
- [ ] `php artisan db:seed --force` berhasil
- [ ] `php artisan storage:link` berhasil
- [ ] `php artisan config:cache route:cache view:cache` berhasil
- [ ] Permissions `storage/` dan `bootstrap/cache/` benar

### Post-Deployment

- [ ] Health check `/up` returns 200
- [ ] Login admin berhasil
- [ ] Upload dokumen diuji
- [ ] View dan download dokumen diuji
- [ ] Halaman cetak laporan diuji
- [ ] Widget cuaca berfungsi (opsional)
- [ ] Notifikasi WhatsApp diuji (jika dikonfigurasi)
- [ ] Push notification diuji (jika dikonfigurasi)
- [ ] Cron scheduler berjalan

### Security

- [ ] HTTPS enabled
- [ ] Password admin sudah diganti
- [ ] `.env` tidak accessible dari web
- [ ] `storage/` tidak accessible dari web (kecuali via route)

---

## 8. Troubleshooting

### 500 Internal Server Error

```bash
# Check logs
tail -f storage/logs/laravel.log

# Clear caches
php artisan optimize:clear
php artisan config:clear
```

### Permission Denied

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Dokumen Tidak Bisa Diakses

```bash
# Pastikan storage link ada
php artisan storage:link

# Check permission
ls -la public/storage

# Harus link ke storage/app/public
```

### Database Connection Refused

- Cek `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- Pastikan MySQL service running
- Cek firewall/security group

### Vite Manifest Not Found

```bash
# Build frontend
npm run build

# Pastikan public/build/ ada
ls -la public/build/
```

### Scheduler Tidak Berjalan

```bash
# Test manual
php artisan schedule:run

# Check crontab
crontab -l

# Add cron entry
* * * * * cd /var/www/agenda-egov && php artisan schedule:run >> /dev/null 2>&1
```

### Notifikasi Tidak Terkirim

- Cek `FONNTE_TOKEN` dan `FIREBASE_PROJECT_ID` di `.env`
- Test di `/admin/notifications/test`
- Cek logs: `storage/logs/laravel.log`

---

## Rollback

Jika perlu rollback ke versi sebelumnya:

1. Backup database sebelum `migrate --force`
2. Backup folder `storage/app/public/` sebelum deploy
3. Deploy artifact/commit lama
4. Jalankan `php artisan optimize:clear`

---

## Support

- **Documentation**: [README.md](../README.md), [AGENTS.md](../AGENTS.md)
- **Issues**: GitHub Issues
- **API Reference**: [API_ROUTING_FEATURES.md](API_ROUTING_FEATURES.md)
