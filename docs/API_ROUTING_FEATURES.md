# API, Routing, dan Features Guide

Dokumen ini menjelaskan route, controller, hak akses, fitur, dan skema data Agenda eGov Laravel 12.

---

## 1. Catatan API

Versi saat ini **belum menyediakan JSON REST API publik**. Semua fitur berjalan lewat route web Blade.

Satu-satunya endpoint JSON internal adalah weather proxy:

```
GET /api/weather   → WeatherController (JSON, public)
```

Jika nanti diperlukan API JSON/mobile, lihat bagian **9. Future API Plan**.

---

## 2. Route Summary

### Public Routes

| Method | URI | Name | Controller | Keterangan |
|--------|-----|------|------------|------------|
| GET | `/` | `agenda.index` | `PublicAgendaController@index` | Daftar agenda publik + filter + live search |
| GET | `/agenda/{agenda}` | `agenda.show` | `PublicAgendaController@show` | Detail agenda publik + dokumen embed |
| GET | `/documents/{document}` | `documents.show` | `DocumentController@show` | Serve file dokumen inline (anti APP_URL mismatch) |
| GET | `/documents/{document}/download` | `documents.download` | `DocumentController@download` | Force-download file dokumen |
| GET | `/api/weather` | `api.weather` | `Api\WeatherController` | Proxy cuaca Sambas (JSON) |
| GET | `/up` | — | Laravel health | Health check |

### Guest/Auth Routes

Didefinisikan di `routes/auth.php`. Middleware: `guest`.

| Method | URI | Name | Keterangan |
|--------|-----|------|------------|
| GET | `/login` | `login` | Form login (split-panel) |
| POST | `/login` | — | Submit login |
| GET | `/register` | `register` | Form register (split-panel) |
| POST | `/register` | — | Submit register; role default `user`; redirect ke login |
| GET | `/forgot-password` | `password.request` | Form permintaan reset password (split-panel) |
| POST | `/forgot-password` | `password.email` | Kirim link reset |
| GET | `/reset-password/{token}` | `password.reset` | Form password baru |
| POST | `/reset-password` | `password.store` | Simpan password baru |
| GET | `/confirm-password` | `password.confirm` | Konfirmasi password |
| POST | `/confirm-password` | — | Submit konfirmasi |
| PUT | `/password` | `password.update` | Update password |
| GET | `/verify-email` | `verification.notice` | Prompt verifikasi email |
| GET | `/verify-email/{id}/{hash}` | `verification.verify` | Proses verifikasi email |
| POST | `/email/verification-notification` | `verification.send` | Kirim ulang verifikasi |
| POST | `/logout` | `logout` | Logout |

### Authenticated User Routes

Middleware: `auth`

| Method | URI | Name | Controller | Keterangan |
|--------|-----|------|------------|------------|
| GET | `/profile` | `profile.edit` | `ProfileController@edit` | Form edit profil |
| PATCH | `/profile` | `profile.update` | `ProfileController@update` | Update profil |
| DELETE | `/profile` | `profile.destroy` | `ProfileController@destroy` | Hapus akun |

### Admin Routes

Middleware: `auth`, `role:admin`  
Prefix: `/admin`  
Name prefix: `admin.`

| Method | URI | Name | Controller | Keterangan |
|--------|-----|------|------------|------------|
| GET | `/admin` | `admin.dashboard` | `Admin\AgendaController@index` | Dashboard admin = daftar agenda |
| GET | `/admin/agendas/print` | `admin.agendas.print` | `Admin\AgendaController@print` | Halaman cetak laporan agenda |
| GET | `/admin/agendas/create` | `admin.agendas.create` | `Admin\AgendaController@create` | Form tambah agenda |
| POST | `/admin/agendas` | `admin.agendas.store` | `Admin\AgendaController@store` | Simpan agenda baru |
| GET | `/admin/agendas/{agenda}` | `admin.agendas.show` | `Admin\AgendaController@show` | Detail agenda admin |
| GET | `/admin/agendas/{agenda}/edit` | `admin.agendas.edit` | `Admin\AgendaController@edit` | Form edit agenda |
| PUT | `/admin/agendas/{agenda}` | `admin.agendas.update` | `Admin\AgendaController@update` | Update agenda |
| DELETE | `/admin/agendas/{agenda}` | `admin.agendas.destroy` | `Admin\AgendaController@destroy` | Hapus agenda + semua dokumennya |
| DELETE | `/admin/agendas/{agenda}/documents/{document}` | `admin.agendas.documents.destroy` | `Admin\AgendaController@destroyDocument` | Hapus satu dokumen (AJAX) |
| GET | `/admin/users` | `admin.users.index` | `Admin\UserController@index` | Daftar user |
| PATCH | `/admin/users/{user}/role` | `admin.users.role` | `Admin\UserController@updateRole` | Ubah role user |

> **Catatan**: Parameter `{agenda}` di-resolve via **slug** (`Agenda::getRouteKeyName() = 'slug'`).

---

## 3. Route Protection Matrix

| Area | Guest | `user` | `admin` |
|------|-------|--------|---------|
| Public agenda (/) | Bisa | Bisa | Bisa |
| Detail agenda | Bisa | Bisa | Bisa |
| Dokumen (view/download) | Bisa | Bisa | Bisa |
| Weather API | Bisa | Bisa | Bisa |
| Login/register | Bisa | Redirect | Redirect |
| Profil | Tidak | Bisa | Bisa |
| Admin dashboard | Tidak | Tidak | Bisa |
| Admin agenda CRUD | Tidak | Tidak | Bisa |
| Admin print | Tidak | Tidak | Bisa |
| Admin user management | Tidak | Tidak | Bisa |

---

## 4. Feature Details

### 4.1 Public Agenda List (`/`)

Controller: `App\Http\Controllers\PublicAgendaController@index`

Query parameters:

| Query | Contoh | Keterangan |
|-------|--------|------------|
| `search` | `?search=rapat` | Live debounce search (perihal, tempat, asal surat) |
| `status` | `?status=terjadwal` | Filter status: `terjadwal`, `selesai`, `dibatalkan`, kosong = semua |

View: `resources/views/agenda/index.blade.php`

Fitur:
- Stat cards berwarna (total, terjadwal, selesai, dibatalkan).
- Filter status dengan button pill aktif berwarna.
- Live search debounce 400ms.
- Tabel dengan status badge berwarna.
- Widget cuaca + jam digital live di header.

### 4.2 Public Agenda Detail (`/agenda/{slug}`)

Controller: `App\Http\Controllers\PublicAgendaController@show`

View: `resources/views/agenda/show.blade.php`

Fitur:
- Layout 2-kolom di desktop (konten kiri, sidebar kanan).
- Breadcrumb.
- Dokumen embed via Blob URL (PDF iframe, image preview).
- Download dokumen anti-IDM via fetch → blob.
- Agenda lainnya di sidebar.
- Tombol "Edit Agenda" hanya muncul jika user login sebagai admin.

### 4.3 Document Serving

Controller: `App\Http\Controllers\DocumentController`

| Method | Route | Keterangan |
|--------|-------|------------|
| `show` | `GET /documents/{document}` | Serve inline dengan Content-Type benar |
| `download` | `GET /documents/{document}/download` | Force-download (`Content-Disposition: attachment`) |

Model attributes:
- `AgendaDocument::$url` → `route('documents.show', $this)`
- `AgendaDocument::$download_url` → `route('documents.download', $this)`
- `AgendaDocument::$type` → `'pdf'`, `'image'`, `'other'`
- `AgendaDocument::$extension` → ekstensi file
- `AgendaDocument::$exists` → cek file di storage

Storage path: `storage/app/public/agendas/documents/{filename}`  
Disk: `public`

### 4.4 Autentikasi

Controller auth: `App\Http\Controllers\Auth\*` (Laravel Breeze foundation)

Register behavior:
- Buat user dengan `role = user`.
- Tidak auto-login; redirect ke `/login` dengan session status.

Login behavior:
- Cek `remember` checkbox untuk remember token.
- Error ditampilkan inline di bawah field password (bukan card terpisah).

### 4.5 RBAC

Middleware: `App\Http\Middleware\EnsureUserRole`

Alias di `bootstrap/app.php`:
```php
'role' => \App\Http\Middleware\EnsureUserRole::class
```

Role yang tersedia: `user`, `admin`

Contoh penggunaan:
```php
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(...);
```

### 4.6 Agenda Management

Model: `App\Models\Agenda`

Validation Requests:
- `App\Http\Requests\StoreAgendaRequest`
- `App\Http\Requests\UpdateAgendaRequest` (extends StoreAgendaRequest)

Field form:

| Field | Required | Keterangan |
|-------|----------|------------|
| `jenis_agenda` | Ya | `internal` atau `eksternal` |
| `perihal_kegiatan` | Ya | Deskripsi agenda |
| `waktu_mulai` | Ya | datetime-local |
| `waktu_selesai` | Ya | Setelah/sama dengan waktu mulai |
| `tempat` | Ya | Lokasi, max 255 |
| `asal_surat` | Ya | Asal surat/instansi, max 255 |
| `tanggal_surat` | Tidak | Date |
| `pakaian` | Tidak | String, max 255 |
| `disposisi` | Tidak | Text |
| `petugas_ditugaskan` | Tidak | String, max 255 |
| `status` | Ya | `terjadwal`, `selesai`, `dibatalkan` |
| `keterangan` | Tidak | Text |
| `documents[]` | Tidak | Array file (PDF/JPG/PNG/DOCX/XLSX, maks 5MB/file) |

Computed attributes:

| Attribute | Keterangan |
|-----------|------------|
| `effective_status` | `berlangsung` jika antara waktu mulai dan selesai; `terjadwal` jika sebelum mulai; `selesai` jika sudah lewat; `dibatalkan` jika status DB = dibatalkan |
| `status_badge_class` | String class Tailwind lengkap untuk badge (amber/blue/emerald/rose) |
| `slug` | Auto-generate dari perihal + tanggal, dipakai sebagai route key |

### 4.7 Document Management

Upload via `Admin\AgendaController@store` / `@update` (multi-file `documents[]`).

Delete via `Admin\AgendaController@destroyDocument`:
- AJAX `POST` dengan `_method=DELETE` di FormData.
- Response JSON `{"success": true}` untuk request AJAX.
- Response redirect untuk form biasa.

File naming: `YmdHis_RandomStr10.ext`

Allowed MIME/ext: `pdf, jpg, jpeg, png, docx, xlsx`

Max size: **5 MB per file**

### 4.8 Weather Widget

Route: `GET /api/weather`  
Controller: `App\Http\Controllers\Api\WeatherController`

Response JSON (internal):
```json
{
  "temp": 28,
  "condition": "Berawan",
  "humidity": 78,
  "wind": 12
}
```

Ditampilkan di header dashboard public via JavaScript fetch.

### 4.9 Print Laporan

Route: `GET /admin/agendas/print`  
Controller: `Admin\AgendaController@print`

Query parameters sama dengan halaman index (status, search, month, year).

View: `resources/views/admin/agendas/print.blade.php`

---

## 5. Console Commands

Tidak ada custom Artisan command aktif saat ini.

Standard commands yang relevan:

```bash
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan route:list --no-ansi
php artisan view:cache --no-ansi
php artisan optimize:clear
```

Dev utility (Windows):

```bash
manage.bat
```

Menu:
- `[1]` Fresh migrate + optimize:clear
- `[2]` migrate:fresh --seed (reset penuh + seed)
- `[3]` optimize:clear saja

---

## 6. Database Tables

### `users`

| Column | Type | Keterangan |
|--------|------|------------|
| `id` | bigint PK | |
| `name` | string | |
| `email` | string unique | |
| `email_verified_at` | timestamp nullable | |
| `password` | string | Hashed |
| `role` | string | `user` atau `admin`; default `user` |
| `remember_token` | string nullable | |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

### `agenda`

| Column | Type | Keterangan |
|--------|------|------------|
| `id` | int PK auto-increment | |
| `jenis_agenda` | enum | `internal`, `eksternal` |
| `perihal_kegiatan` | text | |
| `slug` | string unique nullable | Auto-generate dari perihal + tanggal |
| `waktu_mulai` | datetime | |
| `waktu_selesai` | datetime | |
| `tempat` | string(255) | |
| `asal_surat` | string(255) | |
| `tanggal_surat` | date nullable | |
| `pakaian` | string(100) | |
| `disposisi` | text nullable | |
| `petugas_ditugaskan` | string(255) | |
| `status` | enum | `terjadwal`, `selesai`, `dibatalkan` |
| `keterangan` | text nullable | |
| `diinput_oleh` | string(100) nullable | Nama inputter |
| `created_by` | FK → users nullable | |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

> **Catatan**: `berlangsung` **bukan** nilai DB. Status ini dihitung di PHP via `Agenda::getEffectiveStatusAttribute()` berdasarkan waktu saat ini vs `waktu_mulai`/`waktu_selesai`.

### `dokumen_agenda`

| Column | Type | Keterangan |
|--------|------|------------|
| `id` | bigint PK | |
| `agenda_id` | int FK → agenda cascade delete | |
| `nama_file` | string | Nama file tersimpan di storage |
| `original_name` | string | Nama file asli dari user |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

Storage path: `storage/app/public/agendas/documents/{nama_file}`

### `password_reset_tokens`

Standard Laravel.

### `sessions`

Standard Laravel (session driver: database jika dikonfigurasi).

### `cache`, `jobs`

Standard Laravel.

---

## 7. Views dan UI Components

### Layout

| File | Keterangan |
|------|------------|
| `layouts/app.blade.php` | Layout admin (Figtree font, sidebar, Lucide, docDownload JS) |
| `layouts/guest.blade.php` | Layout auth (Outfit font, split-panel) |

### Partials

| File | Keterangan |
|------|------------|
| `partials/sidebar.blade.php` | Sidebar admin dengan grup menu + card profil |
| `partials/toast.blade.php` | Toast notification |
| `partials/public-footer.blade.php` | Footer publik Diskominfo Sambas |

### Views

| Folder | Keterangan |
|--------|------------|
| `agenda/index.blade.php` | Daftar agenda publik |
| `agenda/show.blade.php` | Detail agenda publik |
| `admin/agendas/index.blade.php` | Dashboard admin + daftar agenda |
| `admin/agendas/show.blade.php` | Detail agenda admin |
| `admin/agendas/create.blade.php` | Form tambah agenda |
| `admin/agendas/edit.blade.php` | Form edit agenda |
| `admin/agendas/_form.blade.php` | Partial form agenda (shared create/edit) |
| `admin/agendas/print.blade.php` | Halaman cetak laporan |
| `admin/users/index.blade.php` | Manajemen user |
| `auth/login.blade.php` | Login split-panel |
| `auth/register.blade.php` | Register split-panel |
| `auth/forgot-password.blade.php` | Forgot password split-panel |
| `profile/edit.blade.php` | Edit profil user |

### CSS Components (`resources/css/app.css`)

| Class | Keterangan |
|-------|------------|
| `.btn`, `.btn-primary`, `.btn-secondary`, `.btn-danger` | Button variants |
| `.input`, `.label`, `.helper` | Form elements |
| `.surface`, `.surface-muted` | Card containers |
| `.table-ui` | Tabel standar |
| `.sidebar-link`, `.sidebar-link-active` | Sidebar nav item |
| `.dashboard-grid` | Grid 2-kolom (mobile) / 4-kolom (desktop) |
| `.dashboard-stat-card`, `.dashboard-stat-card-{color}` | Stat card dengan warna |
| `.dashboard-panel`, `.dashboard-panel-header`, `.dashboard-panel-title` | Panel container |
| `.dashboard-filter`, `.dashboard-filter-active-{color}` | Filter pill button |
| `.badge`, `.badge-{blue,amber,emerald,red,slate}` | Status badge |
| `.animate-dashboard` | Fade-in animasi entry |

---

## 8. JavaScript Behavior

### `resources/js/app.js`
- Sidebar open/close toggle (mobile hamburger).
- Toast auto-close.
- Form `data-confirm` — modal konfirmasi custom untuk hapus agenda.
- Lucide icon initialization.

### Global functions di `layouts/app.blade.php`
- `docDownload(url, filename)` — fetch file → blob → trigger `<a download>`. Anti-IDM.

### Inline scripts di halaman dokumen (show pages)
- Untuk setiap dokumen PDF/image: `fetch(docUrl)` → blob → set `iframe.src` atau `img.src`. Menghindari IDM intercept dan APP_URL mismatch.

---

## 9. Future API Plan

Jika ingin menambahkan REST API JSON:

```
GET    /api/agendas
GET    /api/agendas/{agenda}
POST   /api/auth/login
POST   /api/auth/logout
GET    /api/me
```

Untuk admin:
```
POST   /api/admin/agendas
PUT    /api/admin/agendas/{agenda}
DELETE /api/admin/agendas/{agenda}
POST   /api/admin/agendas/{agenda}/documents
DELETE /api/admin/agendas/{agenda}/documents/{document}
GET    /api/admin/users
PATCH  /api/admin/users/{user}/role
```

Tambahkan di `routes/api.php` dengan Laravel Sanctum untuk token auth dan rate limiting.
