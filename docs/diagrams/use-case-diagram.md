# Use Case Diagram - Agenda eGov

## Aktor
- **Admin**: Administrator sistem
- **User Biasa**: Pengguna terdaftar dengan role user
- **Public User**: Pengguna publik yang belum login

## Use Case

```mermaid
graph TD
    Admin((Admin))
    User((User Biasa))
    Public((Public User))
    
    UC1[Login]
    UC2[Logout]
    UC3[Manage Agenda]
    UC4[Manage Users]
    UC5[Manage Documents]
    UC6[View Public Agenda]
    UC7[View Agenda Detail]
    UC8[Subscribe Notifications]
    UC9[Test WhatsApp]
    UC10[Test FCM]
    UC11[View Subscribers]
    UC12[Resend Notifications]
    UC13[View Profile]
    UC14[Update Profile]
    UC15[Delete Account]
    UC16[Register]
    
    Admin --> UC1
    Admin --> UC2
    Admin --> UC3
    Admin --> UC4
    Admin --> UC5
    Admin --> UC9
    Admin --> UC10
    Admin --> UC11
    Admin --> UC12
    Admin --> UC13
    Admin --> UC14
    Admin --> UC15
    
    User --> UC1
    User --> UC2
    User --> UC6
    User --> UC7
    User --> UC8
    User --> UC13
    User --> UC14
    User --> UC15
    
    Public --> UC6
    Public --> UC7
    Public --> UC8
    Public --> UC16
    
    UC3 -.-> UC5
    UC8 -.-> UC6
```

## Deskripsi Use Case

### Admin
1. **Login**: Masuk ke sistem dengan kredensial admin
2. **Logout**: Keluar dari sistem
3. **Manage Agenda**: CRUD agenda (Create, Read, Update, Delete)
4. **Manage Users**: Kelola user (update role, delete user)
5. **Manage Documents**: Upload dan hapus dokumen agenda
6. **Test WhatsApp**: Test kirim notifikasi WhatsApp
7. **Test FCM**: Test kirim notifikasi push browser
8. **View Subscribers**: Lihat daftar subscriber notifikasi
9. **Resend Notifications**: Kirim ulang notifikasi gagal
10. **View Profile**: Lihat profil sendiri
11. **Update Profile**: Update data profil
12. **Delete Account**: Hapus akun sendiri

### User Biasa
1. **Login**: Masuk ke sistem
2. **Logout**: Keluar dari sistem
3. **View Public Agenda**: Lihat daftar agenda publik
4. **View Agenda Detail**: Lihat detail agenda
5. **Subscribe Notifications**: Berlangganan notifikasi agenda
6. **View Profile**: Lihat profil sendiri
7. **Update Profile**: Update data profil
8. **Delete Account**: Hapus akun sendiri

### Public User
1. **View Public Agenda**: Lihat daftar agenda publik
2. **View Agenda Detail**: Lihat detail agenda
3. **Subscribe Notifications**: Berlangganan notifikasi agenda
4. **Register**: Daftar akun baru (jika registration dibuka)

## Hubungan Use Case
- **Manage Agenda** includes **Manage Documents**: Saat mengelola agenda, admin juga mengelola dokumen
- **Subscribe Notifications** includes **View Public Agenda**: User harus melihat agenda sebelum subscribe
