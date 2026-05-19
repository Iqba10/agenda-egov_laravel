# Deployment Guide — Agenda eGov

Dokumen ini menjelaskan cara deploy Agenda eGov (Laravel 12) ke server production atau staging.

---

## 1. Server Requirements

| Requirement | Minimum |
|-------------|---------|
| PHP | 8.2 atau lebih baru |
| Composer | 2.x |
| Database | MySQL 8.0+ atau MariaDB 10.4+ |
| Node.js + npm | Diperlukan untuk build asset (atau build di CI lalu kirim `public/build`) |
| Web server | Nginx atau Apache |

PHP extensions yang wajib ada:

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
```

---

## 2. Document Root

Web server harus diarahkan ke folder **`public/`**, bukan root project:

```
/var/www/agenda-egov/public
```

Jangan expose root project ke internet — `.env`, source code, dan storage tidak boleh public.

---

## 3. Environment Configuration

Salin `.env.example`:

```bash
cp .env.example .env
```

Konfigurasi penting:

```env
APP_NAME="Agenda eGov - Diskominfo Sambas"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://domain-anda.example

APP_LOCALE=id
APP_FALLBACK_LOCALE=id

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=agenda_egov
DB_USERNAME=agenda_user
DB_PASSWORD=strong-password

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=public

ADMIN_NAME="Administrator Agenda eGov"
ADMIN_EMAIL=admin@agenda-egov.local
ADMIN_PASSWORD=ganti-password-ini
```

Generate app key:

```bash
php artisan key:generate --force
```

> **Penting**: `APP_URL` harus sesuai domain production termasuk scheme (`https://`). Dokumen agenda diakses via route Laravel (`/documents/{id}`), bukan symlink storage langsung — sehingga `APP_URL` port mismatch di lokal tidak mempengaruhi production.

---

## 4. Install PHP Dependencies

```bash
composer install --no-dev --optimize-autoloader
```

---

## 5. Build Frontend Assets

Build di server atau CI lalu deploy hasilnya:

```bash
npm ci
npm run build
```

Pastikan folder ini tersedia setelah build:

```
public/build/
```

Jika build dilakukan di lokal/CI, ikutkan folder `public/build/` dalam deployment artifact.

---

## 6. Storage dan Cache Permissions

Pastikan web server bisa menulis ke:

```
storage/
bootstrap/cache/
```

Linux (sesuaikan user/group dengan konfigurasi server):

```bash
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rw storage bootstrap/cache
```

---

## 7. Migrasi Database

Jalankan migrasi:

```bash
php artisan migrate --force
```

Urutan migration yang akan dijalankan:

1. `create_users_table` — users, password_reset_tokens, sessions
2. `create_cache_table` — cache, cache_locks
3. `create_jobs_table` — jobs, job_batches, failed_jobs
4. `create_agenda_table` — tabel agenda utama
5. `add_slug_to_agendas_table` — slug, tanggal_surat, keterangan, created_by
6. `create_dokumen_agenda_table` — tabel dokumen agenda

---

## 8. Seed Database

Seed admin dan data demo:

```bash
php artisan db:seed --force
```

Seeder yang dijalankan:
- **UserSeeder** — membuat akun admin dan user default:

```
admin@agenda-egov.local / password   (role: admin)
user@agenda-egov.local  / password   (role: user)
```

- **AgendaSeeder** — membuat contoh data agenda

> **Wajib**: Ganti password akun admin segera setelah login pertama kali di production.

---

## 9. Storage Link

Wajib untuk akses file dokumen publik:

```bash
php artisan storage:link
```

Validasi:

```bash
ls -la public/storage
```

Target harus mengarah ke `storage/app/public`.

> **Catatan**: File dokumen di production dapat diakses tanpa symlink karena menggunakan route Laravel `/documents/{id}`. Namun `storage:link` tetap diperlukan jika ada file lain yang menggunakan `Storage::url()`.

---

## 10. Optimasi Laravel

Setelah semua konfigurasi siap:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Untuk clear cache saat troubleshooting:

```bash
php artisan optimize:clear
```

---

## 11. Web Server Configuration

### Nginx

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

Untuk HTTPS, tambahkan SSL certificate (Let's Encrypt / Certbot direkomendasikan).

### Apache

- Arahkan `DocumentRoot` ke `public/`.
- Aktifkan `mod_rewrite`.
- File `public/.htaccess` Laravel harus tersedia.

---

## 12. Health Check

Laravel menyediakan route health bawaan:

```
GET /up
```

Gunakan untuk load balancer atau monitoring.

---

## 13. Smoke Test Setelah Deploy

Jalankan dari server:

```bash
php artisan migrate:status
php artisan route:list --no-ansi
php artisan view:cache --no-ansi
```

Cek via browser:

1. `/` — daftar agenda publik tampil.
2. `/login` — form login split-panel bisa dibuka.
3. Login dengan `admin@agenda-egov.local` berhasil.
4. `/admin` — dashboard admin tampil dengan daftar agenda.
5. Tambah agenda baru berhasil.
6. Upload dokumen berhasil.
7. `/documents/{id}` — dokumen bisa dibuka inline (PDF/gambar).
8. `/documents/{id}/download` — dokumen bisa didownload.
9. `/admin/agendas/print` — halaman cetak tampil.
10. Widget cuaca di header publik tampil.

---

## 14. Dev Utility (Lokal Windows)

Untuk lingkungan lokal Windows, gunakan `manage.bat`:

```
manage.bat
[1] Clear DB & Cache (Fresh Migrate)
[2] Reset DB + Full Reseed
[3] Clear All Caches Only
[4] Exit
```

---

## 15. Production Checklist

- [ ] `.env` production lengkap dan benar.
- [ ] `APP_DEBUG=false`.
- [ ] `APP_KEY` sudah digenerate.
- [ ] `APP_URL` sesuai domain production.
- [ ] Database production tersedia dan dapat diakses.
- [ ] `composer install --no-dev --optimize-autoloader` berhasil.
- [ ] `npm run build` berhasil atau `public/build/` sudah tersedia.
- [ ] `php artisan migrate --force` berhasil.
- [ ] `php artisan db:seed --force` berhasil.
- [ ] `php artisan storage:link` berhasil.
- [ ] `php artisan config:cache route:cache view:cache` berhasil.
- [ ] Permissions `storage/` dan `bootstrap/cache/` benar.
- [ ] Password admin default sudah diganti.
- [ ] Upload dokumen diuji.
- [ ] View dan download dokumen diuji.
- [ ] Halaman cetak laporan diuji.
- [ ] Widget cuaca berfungsi (opsional — tergantung akses internet server).

---

## 16. Rollback

Jika perlu rollback ke versi sebelumnya:

1. Backup database sebelum `migrate --force`.
2. Backup folder `storage/app/public/` sebelum deploy.
3. Deploy artifact lama.
4. Jalankan `php artisan optimize:clear`.

Aplikasi native PHP lama tersedia di `native_old/` sebagai arsip — namun membutuhkan konfigurasi web server dari awal jika ingin diaktifkan kembali.
