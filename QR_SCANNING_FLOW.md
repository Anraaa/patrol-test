# QR Code Scanning Flow - Implementasi Baru

## 📋 Ringkasan Perubahan

Saya telah membuat flow QR scanning baru di mana user **harus scan QR terlebih dahulu** sebelum bisa isi form patrol. Alur kerjanya adalah:

```
Scan QR → Validasi → Cek Login → Redirect ke Form
```

## 🔄 Alur Lengkap

### 1️⃣ User Scan QR Code
User membuka link QR atau mengakses endpoint:
```
https://yourapp.local/scan-qr/{token}
```

### 2️⃣ Sistem Validasi QR
- ✅ Cek QR token ada di database
- ✅ Cek QR belum pernah di-scan sebelumnya
- ✅ Jika valid → lanjut ke step 3
- ❌ Jika tidak valid → tampilkan error page

### 3️⃣ Cek Status Login
- **Belum login** → Redirect ke halaman login Filament
  - Setelah login → Redirect balik otomatis ke form patrol
- **Sudah login** → Redirect langsung ke form patrol

### 4️⃣ User Isi Form Patrol
- Form patrol muncul dengan notifikasi "QR code tervalidasi ✅"
- User isi semua data seperti biasa (lokasi, shift, deskripsi, foto, signature, dll)

### 5️⃣ Simpan Patrol dengan QR Token
- Patrol disimpan dengan QR token yang sudah di-validasi
- Data QR (`qr_scanned_at`, `qr_scanned_ip`) tercatat

## 📁 Files yang Diubah

### 1. `app/Http/Controllers/PatrolQrController.php`
**Ditambah method baru:** `publicScan(string $token)`
- Validasi QR token
- Cek auth status
- Redirect sesuai kondisi

```php
public function publicScan(string $token)
{
    // 1. Validate QR token
    $patrol = Patrol::where('qr_code_token', $token)->first();
    
    // 2. Check if authenticated
    if (!auth()->check()) {
        session()->put('url.intended', route('patrol.qr-scan', ['token' => $token]));
        return redirect()->route('filament.admin.auth.login');
    }
    
    // 3. Redirect to patrol form
    session()->put('qr_scan_token', $token);
    return redirect()->route('filament.admin.resources.patrols.create');
}
```

### 2. `routes/web.php`
**Ditambah route baru:**
```php
Route::get('/scan-qr/{token}', [PatrolQrController::class, 'publicScan'])
    ->name('patrol.qr-scan');
```

### 3. `app/Filament/Admin/Resources/PatrolResource/Pages/CreatePatrol.php`
**Perubahan di method `mount()`:**
- Cek session `qr_scan_token`
- Validasi token
- Tampilkan notifikasi jika dari QR scan

**Perubahan di `mutateFormDataBeforeCreate()`:**
- Tambah `qr_code_token` dari session ke data patrol

### 4. `resources/views/qr-scan-result.blade.php` (FILE BARU)
View untuk menampilkan hasil validasi QR:
- Pesan sukses atau error
- Informasi patrol (jika berhasil)
- Button redirect ke dashboard atau form

## 🚀 Cara Penggunaan

### Generate QR Code (untuk admin/manager)
```bash
# POST /api/qr/generate-token (harus auth)
# Response:
{
  "token": "abc123xyz...",
  "scan_url": "https://yourapp.local/scan-qr/abc123xyz..."
}
```

### User Scan QR
1. User buka link QR atau scan dengan camera:
   ```
   https://yourapp.local/scan-qr/{token}
   ```

2. Sistem otomatis:
   - Validasi QR token
   - Jika belum login → ke login page
   - Jika sudah login → ke form patrol

3. User isi form dan submit

## 📊 Flow Diagram

```
User Scan QR
    ↓
GET /scan-qr/{token}
    ↓
QR Valid? ──NO─→ Show Error Page
    ↓ YES
QR Already Scanned? ──YES─→ Show Warning Page
    ↓ NO
User Authenticated? 
    ├─ NO  → Redirect to /admin/login
    │        (after login → automatic redirect back)
    │
    └─ YES → Redirect to /admin/patrols/create
            (with qr_scan_token in session)
                    ↓
            Show Form Patrol
            (with QR validated notification)
                    ↓
            User Fill Form & Submit
                    ↓
            Patrol Saved with qr_code_token
```

## 🔧 Konfigurasi

Tidak ada konfigurasi tambahan yang diperlukan. Sistem otomatis menggunakan:
- Token random 32 karakter (dari `generateToken()`)
- Filament default login route
- Filament default patrol create route

## ✅ Testing Checklist

- [ ] Generate QR token via API
- [ ] Access `/scan-qr/{token}` without login → redirect to login
- [ ] Login successfully → automatic redirect to form patrol
- [ ] Form patrol show "QR code tervalidasi" notification
- [ ] Fill form and submit → patrol saved with QR token
- [ ] Try scanning same QR twice → show warning message

## 📝 Catatan Penting

1. **QR Token Storage**: Token disimpan di column `qr_code_token` di table `patrols`
2. **Auto Redirect**: Setelah login, user otomatis redirect ke form patrol
3. **Session Cleanup**: Session `qr_scan_token` otomatis dihapus setelah diambil
4. **Validation**: QR validation cek:
   - Token ada di database
   - Token belum di-scan (`qr_scanned_at` null)

## 🐛 Troubleshooting

**Problem**: User login tapi tidak redirect ke form patrol
- **Solution**: Cek session `qr_scan_token` tersimpan dengan benar

**Problem**: Notifikasi QR tidak muncul
- **Solution**: Cek `CreatePatrol::mount()` eksekusi dengan benar

**Problem**: QR token tidak tersimpan di patrol
- **Solution**: Cek `mutateFormDataBeforeCreate()` tambah token ke data

## 📞 Support

Untuk pertanyaan atau modifikasi lebih lanjut, silakan tanyakan!
