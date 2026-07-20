# UML Activity Diagram — Agenda eGov

## Overview

Activity diagram untuk flow utama di aplikasi Agenda eGov:
- Authentication Flow (Login/Register)
- Subscribe Notifikasi Flow
- CRUD Agenda Flow (Admin)
- Kirim Notifikasi Flow (Scheduler)

---

## 1. Authentication Flow (Login)

### Mermaid Diagram

```mermaid
flowchart TD
    A([Start]) --> B[Buka Halaman Login]
    B --> C{Input Email & Password}
    C -->|Valid| D[Submit Form]
    C -->|Invalid| C
    D --> E{Validasi Server}
    E -->|Sukses| F[Create Session]
    E -->|Gagal| G[Tampilkan Error]
    G --> C
    F --> H{Role User?}
    H -->|Admin| I[Redirect ke Dashboard Admin]
    H -->|User| J[Redirect ke Halaman Agenda]
    I --> K([End])
    J --> K
```

### PlantUML Diagram

```plantuml
@startuml
start
:Buka Halaman Login;
:Input Email & Password;
if (Input Valid?) then (yes)
    :Submit Form;
    if (Validasi Server?) then (yes)
        :Create Session;
        if (Role Admin?) then (yes)
            :Redirect ke Dashboard Admin;
        else (no)
            :Redirect ke Halaman Agenda;
        endif
    else (no)
        :Tampilkan Error;
        :Input Email & Password;
    endif
else (no)
    :Input Email & Password;
endif
stop
@enduml
```

---

## 2. Register Flow

### Mermaid Diagram

```mermaid
flowchart TD
    A([Start]) --> B{REGISTRATION_OPEN?}
    B -->|False| C[Tampilkan Pesan Registrasi Ditutup]
    C --> D([End])
    B -->|True| E[Buka Halaman Register]
    E --> F[Input Nama, Email, Password]
    F --> G[Submit Form]
    G --> H{Validasi Server}
    H -->|Sukses| I[Create User dengan role user]
    H -->|Gagal| J[Tampilkan Error]
    J --> F
    I --> K[Auto Login]
    K --> L[Redirect ke Halaman Agenda]
    L --> M([End])
```

### PlantUML Diagram

```plantuml
@startuml
start
if (REGISTRATION_OPEN?) then (false)
    :Tampilkan Pesan Registrasi Ditutup;
    stop
else (true)
    :Buka Halaman Register;
    :Input Nama, Email, Password;
    :Submit Form;
    if (Validasi Server?) then (yes)
        :Create User dengan role user;
        :Auto Login;
        :Redirect ke Halaman Agenda;
    else (no)
        :Tampilkan Error;
        :Input Nama, Email, Password;
    endif
endif
stop
@enduml
```

---

## 3. Subscribe Notifikasi Flow

### Mermaid Diagram

```mermaid
flowchart TD
    A([Start]) --> B[Buka Halaman Agenda]
    B --> C[Klik Tombol Subscribe]
    C --> D[Buka Modal Subscribe]
    D --> E{User Login?}
    E -->|Belum| F[Tampilkan Form Login]
    F --> G{Login Sukses?}
    G -->|Ya| H[Input Nama, No HP, Pilih Channel]
    G -->|Tidak| I([End])
    E -->|Sudah| H
    H --> J[Submit Form]
    J --> K{Validasi Input}
    K -->|Valid| L[Simpan ke notifikasi_pendaftar]
    K -->|Invalid| M[Tampilkan Error]
    M --> H
    L --> N{Channel FCM?}
    N -->|Ya| O[Simpan FCM Token]
    N -->|Tidak| P[Skip FCM Token]
    O --> Q[Buat Agenda Reminder]
    P --> Q
    Q --> R[Tampilkan Toast Sukses]
    R --> S[Tutup Modal]
    S --> T([End])
```

### PlantUML Diagram

```plantuml
@startuml
start
:Buka Halaman Agenda;
:Klik Tombol Subscribe;
:Buka Modal Subscribe;
if (User Login?) then (no)
    :Tampilkan Form Login;
    if (Login Sukses?) then (yes)
    else (no)
        stop
    endif
endif
:Input Nama, No HP, Pilih Channel;
:Submit Form;
if (Validasi Input?) then (yes)
    :Simpan ke notifikasi_pendaftar;
    if (Channel FCM?) then (yes)
        :Simpan FCM Token;
    endif
    :Buat Agenda Reminder;
    :Tampilkan Toast Sukses;
    :Tutup Modal;
else (no)
    :Tampilkan Error;
endif
stop
@enduml
```

---

## 4. CRUD Agenda Flow (Admin) - Create

### Mermaid Diagram

```mermaid
flowchart TD
    A([Start]) --> B[Login sebagai Admin]
    B --> C[Buka Dashboard Admin]
    C --> D[Klik Tombol Tambah Agenda]
    D --> E[Buka Form Create Agenda]
    E --> F[Input Perihal, Jenis, Waktu, Lokasi, Deskripsi]
    F --> G{Upload Dokumen?}
    G -->|Ya| H[Pilih File]
    G -->|Tidak| I[Submit Form]
    H --> I
    I --> J{Validasi Form}
    J -->|Valid| K[Simpan Agenda ke Database]
    J -->|Invalid| L[Tampilkan Error]
    L --> F
    K --> M{Ada Dokumen?}
    M -->|Ya| N[Simpan Dokumen ke Storage]
    M -->|Tidak| O[Redirect ke List Agenda]
    N --> P[Simpan Data Dokumen ke dokumen_agenda]
    P --> O
    O --> Q[Tampilkan Toast Sukses]
    Q --> R([End])
```

### PlantUML Diagram

```plantuml
@startuml
start
:Login sebagai Admin;
:Buka Dashboard Admin;
:Klik Tombol Tambah Agenda;
:Buka Form Create Agenda;
:Input Perihal, Jenis, Waktu, Lokasi, Deskripsi;
if (Upload Dokumen?) then (yes)
    :Pilih File;
endif
:Submit Form;
if (Validasi Form?) then (yes)
    :Simpan Agenda ke Database;
    if (Ada Dokumen?) then (yes)
        :Simpan Dokumen ke Storage;
        :Simpan Data Dokumen ke dokumen_agenda;
    endif
    :Redirect ke List Agenda;
    :Tampilkan Toast Sukses;
else (no)
    :Tampilkan Error;
endif
stop
@enduml
```

---

## 5. CRUD Agenda Flow (Admin) - Update

### Mermaid Diagram

```mermaid
flowchart TD
    A([Start]) --> B[Login sebagai Admin]
    B --> C[Buka Dashboard Admin]
    C --> D[Klik Agenda di List]
    D --> E[Buka Detail Agenda]
    E --> F[Klik Tombol Edit]
    F --> G[Buka Form Edit Agenda]
    G --> H[Update Data Agenda]
    H --> I{Upload Dokumen Baru?}
    I -->|Ya| J[Pilih File]
    I -->|Tidak| K[Submit Form]
    J --> K
    K --> L{Validasi Form}
    L -->|Valid| M[Update Agenda di Database]
    L -->|Invalid| N[Tampilkan Error]
    N --> H
    M --> O{Ada Dokumen Baru?}
    O -->|Ya| P[Simpan Dokumen ke Storage]
    O -->|Tidak| Q[Redirect ke Detail Agenda]
    P --> R[Simpan Data Dokumen ke dokumen_agenda]
    R --> Q
    Q --> S[Tampilkan Toast Sukses]
    S --> T([End])
```

### PlantUML Diagram

```plantuml
@startuml
start
:Login sebagai Admin;
:Buka Dashboard Admin;
:Klik Agenda di List;
:Buka Detail Agenda;
:Klik Tombol Edit;
:Buka Form Edit Agenda;
:Update Data Agenda;
if (Upload Dokumen Baru?) then (yes)
    :Pilih File;
endif
:Submit Form;
if (Validasi Form?) then (yes)
    :Update Agenda di Database;
    if (Ada Dokumen Baru?) then (yes)
        :Simpan Dokumen ke Storage;
        :Simpan Data Dokumen ke dokumen_agenda;
    endif
    :Redirect ke Detail Agenda;
    :Tampilkan Toast Sukses;
else (no)
    :Tampilkan Error;
endif
stop
@enduml
```

---

## 6. Kirim Notifikasi Flow (Scheduler)

### Mermaid Diagram

```mermaid
flowchart TD
    A([Start]) --> B[Scheduler Trigger]
    B --> C[Query agenda_reminders pending]
    C --> D{Ada Reminder Pending?}
    D -->|Tidak| E([End])
    D -->|Ya| F[Loop per reminder]
    F --> G{Channel?}
    G -->|whatsapp| H[Load FonnteSender]
    G -->|fcm| I[Load FcmSender]
    H --> J[Kirim WhatsApp]
    I --> K[Kirim Push Notification]
    J --> L{Kirim Sukses?}
    K --> L
    L -->|Ya| M[Update is_sent = true]
    L -->|Tidak| N[Log Error]
    N --> O[Continue Loop]
    M --> O
    O --> P{Masih Ada Reminder?}
    P -->|Ya| F
    P -->|Tidak| Q[Tampilkan Summary]
    Q --> R([End])
```

### PlantUML Diagram

```plantuml
@startuml
start
:Scheduler Trigger;
:Query agenda_reminders pending;
if (Ada Reminder Pending?) then (no)
    stop
else (yes)
    repeat
        :Loop per reminder;
        if (Channel?) then (whatsapp)
            :Load FonnteSender;
            :Kirim WhatsApp;
        else (fcm)
            :Load FcmSender;
            :Kirim Push Notification;
        endif
        if (Kirim Sukses?) then (yes)
            :Update is_sent = true;
        else (no)
            :Log Error;
        endif
    repeat while (Masih Ada Reminder?)
    :Tampilkan Summary;
endif
stop
@enduml
```

---

## 7. Download Dokumen Flow

### Mermaid Diagram

```mermaid
flowchart TD
    A([Start]) --> B[Buka Detail Agenda]
    B --> C[Klik Tombol Download Dokumen]
    C --> D[Fetch document via /documents/{id}]
    D --> E{Dokumen Ada?}
    E -->|Tidak| F[Tampilkan 404]
    F --> G([End])
    E -->|Ya| H[DocumentController serve file]
    H --> I[Convert to Blob]
    I --> J[Create temporary <a> tag]
    J --> K[Trigger download]
    K --> L[Remove <a> tag]
    L --> M([End])
```

### PlantUML Diagram

```plantuml
@startuml
start
:Buka Detail Agenda;
:Klik Tombol Download Dokumen;
:Fetch document via /documents/{id};
if (Dokumen Ada?) then (no)
    :Tampilkan 404;
    stop
else (yes)
    :DocumentController serve file;
    :Convert to Blob;
    :Create temporary <a> tag;
    :Trigger download;
    :Remove <a> tag;
endif
stop
@enduml
```

---

## 8. Test Notifikasi Flow (Admin)

### Mermaid Diagram

```mermaid
flowchart TD
    A([Start]) --> B[Login sebagai Admin]
    B --> C[Buka Halaman Test Notifikasi]
    C --> D{Pilih Test Type}
    D -->|WhatsApp| E[Input Nomor HP]
    D -->|FCM| F[Input FCM Token]
    D -->|Broadcast| G[Input Topic]
    E --> H[Klik Kirim Test WA]
    F --> I[Klik Kirim Test FCM]
    G --> J[Klik Broadcast]
    H --> K[FonnteSender send]
    I --> L[FcmSender send]
    J --> M[FcmSender send to topic]
    K --> N{Tampilkan Response}
    L --> N
    M --> N
    N --> O[Status: Sukses/Gagal]
    O --> P([End])
```

### PlantUML Diagram

```plantuml
@startuml
start
:Login sebagai Admin;
divide "Pilih Test Type"
    if (WhatsApp?) then (yes)
        :Input Nomor HP;
        :Klik Kirim Test WA;
        :FonnteSender send;
    elseif (FCM?) then (yes)
        :Input FCM Token;
        :Klik Kirim Test FCM;
        :FcmSender send;
    elseif (Broadcast?) then (yes)
        :Input Topic;
        :Klik Broadcast;
        :FcmSender send to topic;
    endif
end division
:Tampilkan Response;
:Status: Sukses/Gagal;
stop
@enduml
```

---

## Notes

- **Authentication Flow**: Menggunakan Laravel Breeze untuk UI dan session management
- **Subscribe Notifikasi**: Data disimpan ke `notifikasi_pendaftar`, `fcm_tokens`, dan `agenda_reminders`
- **CRUD Agenda**: Dokumen disimpan di `storage/app/public` dan diakses via route `/documents/{id}`
- **Kirim Notifikasi**: Dijalankan oleh scheduler setiap 5 menit via `php artisan agenda:send-reminders`
- **Download Dokumen**: Menggunakan blob URL untuk menghindari intercept oleh IDM
- **Test Notifikasi**: Panel admin untuk testing Fonnte dan FCM secara manual
