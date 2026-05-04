# PatrolHR - Sistem Manajemen Patroli HR Operation

**PatrolHR** adalah aplikasi web berbasis Laravel & Filament untuk mengelola dan monitoring kegiatan patroli security/HR Operation secara real-time dengan fitur QR code validation, foto absensi, tanda tangan digital, dan pelaporan pelanggaran.

---

## 📋 Daftar Isi

- [Fitur Utama](#fitur-utama)
- [Stack Teknologi](#stack-teknologi)
- [Instalasi & Setup](#instalasi--setup)
- [Struktur Proyek](#struktur-proyek)
- [Panduan Penggunaan](#panduan-penggunaan)
- [Database Schema](#database-schema)
- [API & Endpoint](#api--endpoint)
- [Troubleshooting](#troubleshooting)

---

## 🎯 Fitur Utama

### 1. **Form Laporan Patroli (3-Step Wizard)**
   - **Step 1 - PIC Patroli**: Input PIC (Person in Charge), waktu, shift
   - **Step 2 - Lokasi & Temuan**: Pilih area patrol, toggle ada/tidaknya temuan, pilih karyawan pelanggar (jika ada)
   - **Step 3 - Checkpoint & Absensi**: Foto muka + tanda tangan digital (wajib selesai sebelum simpan)

### 2. **QR Code Validation**
   - Scan QR code lokasi sebelum membuka form patrol
   - Validasi otomatis dengan timestamp dan IP address
   - Mencegah pelaporan palsu dari lokasi yang salah

### 3. **Checksheet Patrol**
   - Rekap kegiatan patrol harian per shift, location, dan group
   - Export PDF & Excel untuk keperluan dokumentasi
   - Tampil status paraf/signature dari setiap petugas

### 4. **Rekap Temuan Patrol**
   - Daftar lengkap temuan/pelanggaran dengan detail karyawan pelanggar
   - Filter by tanggal, shift, group, dan jenis pelanggaran
   - Evidence (foto temuan) tersimpan dan dapat diakses
   - Export PDF & Excel untuk laporan manajemen

### 5. **Manajemen Data Master**
   - **Karyawan**: Database karyawan dengan NIP, nama, shift group (A/B/C/D)
   - **Lokasi**: Area patrol dengan koordinat GPS dan UUID untuk QR code
   - **Pelanggaran**: Tipe-tipe pelanggaran yang dapat dicatat
   - **Tindakan**: Daftar tindakan/sanksi (SP, PHK, Teguran, dll)
   - **Shift**: Grup shift (Shift 1, Shift 2, Shift 3)

### 6. **Authentication & Authorization**
   - Role-based access control (Admin, Supervisor, Security Officer)
   - Permission management via Spatie Laravel Permission
   - Activity logging untuk audit trail

---

## 🛠️ Stack Teknologi

| Layer | Teknologi |
|-------|-----------|
| **Backend** | Laravel 11 |
| **Admin Panel** | Filament |
| **Frontend** | Blade Templates + Alpine.js |
| **Database** | MariaDB / MySQL |
| **File Storage** | Local Storage (public disk) |
| **Export** | PhpSpreadsheet (Excel), DomPDF (PDF) |
| **Queue** | Redis / Database |
| **Auth** | Laravel Sanctum + Spatie Permission |

---

## 📦 Instalasi & Setup

### Prasyarat
- PHP 8.2+
- Composer
- MariaDB/MySQL 5.7+
- Node.js 16+ (untuk Vite)

### Langkah Instalasi

```bash
# 1. Clone repository
git clone <repo-url> PatrolHR
cd PatrolHR

# 2. Install dependencies
composer install
npm install

# 3. Copy env file
cp .env.example .env

# 4. Generate app key
php artisan key:generate

# 5. Setup database
php artisan migrate
php artisan db:seed

# 6. Link storage (untuk file uploads)
php artisan storage:link

# 7. Build assets
npm run build

# 8. Start dev server
php artisan serve
```

### Akses Aplikasi
- **Admin Panel**: `http://localhost:8000/admin`
- **Default Credentials**: Lihat output seeder atau `.env`

---

## 📂 Struktur Proyek

```
app/
├── Models/
│   ├── User.php                 # User model dengan relationship
│   ├── Employee.php             # Data karyawan
│   ├── Patrol.php               # Laporan patroli
│   ├── PatrolCheckpoint.php     # Checkpoint (foto + paraf)
│   ├── Location.php             # Area patrol dengan GPS
│   ├── Violation.php            # Jenis pelanggaran
│   ├── Action.php               # Tindakan/sanksi
│   ├── Shift.php                # Grup shift
│   └── ...
│
├── Filament/
│   └── Admin/
│       ├── Resources/
│       │   ├── PatrolResource.php       # CRUD patrol
│       │   ├── EmployeeResource.php     # CRUD karyawan
│       │   ├── LocationResource.php     # CRUD lokasi
│       │   └── ...
│       └── Pages/
│           ├── ChecksheetPatrol.php     # Page checksheet
│           ├── RekapTemuanPatrol.php    # Page rekap temuan
│           └── Dashboard.php
│
├── Events/
│   └── PatrolQrScanned.php      # Event saat QR scan
│
├── Http/Controllers/
│   └── ...                       # Custom controller jika ada
│
└── Console/Commands/
    └── DebugChecksheetGroup.php  # Utility command

database/
├── migrations/                  # Schema database
├── seeders/                     # Data seeder
└── factories/                   # Model factories

resources/
├── views/
│   ├── filament/
│   │   ├── forms/components/
│   │   │   └── qr-checkpoint.blade.php    # Checkpoint UI
│   │   ├── admin/pages/
│   │   │   ├── checksheet-patrol.blade.php
│   │   │   └── rekap-temuan-patrol.blade.php
│   │   └── ...
│   └── exports/
│       ├── checksheet-patrol.blade.php    # PDF export
│       └── rekap-temuan-patrol.blade.php
│
└── js/
    └── app.js                  # Alpine.js & Vite build

routes/
├── web.php                      # Web routes (export PDF/Excel)
└── api.php                      # API routes (jika ada)

config/
├── app.php
├── database.php
├── filament.php
├── permission.php
└── ...
```

---

## 🚀 Panduan Penggunaan

### 1. Membuat Laporan Patroli

#### Alur Umum:
1. **Scan QR Code** lokasi patrol (di aplikasi mobile atau web)
   - QR code di-generate dari menu **Lokasi**
   - Sistem mencatat UUID lokasi dan IP address

2. **Buka Form Patrol** (auto-redirect setelah scan QR)
   - **Step 1**: Pilih petugas/PIC, waktu, shift
   - **Step 2**: Lokasi sudah terisi otomatis, pilih apakah ada temuan
     - Jika **ADA TEMUAN**: Pilih karyawan pelanggar, jenis pelanggaran, deskripsi, foto
     - Jika **TIDAK ADA**: Langsung ke step 3
   - **Step 3**: Ambil **FOTO MUKA** (selfie) + **TANDA TANGAN** → Klik "✅ Selesai & Simpan"

3. **Klik "💾 Simpan Laporan Patroli"**
   - Foto muka & tanda tangan otomatis tersimpan di kolom `face_photo` & `signature` tabel `patrols`
   - Laporan tercatat di database

### 2. Melihat Checksheet Patrol

Lokasi: **Admin Panel → Patroli → Checksheet Patrol**

- Filter by: Tanggal, Shift, Area Lokasi
- Tampil: No, Tanggal, Shift, **Group Shift PIC**, Jam, Nama Petugas, Paraf
- **Export PDF** & **Export Excel** untuk dokumentasi

### 3. Melihat Rekap Temuan Patrol

Lokasi: **Admin Panel → Patroli → Rekap Temuan Patrol**

- Filter by: Tanggal, Shift, Group, Hanya Pelanggaran
- Tampil: Tanggal, Shift, Lokasi, Karyawan Pelanggar, Jenis Pelanggaran, Tindakan, Evidence (foto)
- **Export PDF** & **Export Excel** untuk laporan manajemen

### 4. Manajemen Master Data

- **Karyawan**: Kelola data karyawan, NIP, nama, shift group
- **Lokasi**: Buat lokasi baru, assign UUID, generate QR code
- **Pelanggaran**: Tambah jenis pelanggaran baru
- **Tindakan**: Tambah daftar tindakan/sanksi
- **Shift**: Kelola grup shift

---

## 🗄️ Database Schema

### Tabel Utama

**patrols**
```
id, user_id (PIC), employee_id (pelanggar/nullable), shift_id, location_id, 
violation_id (nullable), action_id (nullable), description, photos (array),
signature (longtext), face_photo, patrol_time, qr_scanned_at, qr_scanned_ip,
created_at, updated_at
```

**patrol_checkpoints** (deprecated di versi baru, pakai langsung ke patrols)
```
id, patrol_id, location_id, user_id, face_photo, signature, scanned_at
```

**employees**
```
id, user_id, nip, name, shfgroup (A/B/C/D), created_at, updated_at
```

**locations**
```
id, name, uuid (untuk QR), latitude, longitude, created_at, updated_at
```

**users**
```
id, name, email, password, role, avatar_url, created_at, updated_at
```

---

## 🔗 API & Endpoint

### Web Routes (Export)

**PDF Exports**
```
GET /admin/patrols/checksheet/export-pdf
    ?date_from=2026-05-01&date_until=2026-05-31&shift_id=1&location_id=2

GET /admin/patrols/rekap-temuan/export-pdf
    ?date_from=2026-05-01&date_until=2026-05-31&shift_id=1&shfgroup=A&only_violations=1
```

**Excel Exports**
```
GET /admin/patrols/checksheet/export-excel
    [same params as PDF]

GET /admin/patrols/rekap-temuan/export-excel
    [same params as PDF]
```

---

## ⚙️ Konfigurasi

### `.env` Penting

```env
APP_NAME=PatrolHR
APP_TIMEZONE=Asia/Jakarta

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=railway
DB_USERNAME=root
DB_PASSWORD=

FILAMENT_LOGGER_ENABLED=true
```

### Shift Configuration

Edit di kode:
- **Shift 1**: 07:00 - 15:00 (🟢 Hijau)
- **Shift 2**: 15:00 - 23:00 (🟡 Kuning)
- **Shift 3**: 23:00 - 07:00 (🔵 Biru)

---

## 🐛 Troubleshooting

### Error: "Carbon\Exceptions\InvalidFormatException - Not enough data"
**Solusi**: Pastikan semua field timestamp disimpan dengan format `Y-m-d H:i:s`. Check setter/getter di Patrol model.

### Error: "Column 'patrol_time' cannot be null"
**Solusi**: Di setter `setPatrolTimeAttribute()`, jika value kosong set ke `now()` (current timestamp UTC).

### Foto/Paraf tidak muncul di Checksheet
**Solusi**: 
- Pastikan checkpoint sudah completed (klik "✅ Selesai & Simpan" di step 3)
- Check storage path di `storage/app/public/checkpoint-face-photos/` dan `checkpoint-signatures/`
- Jalankan `php artisan storage:link` untuk link storage

### QR Code tidak terbaca
**Solusi**: 
- Pastikan lokasi sudah punya UUID (auto-generate saat create)
- Generate ulang QR code di menu Lokasi
- Gunakan mobile app atau QR reader yang support format PNG

### Export PDF blank
**Solusi**: 
- Check path di `resources/views/exports/`
- Pastikan DomPDF sudah terinstall: `composer require barryvdh/laravel-dompdf`
- Check folder permission: `storage/` dan `public/`

---

## 📞 Support & Maintenance

### Log Debugging
```bash
# Tail log real-time
tail -f storage/logs/laravel.log

# Clear log
php artisan log:clear
```

### Database Backup
```bash
# Backup
mysqldump -u root -p database_name > backup.sql

# Restore
mysql -u root -p database_name < backup.sql
```

### Reset Application
```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Reset database (HATI-HATI!)
php artisan migrate:fresh --seed
```

---

## 📝 Changelog

### v1.0.0 (Current)
- ✅ Form patrol 3-step wizard
- ✅ QR code validation
- ✅ Checkpoint foto + paraf digital
- ✅ Checksheet patrol with export
- ✅ Rekap temuan patrol with export
- ✅ Master data management
- ✅ Activity logging
- ✅ Role & permission system

---

