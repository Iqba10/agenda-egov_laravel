# Activity Diagram - Manajemen Agenda oleh Admin

## Deskripsi
Diagram ini menunjukkan alur lengkap manajemen agenda oleh administrator sistem, mulai dari login, CRUD agenda, manajemen dokumen, hingga manajemen user.

## Diagram

```mermaid
graph TD
    Start([Start]) --> Login[Login sebagai Admin]
    Login --> AuthCheck{Authentication Berhasil?}
    AuthCheck -->|Tidak| Login
    AuthCheck -->|Ya| Dashboard[Menuju Dashboard Admin]
    
    Dashboard --> Action{Pilih Aksi}
    
    Action -->|Buat Agenda Baru| CreateForm[Buka Form Create Agenda]
    Action -->|Edit Agenda| SelectAgenda[Pilih Agenda]
    Action -->|Hapus Agenda| SelectAgenda
    Action -->|Kelola Dokumen| SelectAgenda
    Action -->|Kelola User| UserManagement[Menuju User Management]
    
    CreateForm --> FillData[Isi Data Agenda]
    FillData --> UploadDocs{Upload Dokumen?}
    UploadDocs -->|Ya| ProcessUpload[Proses Upload Dokumen]
    UploadDocs -->|Tidak| ValidateData
    ProcessUpload --> ValidateData[Validasi Data]
    
    ValidateData --> DataValid{Data Valid?}
    DataValid -->|Tidak| CreateForm
    DataValid -->|Ya| SaveAgenda[Simpan Agenda ke Database]
    SaveAgenda --> SuccessMsg[Tampilkan Pesan Sukses]
    
    SelectAgenda --> EditForm[Buka Form Edit Detail]
    EditForm --> UpdateData[Update Data Agenda]
    UpdateData --> SaveAgenda
    
    SelectAgenda --> DeleteConfirm{Konfirmasi Hapus?}
    DeleteConfirm -->|Tidak| Dashboard
    DeleteConfirm -->|Ya| DeleteDocs[Hapus Dokumen Terkait]
    DeleteDocs --> DeleteAgenda[Hapus Agenda dari Database]
    DeleteAgenda --> SuccessMsg
    
    SelectAgenda --> DocManagement[Menuju Dokumen Management]
    DocManagement --> DocAction{Aksi Dokumen}
    DocAction -->|Upload| ProcessUpload
    DocAction -->|Hapus| DeleteDocConfirm{Konfirmasi Hapus Dokumen?}
    DeleteDocConfirm -->|Ya| DeleteDocFile[Hapus File dan Database]
    DeleteDocConfirm -->|Tidak| DocManagement
    DeleteDocFile --> DocManagement
    
    UserManagement --> UserAction{Aksi User}
    UserAction -->|Update Role| UpdateUserRole[Update Role User]
    UserAction -->|Hapus User| DeleteUserConfirm{Konfirmasi Hapus?}
    DeleteUserConfirm -->|Ya| DeleteUser[Hapus User]
    DeleteUserConfirm -->|Tidak| UserManagement
    UpdateUserRole --> UserManagement
    DeleteUser --> UserManagement
    
    SuccessMsg --> Dashboard
    Dashboard --> Logout{Logout?}
    Logout -->|Ya| End([End])
    Logout -->|Tidak| Action
```

## Penjelasan Alur

### 1. Login
- Admin memasukkan kredensial login
- Sistem melakukan validasi
- Jika berhasil, diarahkan ke dashboard admin
- Jika gagal, kembali ke halaman login

### 2. Dashboard
- Admin memilih aksi yang ingin dilakukan:
  - Buat Agenda Baru
  - Edit Agenda
  - Hapus Agenda
  - Kelola Dokumen
  - Kelola User

### 3. Buat Agenda Baru
- Buka form create agenda
- Isi data agenda (perihal, waktu, tempat, dll)
- Opsional: upload dokumen
- Validasi data
- Simpan ke database
- Tampilkan pesan sukses

### 4. Edit/Hapus Agenda
- Pilih agenda dari daftar
- Buka form edit/confirm delete
- Update data atau konfirmasi hapus
- Jika hapus: hapus dokumen terkait dulu
- Simpan perubahan ke database

### 5. Kelola Dokumen
- Pilih agenda
- Masuk ke manajemen dokumen
- Upload dokumen baru atau hapus dokumen
- Dokumen disimpan ke storage dan database

### 6. Kelola User
- Masuk ke user management
- Update role user (admin/user)
- Hapus user (dengan validasi minimal 1 admin)
- Simpan perubahan

### 7. Logout
- Admin memilih logout
- Session dihapus
- Diarahkan ke halaman login
