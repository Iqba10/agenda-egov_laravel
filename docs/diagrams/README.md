# Diagram Dokumentasi - Agenda eGov

Folder ini berisi diagram-digram UML untuk aplikasi Agenda eGov.

## Daftar Diagram

1. **use-case-diagram.md** - Use Case Diagram
2. **activity-diagram.md** - Activity Diagram (Manajemen Agenda)
3. **erd-diagram.md** - Entity Relationship Diagram
4. **class-diagram.md** - Class Diagram

## Cara Melihat Diagram

### Option 1: Mermaid Live Editor (Online)
1. Buka https://mermaid.live
2. Copy kode Mermaid dari file diagram
3. Paste ke editor
4. Diagram akan otomatis dirender

### Option 2: GitHub
1. Push file-file ini ke repository GitHub
2. GitHub akan otomatis merender diagram Mermaid di file Markdown
3. Buka file `.md` di GitHub untuk melihat diagram

### Option 3: VS Code
1. Install extension "Markdown Preview Mermaid Support"
2. Buka file diagram di VS Code
3. Klik "Preview" untuk melihat diagram

### Option 4: Export ke PNG/SVG
1. Buka https://mermaid.live
2. Paste kode Mermaid
3. Klik menu "Actions" → "Export PNG/SVG"
4. Download diagram dalam format gambar

## Ringkasan

### Use Case Diagram
Menampilkan aktor (Admin, User Biasa, Public User) dan use case utama sistem seperti:
- Login/Logout
- Manage Agenda (CRUD)
- Manage Users
- Manage Documents
- View Public Agenda
- Subscribe Notifications
- Test WhatsApp/FCM

### Activity Diagram
Menunjukkan alur lengkap manajemen agenda oleh admin:
- Login → Dashboard → Pilih Aksi
- Create/Edit/Delete Agenda
- Upload/Hapus Dokumen
- Manage Users (Update Role, Delete)

### ERD Diagram
Menampilkan struktur database lengkap:
- **users** (id, name, username, email, password, role)
- **agenda** (id, slug, jenis_agenda, perihal_kegiatan, waktu_mulai, waktu_selesai, dll)
- **dokumen_agenda** (id, agenda_id, nama_file, content, mime_type, dll)
- **agenda_reminders** (id, nama, phone_number, channel, is_sent, agenda_id)
- **fcm_tokens** (id, token, device_name, subscribed_agendas, sent_reminders)
- **notifikasi_pendaftar** (id, agenda_id, nama, phone_number, fcm_token_id, channel_preference)

### Class Diagram
Menampilkan struktur class dan hubungan:
- **Controllers**: AgendaController, UserController, NotificationTestController, SubscriberController, WeatherController, AuthenticatedSessionController, RegisteredUserController, DocumentController, NotificationController, ProfileController, PublicAgendaController
- **Models**: Agenda, AgendaDocument, AgendaReminder, FcmToken, NotifikasiPendaftar, User
- **Services**: AgendaReminderService, FonnteSender, FcmSender
