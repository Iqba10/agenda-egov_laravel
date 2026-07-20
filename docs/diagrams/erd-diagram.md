# ERD (Entity Relationship Diagram) - Agenda eGov

## Deskripsi
Diagram ini menunjukkan struktur database lengkap untuk aplikasi Agenda eGov, termasuk relasi antar tabel dan kolom-kolom yang ada.

## Diagram

```mermaid
graph TD
    users[users]
    agenda[agenda]
    dokumen_agenda[dokumen_agenda]
    agenda_reminders[agenda_reminders]
    fcm_tokens[fcm_tokens]
    notifikasi_pendaftar[notifikasi_pendaftar]
    cache[cache]
    jobs[jobs]
    job_batches[job_batches]
    failed_jobs[failed_jobs]
    
    users -->|creates| agenda
    users -->|receives| agenda_reminders
    users -->|subscribes| notifikasi_pendaftar
    
    agenda -->|has| dokumen_agenda
    agenda -->|has| agenda_reminders
    agenda -->|has| notifikasi_pendaftar
    
    fcm_tokens -->|used by| notifikasi_pendaftar
    
    users --- users_fields
    subgraph users_fields
        id[id PK]
        name[name]
        username[username UK]
        email[email UK]
        password[password]
        role[role admin/user]
        email_verified_at[email_verified_at]
        remember_token[remember_token]
        timestamps[timestamps]
    end
    
    agenda --- agenda_fields
    subgraph agenda_fields
        id[id PK]
        slug[slug UK]
        jenis_agenda[jenis_agenda internal/eksternal]
        perihal_kegiatan[perihal_kegiatan]
        waktu_mulai[waktu_mulai]
        waktu_selesai[waktu_selesai]
        tempat[tempat]
        asal_surat[asal_surat]
        tanggal_surat[tanggal_surat]
        pakaian[pakaian]
        disposisi[disposisi]
        petugas_ditugaskan[petugas_ditugaskan]
        status[status terjadwal/selesai/dibatalkan]
        keterangan[keterangan]
        diinput_oleh[diinput_oleh]
        created_by[created_by FK]
        reminder_sent_at[reminder_sent_at]
        timestamps[timestamps]
    end
    
    dokumen_agenda --- dokumen_agenda_fields
    subgraph dokumen_agenda_fields
        id[id PK]
        agenda_id[agenda_id FK]
        nama_file[nama_file]
        original_name[original_name]
        content_hash[content_hash]
        content[content LONGBLOB]
        mime_type[mime_type]
        file_size[file_size]
        timestamps[timestamps]
    end
    
    agenda_reminders --- agenda_reminders_fields
    subgraph agenda_reminders_fields
        id[id PK]
        nama[nama]
        phone_number[phone_number]
        channel[channel whatsapp/fcm]
        is_sent[is_sent boolean]
        sent_at[sent_at]
        agenda_id[agenda_id FK]
        timestamps[timestamps]
    end
    
    fcm_tokens --- fcm_tokens_fields
    subgraph fcm_tokens_fields
        id[id PK]
        token[token UK]
        device_name[device_name]
        subscribed_agendas[subscribed_agendas JSON]
        sent_reminders[sent_reminders JSON]
        is_active[is_active boolean]
        timestamps[timestamps]
    end
    
    notifikasi_pendaftar --- notifikasi_pendaftar_fields
    subgraph notifikasi_pendaftar_fields
        id[id PK]
        agenda_id[agenda_id FK]
        nama[nama]
        phone_number[phone_number]
        fcm_token_id[fcm_token_id FK]
        channel_preference[channel_preference whatsapp/fcm/both]
        reminder_minutes[reminder_minutes int]
        status[status]
        whatsapp_sent[whatsapp_sent boolean]
        whatsapp_sent_at[whatsapp_sent_at]
        fcm_sent[fcm_sent boolean]
        fcm_sent_at[fcm_sent_at]
        sudah_dikirim[sudah_dikirim boolean]
        timestamps[timestamps]
    end
```

## Tabel Database

### users
Tabel untuk menyimpan data user/pengguna sistem.

| Kolom | Tipe | Deskripsi |
|-------|------|----------|
| id | bigint | Primary Key |
| name | string | Nama lengkap user |
| username | string | Username (unique) |
| email | string | Email (unique) |
| password | string | Password terenkripsi |
| role | string | Role (admin/user) |
| email_verified_at | timestamp | Waktu verifikasi email |
| remember_token | string | Token remember me |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diupdate |

### agenda
Tabel utama untuk menyimpan data agenda kegiatan.

| Kolom | Tipe | Deskripsi |
|-------|------|----------|
| id | bigint | Primary Key |
| slug | string | Slug URL (unique) |
| jenis_agenda | enum | Internal/Eksternal |
| perihal_kegiatan | string | Perihal kegiatan |
| waktu_mulai | datetime | Waktu mulai agenda |
| waktu_selesai | datetime | Waktu selesai agenda |
| tempat | string | Tempat pelaksanaan |
| asal_surat | string | Asal surat |
| tanggal_surat | date | Tanggal surat |
| pakaian | string | Kode pakaian |
| disposisi | string | Disposisi |
| petugas_ditugaskan | string | Petugas ditugaskan |
| status | enum | terjadwal/selesai/dibatalkan |
| keterangan | text | Keterangan tambahan |
| diinput_oleh | string | Nama yang menginput |
| created_by | bigint | ID user yang membuat (FK) |
| reminder_sent_at | datetime | Waktu reminder dikirim |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diupdate |

### dokumen_agenda
Tabel untuk menyimpan dokumen/attachment agenda.

| Kolom | Tipe | Deskripsi |
|-------|------|----------|
| id | bigint | Primary Key |
| agenda_id | bigint | ID agenda (FK) |
| nama_file | string | Nama file di storage |
| original_name | string | Nama asli file |
| content_hash | string | Hash SHA-256 konten |
| content | longblob | Konten file (opsional) |
| mime_type | string | MIME type file |
| file_size | bigint | Ukuran file dalam bytes |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diupdate |

### agenda_reminders
Tabel untuk antrian pengingat agenda (legacy).

| Kolom | Tipe | Deskripsi |
|-------|------|----------|
| id | bigint | Primary Key |
| nama | string | Nama penerima |
| phone_number | string | Nomor telepon |
| channel | enum | whatsapp/fcm |
| is_sent | boolean | Status terkirim |
| sent_at | datetime | Waktu terkirim |
| agenda_id | bigint | ID agenda (FK) |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diupdate |

### fcm_tokens
Tabel untuk menyimpan FCM device tokens untuk notifikasi browser.

| Kolom | Tipe | Deskripsi |
|-------|------|----------|
| id | bigint | Primary Key |
| token | string | FCM token (unique) |
| device_name | string | Nama device |
| subscribed_agendas | json | Daftar ID agenda yang disubscribe |
| sent_reminders | json | Daftar reminder yang sudah dikirim |
| is_active | boolean | Status aktif |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diupdate |

### notifikasi_pendaftar
Tabel untuk menyimpan data subscriber notifikasi publik.

| Kolom | Tipe | Deskripsi |
|-------|------|----------|
| id | bigint | Primary Key |
| agenda_id | bigint | ID agenda (FK) |
| nama | string | Nama subscriber |
| phone_number | string | Nomor WhatsApp |
| fcm_token_id | bigint | ID FCM token (FK) |
| channel_preference | enum | whatsapp/fcm/both |
| reminder_minutes | int | Menit sebelum agenda (default: 60) |
| status | string | Status |
| whatsapp_sent | boolean | Status WA terkirim |
| whatsapp_sent_at | datetime | Waktu WA terkirim |
| fcm_sent | boolean | Status FCM terkirim |
| fcm_sent_at | datetime | Waktu FCM terkirim |
| sudah_dikirim | boolean | Flag sudah dikirim |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diupdate |

### cache, jobs, job_batches, failed_jobs
Tabel sistem Laravel untuk caching dan queue processing.

## Relasi Antar Tabel

1. **users → agenda**: One-to-Many (satu user bisa membuat banyak agenda)
2. **users → agenda_reminders**: One-to-Many (satu user bisa menerima banyak reminder)
3. **users → notifikasi_pendaftar**: One-to-Many (satu user bisa subscribe banyak agenda)
4. **agenda → dokumen_agenda**: One-to-Many (satu agenda bisa punya banyak dokumen)
5. **agenda → agenda_reminders**: One-to-Many (satu agenda punya banyak reminder)
6. **agenda → notifikasi_pendaftar**: One-to-Many (satu agenda punya banyak subscriber)
7. **fcm_tokens → notifikasi_pendaftar**: One-to-Many (satu token bisa dipakai banyak subscriber)
