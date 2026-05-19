# API, Routing, dan Features Guide

Dokumen ini menjelaskan route, controller, hak akses, fitur, dan skema data Agenda eGov Laravel 12.

---

## Table of Contents

1. [API Notes](#1-api-notes)
2. [Route Summary](#2-route-summary)
3. [Route Protection Matrix](#3-route-protection-matrix)
4. [Feature Details](#4-feature-details)
5. [Console Commands](#5-console-commands)
6. [Database Schema](#6-database-schema)
7. [Views & UI Components](#7-views--ui-components)
8. [JavaScript Behavior](#8-javascript-behavior)
9. [Future API Plan](#9-future-api-plan)

---

## 1. API Notes

Versi saat ini **belum menyediakan JSON REST API publik**. Semua fitur berjalan lewat route web Blade.

Endpoint JSON internal:

| Method | URI | Controller | Keterangan |
|--------|-----|------------|------------|
| GET | `/api/weather` | `Api\WeatherController` | Proxy cuaca Sambas (JSON) |

---

## 2. Route Summary

### Public Routes

| Method | URI | Name | Controller | Keterangan |
|--------|-----|------|------------|------------|
| GET | `/` | `agenda.index` | `PublicAgendaController@index` | Daftar agenda publik |
| GET | `/agenda/{agenda}` | `agenda.show` | `PublicAgendaController@show` | Detail agenda publik |
| GET | `/documents/{document}` | `documents.show` | `DocumentController@show` | Serve file inline |
| GET | `/documents/{document}/download` | `documents.download` | `DocumentController@download` | Force-download file |
| POST | `/notifications/subscribe` | `notifications.subscribe` | `NotificationController@subscribe` | Subscribe notifikasi |
| POST | `/notifications/fcm-token` | `notifications.fcm-token` | `NotificationController@storeFcmToken` | Store FCM token |
| GET | `/api/weather` | `api.weather` | `Api\WeatherController` | Proxy cuaca (JSON) |
| GET | `/up` | — | Laravel health | Health check |

### Guest/Auth Routes

Didefinisikan di `routes/auth.php`. Middleware: `guest`.

| Method | URI | Name | Keterangan |
|--------|-----|------|------------|
| GET | `/login` | `login` | Form login (split-panel) |
| POST | `/login` | — | Submit login |
| GET | `/register` | `register` | Form register (split-panel) |
| POST | `/register` | — | Submit register |
| GET | `/forgot-password` | `password.request` | Form permintaan reset |
| POST | `/forgot-password` | `password.email` | Kirim link reset |
| GET | `/reset-password/{token}` | `password.reset` | Form password baru |
| POST | `/reset-password` | `password.store` | Simpan password baru |
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
| GET | `/admin` | `admin.dashboard` | `Admin\AgendaController@index` | Dashboard admin |
| GET | `/admin/agendas/print` | `admin.agendas.print` | `Admin\AgendaController@print` | Halaman cetak |
| GET | `/admin/agendas/create` | `admin.agendas.create` | `Admin\AgendaController@create` | Form tambah |
| POST | `/admin/agendas` | `admin.agendas.store` | `Admin\AgendaController@store` | Simpan agenda |
| GET | `/admin/agendas/{agenda}` | `admin.agendas.show` | `Admin\AgendaController@show` | Detail agenda |
| GET | `/admin/agendas/{agenda}/edit` | `admin.agendas.edit` | `Admin\AgendaController@edit` | Form edit |
| PUT | `/admin/agendas/{agenda}` | `admin.agendas.update` | `Admin\AgendaController@update` | Update agenda |
| DELETE | `/admin/agendas/{agenda}` | `admin.agendas.destroy` | `Admin\AgendaController@destroy` | Hapus agenda |
| DELETE | `/admin/agendas/{agenda}/documents/{document}` | `admin.agendas.documents.destroy` | `Admin\AgendaController@destroyDocument` | Hapus dokumen |
| GET | `/admin/users` | `admin.users.index` | `Admin\UserController@index` | Daftar user |
| PATCH | `/admin/users/{user}/role` | `admin.users.role` | `Admin\UserController@updateRole` | Ubah role |
| GET | `/admin/notifications/test` | `admin.notifications.test` | `Admin\NotificationTestController@index` | Panel test notifikasi |
| POST | `/admin/notifications/test/whatsapp` | `admin.notifications.test.whatsapp` | `Admin\NotificationTestController@testWhatsApp` | Test WA |
| POST | `/admin/notifications/test/fcm` | `admin.notifications.test.fcm` | `Admin\NotificationTestController@testFcm` | Test FCM |
| POST | `/admin/notifications/test/broadcast` | `admin.notifications.test.broadcast` | `Admin\NotificationTestController@testBroadcast` | Broadcast FCM |

> **Note**: Parameter `{agenda}` di-resolve via **slug** (`Agenda::getRouteKeyName() = 'slug'`).

---

## 3. Route Protection Matrix

| Area | Guest | `user` | `admin` |
|------|-------|--------|---------|
| Public agenda (/) | ✓ | ✓ | ✓ |
| Detail agenda | ✓ | ✓ | ✓ |
| Dokumen (view/download) | ✓ | ✓ | ✓ |
| Subscribe notifikasi | ✓ | ✓ | ✓ |
| Weather API | ✓ | ✓ | ✓ |
| Login/register | ✓ | → | → |
| Profil | ✗ | ✓ | ✓ |
| Admin dashboard | ✗ | ✗ | ✓ |
| Admin agenda CRUD | ✗ | ✗ | ✓ |
| Admin print | ✗ | ✗ | ✓ |
| Admin user management | ✗ | ✗ | ✓ |
| Admin test notifikasi | ✗ | ✗ | ✓ |

Legend: ✓ = Allowed, ✗ = Denied, → = Redirect to dashboard

---

## 4. Feature Details

### 4.1 Public Agenda List (`/`)

**Controller**: `PublicAgendaController@index`

**Query Parameters**:

| Query | Contoh | Keterangan |
|-------|--------|------------|
| `search` | `?search=rapat` | Live debounce search |
| `status` | `?status=terjadwal` | Filter status |

**Features**:
- Stat cards (total, terjadwal, selesai, dibatalkan)
- Filter status dengan button pill
- Live search debounce 400ms
- Status badge berwarna
- Widget cuaca + jam digital
- Modal subscribe notifikasi

### 4.2 Public Agenda Detail (`/agenda/{slug}`)

**Controller**: `PublicAgendaController@show`

**Features**:
- Layout 2-kolom di desktop
- Breadcrumb
- Dokumen embed via Blob URL
- Download anti-IDM via fetch → blob
- Agenda lainnya di sidebar
- Tombol subscribe notifikasi

### 4.3 Document Serving

**Controller**: `DocumentController`

| Method | Route | Keterangan |
|--------|-------|------------|
| `show` | `GET /documents/{document}` | Serve inline |
| `download` | `GET /documents/{document}/download` | Force-download |

**Model Attributes** (`AgendaDocument`):
- `url` → `route('documents.show', $this)`
- `download_url` → `route('documents.download', $this)`
- `type` → `'pdf'`, `'image'`, `'other'`
- `extension` → file extension
- `exists` → cek file di storage

**Storage**: `storage/app/public/agendas/documents/{filename}`

### 4.4 Notification Subscription

**Controller**: `NotificationController`

**Endpoints**:
- `POST /notifications/subscribe` — Subscribe dengan channel preference
- `POST /notifications/fcm-token` — Store FCM device token

**Request Body** (subscribe):
```json
{
  "agenda_id": 1,
  "nama": "John Doe",
  "phone_number": "08123456789",
  "fcm_token": "...",
  "channel_preference": "both"
}
```

**Channel Options**: `whatsapp`, `fcm`, `both`

### 4.5 Admin Test Notifikasi

**Controller**: `Admin\NotificationTestController`

**Features**:
- Test kirim WhatsApp ke nomor tertentu
- Test kirim FCM ke token tertentu
- Broadcast FCM ke topic `agenda-updates`
- Status konfigurasi (Fonnte configured, FCM configured)

### 4.6 Agenda Management

**Model**: `App\Models\Agenda`

**Validation Requests**:
- `StoreAgendaRequest`
- `UpdateAgendaRequest`

**Form Fields**:

| Field | Required | Keterangan |
|-------|----------|------------|
| `jenis_agenda` | Ya | `internal` atau `eksternal` |
| `perihal_kegiatan` | Ya | Deskripsi agenda |
| `waktu_mulai` | Ya | datetime-local |
| `waktu_selesai` | Ya | >= waktu_mulai |
| `tempat` | Ya | Lokasi, max 255 |
| `asal_surat` | Ya | Asal surat, max 255 |
| `tanggal_surat` | Tidak | Date |
| `pakaian` | Tidak | String, max 255 |
| `disposisi` | Tidak | Text |
| `petugas_ditugaskan` | Tidak | String, max 255 |
| `status` | Ya | `terjadwal`, `selesai`, `dibatalkan` |
| `keterangan` | Tidak | Text |
| `documents[]` | Tidak | Array file (max 5MB/file) |

**Computed Attributes**:

| Attribute | Keterangan |
|-----------|------------|
| `effective_status` | Computed: `berlangsung` jika antara waktu mulai/selesai |
| `status_badge_class` | Tailwind classes untuk badge |
| `slug` | Auto-generate dari perihal + tanggal |

### 4.7 Document Upload

**Upload**: via `Admin\AgendaController@store` / `@update`

**Delete**: via `Admin\AgendaController@destroyDocument` (AJAX)

**File Naming**: `YmdHis_RandomStr10.ext`

**Allowed Types**: `pdf, jpg, jpeg, png, docx, xlsx`

**Max Size**: 5 MB per file

### 4.8 Weather Widget

**Route**: `GET /api/weather`

**Response**:
```json
{
  "temp": 28,
  "condition": "Berawan",
  "humidity": 78,
  "wind": 12
}
```

### 4.9 Print Laporan

**Route**: `GET /admin/agendas/print`

**Query Parameters**: sama dengan halaman index (status, search, month, year)

---

## 5. Console Commands

### Custom Commands

| Command | Keterangan |
|---------|------------|
| `php artisan agenda:send-reminders` | Kirim notifikasi pending |

### Standard Commands

```bash
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan route:list --no-ansi
php artisan optimize:clear
```

### Scheduler

```bash
# Cron (production)
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

---

## 6. Database Schema

### `users`

| Column | Type | Keterangan |
|--------|------|------------|
| `id` | bigint PK | |
| `name` | string | |
| `username` | string unique nullable | |
| `email` | string unique | |
| `role` | string | `user` atau `admin` |
| `password` | string | Hashed |
| `remember_token` | string nullable | |
| `timestamps` | | |

### `agenda`

| Column | Type | Keterangan |
|--------|------|------------|
| `id` | int PK | |
| `jenis_agenda` | enum | `internal`, `eksternal` |
| `perihal_kegiatan` | text | |
| `slug` | string unique nullable | Auto-generate |
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
| `diinput_oleh` | string(100) nullable | |
| `created_by` | FK → users nullable | |
| `timestamps` | | |

> **Note**: `berlangsung` adalah computed state, bukan nilai DB.

### `dokumen_agenda`

| Column | Type | Keterangan |
|--------|------|------------|
| `id` | bigint PK | |
| `agenda_id` | FK → agenda | cascade delete |
| `nama_file` | string | Nama file di storage |
| `original_name` | string | Nama file asli |
| `content_hash` | string nullable | Hash untuk dedupe |
| `timestamps` | | |

### `agenda_reminders`

| Column | Type | Keterangan |
|--------|------|------------|
| `id` | bigint PK | |
| `nama` | string(100) | |
| `phone_number` | string(20) | |
| `channel` | enum | `whatsapp`, `fcm` |
| `is_sent` | boolean | default false |
| `sent_at` | timestamp nullable | |
| `agenda_id` | FK → agenda | cascade delete |
| `timestamps` | | |

### `fcm_tokens`

| Column | Type | Keterangan |
|--------|------|------------|
| `id` | bigint PK | |
| `token` | string(500) unique | FCM device token |
| `device_name` | string(255) nullable | |
| `subscribed_agendas` | json nullable | |
| `is_active` | boolean | default true |
| `timestamps` | | |

### `notifikasi_pendaftar`

| Column | Type | Keterangan |
|--------|------|------------|
| `id` | bigint PK | |
| `agenda_id` | FK → agenda | cascade delete |
| `nama` | string(100) nullable | |
| `phone_number` | string(20) nullable | |
| `fcm_token_id` | FK → fcm_tokens nullable | null on delete |
| `channel_preference` | enum | `whatsapp`, `fcm`, `both` |
| `whatsapp_sent` | boolean | default false |
| `whatsapp_sent_at` | timestamp nullable | |
| `fcm_sent` | boolean | default false |
| `fcm_sent_at` | timestamp nullable | |
| `timestamps` | | |

---

## 7. Views & UI Components

### Layouts

| File | Keterangan |
|------|------------|
| `layouts/app.blade.php` | Layout admin (Figtree font, sidebar) |
| `layouts/guest.blade.php` | Layout auth (Outfit font, split-panel) |

### Partials

| File | Keterangan |
|------|------------|
| `partials/sidebar.blade.php` | Sidebar admin |
| `partials/toast.blade.php` | Toast notification |
| `partials/public-footer.blade.php` | Footer publik |

### CSS Components (`resources/css/app.css`)

| Class | Keterangan |
|-------|------------|
| `.btn`, `.btn-primary`, `.btn-secondary`, `.btn-danger` | Buttons |
| `.input`, `.label`, `.helper` | Form elements |
| `.surface`, `.surface-muted` | Card containers |
| `.table-ui` | Table styling |
| `.sidebar-link`, `.sidebar-link-active` | Sidebar nav |
| `.dashboard-grid` | Stats grid |
| `.dashboard-stat-card`, `.dashboard-stat-card-{color}` | Stat cards |
| `.dashboard-filter`, `.dashboard-filter-active-{color}` | Filter pills |
| `.badge`, `.badge-{blue,amber,emerald,red,slate}` | Status badges |
| `.animate-dashboard` | Fade-in animation |

---

## 8. JavaScript Behavior

### `resources/js/app.js`

- Sidebar open/close toggle (mobile)
- Toast auto-close
- Modal konfirmasi delete (data-confirm)
- `lucide.createIcons()` initialization
- `window.docDownload(url)` — download via fetch → blob

### `resources/js/firebase-init.js`

- Firebase SDK initialization
- FCM token request
- Foreground message handling

### `public/firebase-messaging-sw.js`

- Service worker untuk background FCM

---

## 9. Future API Plan

Jika diperlukan API JSON/mobile di masa depan:

### Potential Endpoints

```
GET  /api/v1/agendas              List agenda (paginated)
GET  /api/v1/agendas/{id}         Detail agenda
POST /api/v1/agendas/{id}/subscribe   Subscribe notifikasi
GET  /api/v1/weather              Proxy cuaca (existing)
```

### Authentication Options

- Laravel Sanctum (SPA + Mobile)
- API tokens (simple)

### Implementation Notes

- Buat controller terpisah di `app/Http/Controllers/Api/`
- Gunakan API resources untuk response format
- Rate limiting via `throttle` middleware
