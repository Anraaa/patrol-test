# QR Code Scanning Flow - Implementasi Baru

## 📋 Ringkasan Perubahan

Flow QR scanning sekarang adalah: **User HARUS scan QR lokasi terlebih dahulu** sebelum bisa membuat laporan patrol. Alur kerjanya adalah:

```
Scan QR Lokasi → Validasi Lokasi → Cek Login → Redirect ke Form Patrol
```

## 🔄 Alur Lengkap

### 1️⃣ User Scan QR Lokasi
User membuka link QR atau mengakses endpoint dengan UUID lokasi:
```
https://yourapp.local/scan-qr/{location-uuid}
```

### 2️⃣ Sistem Validasi QR Lokasi
- ✅ Cek lokasi ada di database (berdasarkan UUID)
- ✅ Tampilkan informasi lokasi (nama, koordinat GPS, radius)
- ✅ Jika valid → lanjut ke step 3
- ❌ Jika tidak valid → tampilkan error page

### 3️⃣ Cek Status Login
- **Belum login** → Redirect ke halaman login Filament
  - Setelah login → Redirect balik otomatis ke form patrol dengan lokasi terisi
- **Sudah login** → Redirect langsung ke form patrol dengan lokasi terisi

### 4️⃣ User Isi Form Patrol
- Form patrol muncul dengan lokasi sudah terisi
- Tampilkan notifikasi "📍 Lokasi terdeteksi dari QR Code"
- User isi semua data lainnya (shift, deskripsi, foto, signature, dll)

### 5️⃣ Simpan Laporan Patrol
- Laporan patrol disimpan dengan lokasi dari QR
- Jika ada checkpoint data, simpan juga ke PatrolCheckpoint

## 📁 Files yang Diubah

### 1. `app/Http/Controllers/PatrolQrController.php`
**Update method:** `publicScan(string $uuid)` - terima UUID lokasi (bukan token patrol)
- Validasi lokasi exist di database
- Cek auth status
- Redirect ke form patrol dengan query param `?loc={uuid}`

```php
public function publicScan(string $uuid)
{
    // 1. Validate location exists by UUID
    $location = Location::where('uuid', $uuid)->first();
    
    // 2. If not authenticated → redirect to login
    if (!auth()->check()) {
        session()->put('url.intended', route('patrol.qr-scan', ['uuid' => $uuid]));
        return redirect()->route('filament.admin.auth.login');
    }
    
    // 3. Redirect to patrol form with location pre-filled
    return redirect()->route('filament.admin.resources.patrols.create', ['loc' => $uuid]);
}
```

### 2. `routes/web.php`
**Update route:**
```php
Route::get('/scan-qr/{uuid}', [PatrolQrController::class, 'publicScan'])
    ->name('patrol.qr-scan');
```

### 3. `app/Filament/Admin/Resources/PatrolResource/Pages/CreatePatrol.php`
**Perubahan di method `mount()`:**
- Terima UUID lokasi dari query param `?loc={uuid}`
- Validasi lokasi exist
- Auto-fill lokasi ke form
- Tampilkan notifikasi

**Dihapus:**
- Logic untuk `qr_scan_token` session
- Property `$qrScanToken`
- Logic untuk add `qr_code_token` saat create patrol

### 4. `resources/views/qr-scan-result.blade.php`
Update view untuk menampilkan informasi lokasi:
- Nama lokasi
- Koordinat GPS (jika ada)
- Radius verifikasi

## 🚀 Cara Penggunaan

### Generate QR Code untuk Lokasi
QR code sudah otomatis di-generate di model Location:
```php
Location->getQrContentAttribute() 
// Output: https://yourapp.local/scan-qr/{location-uuid}
```

### Admin/Manager Buat Lokasi
```bash
# Di Filament admin panel
1. Buka Resources > Lokasi
2. Buat lokasi baru dengan nama, koordinat GPS (optional), radius
3. Location UUID auto-generate
4. QR code auto-generate dari UUID
5. Print QR code atau share link
```

### User Scan QR Lokasi
1. User buka link QR atau scan dengan camera:
   ```
   https://yourapp.local/scan-qr/{location-uuid}
   ```

2. Sistem validasi lokasi:
   - Valid → Show lokasi info
   - Invalid → Show error message

3. Jika belum login:
   - Redirect ke `/admin/login`
   - Setelah login → auto redirect ke form patrol dengan lokasi terisi

4. Jika sudah login:
   - Langsung redirect ke form patrol dengan lokasi terisi

5. User isi form patrol:
   - Lokasi sudah terisi dari QR
   - Isi data lainnya
   - Submit

6. Laporan patrol disimpan dengan lokasi dari QR

## 📊 Flow Diagram

```
┌──────────────────────────────────┐
│ User Scan QR Lokasi              │
│ /scan-qr/{location-uuid}         │
└──────────────┬───────────────────┘
               ↓
      ✓ Validate Location
               ↓
      Location Valid?
        ├─ NO  → Show Error Page
        └─ YES ↓
           User Authenticated? 
               ├─ NO  → Redirect to /admin/login
               │        (after login → automatic redirect back)
               │
               └─ YES → Redirect to /admin/patrols/create?loc={uuid}
                             ↓
                   Show Form Patrol
                   (lokasi sudah terisi)
                             ↓
                   User Fill Form & Submit
                             ↓
                   Patrol Saved with Location
```

## 🧪 Testing Checklist

- [ ] Admin buat lokasi baru dengan UUID
- [ ] Copy QR URL: `/scan-qr/{uuid}`
- [ ] Test scan tanpa login → redirect ke login page
- [ ] Login dan kembali → form patrol muncul dengan lokasi terisi
- [ ] Fill form dan submit → patrol tersimpan
- [ ] Verifikasi lokasi terisi di database

## 📝 Catatan Penting

1. **Location UUID**: Auto-generate saat membuat lokasi baru di Filament
2. **QR URL**: Auto-generate dari method `getQrContentAttribute()` di Location model
3. **Auto Redirect**: Setelah login, user otomatis redirect ke form patrol dengan lokasi terisi
4. **Lokasi Validation**: Hanya cek lokasi ada di database (berdasarkan UUID)
5. **No QR Token**: Flow ini tidak menggunakan `qr_code_token`, hanya lokasi UUID

## API Endpoints (Masih Ada)

Endpoint ini masih bisa digunakan untuk checkpoint validation:
```bash
# Generate QR token untuk patrol (harus auth)
POST /api/qr/generate-token

# Validate QR token (harus auth)
POST /api/qr/validate/{token}
```

Endpoint ini untuk scanning lokasi (public):
```bash
# Scan QR lokasi
GET /scan-qr/{location-uuid}
```

## 🔄 Perbedaan Workflow Lama vs Baru

### Workflow Lama:
1. User create patrol form
2. User fill all data (lokasi, shift, foto, signature)
3. Submit → patrol saved

### Workflow Baru:
1. User scan QR lokasi terlebih dahulu
2. Redirect ke form patrol (lokasi sudah terisi)
3. User fill sisa data (shift, foto, signature)
4. Submit → patrol saved with location from QR

## 📞 Support

Untuk pertanyaan atau modifikasi lebih lanjut, silakan tanyakan!
