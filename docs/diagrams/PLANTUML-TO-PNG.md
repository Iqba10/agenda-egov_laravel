# Cara Convert PlantUML ke PNG

## Kenapa PlantUML Bukan Mermaid?

**Mermaid TIDAK mendukung Use Case Diagram standar UML** (stick figure actor + oval use case). Mermaid hanya mendukung:
- Flowchart
- Sequence Diagram
- Class Diagram
- State Diagram
- Gantt Chart

**PlantUML adalah tool yang tepat untuk Use Case Diagram standar UML.**

## Cara Paling Mudah (Online - Tanpa Install)

### Option 1: PlantUML Editor (Recommended)
1. Buka: https://plantuml-editor.kkeisuke.com/
2. Copy isi file `use-case-diagram.puml`
3. Paste ke editor
4. Diagram otomatis dirender
5. Klik **"Download PNG"** untuk download

### Option 2: PlantText
1. Buka: https://www.planttext.com/
2. Copy isi file `use-case-diagram.puml`
3. Paste ke editor
4. Klik **"Download as PNG"**

## Cara dengan Install Software (Offline)

### Install PlantUML CLI

**Prerequisites:**
- Install Java JDK: https://www.oracle.com/java/technologies/downloads/

**Steps:**
1. Download PlantUML jar: https://sourceforge.net/projects/plantuml/files/plantuml.jar/download
2. Simpan sebagai `plantuml.jar`

**Convert ke PNG:**
```powershell
java -jar plantuml.jar use-case-diagram.puml
```

### VS Code Extension

1. Install extension "PlantUML" di VS Code
2. Buka file `use-case-diagram.puml`
3. Klik kanan → "Preview Current Diagram"
4. Klik icon download di preview untuk export PNG

## Spesifikasi Diagram yang Dibuat

File `use-case-diagram.puml` sudah dibuat dengan:
- **Judul**: Use Case Diagram Sistem Agenda E-Gov
- **3 Aktor**: Admin (kiri), User Biasa (tengah), Public User (kanan)
- **15 Use Case** dengan koneksi yang sesuai
- **Relasi**: Manage Agenda <<include>> Manage Documents
- **Style**: Oval pastel ungu muda (#ece6fb), border ungu (#8878c3), garis abu-abu
- **Format**: Landscape (lebar > tinggi)
