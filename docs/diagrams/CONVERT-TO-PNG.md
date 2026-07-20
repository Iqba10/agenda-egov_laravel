# Cara Convert PlantUML ke PNG

File `use-case-diagram.puml` sudah dibuat dengan format PlantUML. Berikut cara convert ke PNG:

## Option 1: PlantUML Online Editor (Paling Mudah)

1. Buka https://plantuml-editor.kkeisuke.com/
2. Copy seluruh isi file `use-case-diagram.puml`
3. Paste ke editor
4. Diagram akan otomatis dirender
5. Klik tombol "Download PNG" untuk download gambar

## Option 2: PlantText Online

1. Buka https://www.planttext.com/
2. Copy isi file `use-case-diagram.puml`
3. Paste ke editor
4. Klik "Download as PNG"

## Option 3: Install PlantUML CLI (Windows)

### Prerequisites
- Install Java JDK terlebih dahulu: https://www.oracle.com/java/technologies/downloads/

### Install PlantUML
1. Download PlantUML jar: https://sourceforge.net/projects/plantuml/files/plantuml.jar/download
2. Simpan sebagai `plantuml.jar` di folder yang mudah diakses

### Convert ke PNG
Buka PowerShell/CMD di folder `docs/diagrams/`:

```powershell
java -jar plantuml.jar use-case-diagram.puml
```

File PNG akan dibuat dengan nama `use-case-diagram.png`

## Option 4: VS Code Extension

1. Install extension "PlantUML" di VS Code
2. Buka file `use-case-diagram.puml`
3. Klik kanan → "Preview Current Diagram"
4. Klik icon download di preview untuk export PNG

## Option 5: Graphviz + PlantUML

Jika sudah punya Graphviz terinstall:

```powershell
java -jar plantuml.jar -graphvizdot "C:\Program Files\Graphviz\bin\dot.exe" use-case-diagram.puml
```

## Spesifikasi Diagram yang Dibuat

- **Judul**: Use Case Diagram Sistem Agenda E-Gov
- **3 Aktor**: Admin (kiri), User Biasa (tengah), Public User (kanan)
- **15 Use Case**:
  - Admin: Login, Logout, View Profile, Update Profile, Manage Agenda, Manage Documents, Manage Users, Test WhatsApp, Test FCM, View Subscribers, Resend Notifications
  - User Biasa: Login, Logout, View Profile, Update Profile
  - Public User: Register, View Public Agenda, View Agenda Detail, Subscribe Notifications
- **Relasi**: Manage Agenda <<include>> Manage Documents
- **Style**: Garis tipis abu-abu, oval pastel ungu muda (#ece6fb), border ungu (#8878c3), font sans-serif, background putih
- **Format**: Landscape (lebar > tinggi)
