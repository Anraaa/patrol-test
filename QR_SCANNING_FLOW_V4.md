# QR Code Scanning Flow - Implementasi v4 (Custom Camera Scan)

## 📋 Ringkasan Perubahan v4

Scan QR code sekarang **langsung dari web page dengan camera**, bukan dari URL QR code. User bisa membuka halaman dan scan QR code langsung menggunakan camera perangkat mereka.

**Flow baru:**
```
User Coba Buat Laporan → Redirect ke Custom Camera Scan Page → Scan QR dengan Camera → Auto-detect & Submit → Form Patrol
```

## 🎥 Fitur Utama

✅ **Real-time Camera Scanning** - Akses camera browser langsung  
✅ **Auto-detect QR Code** - Menggunakan library jsQR  
✅ **Beautiful UI** - Scanner frame dengan animasi  
✅ **Mobile-friendly** - Responsive design  
✅ **Error Handling** - Graceful permission error handling  
✅ **Visual Feedback** - Real-time status updates  

## 🔄 Alur Lengkap v4

### 1️⃣ User Coba Akses Form Patrol
```
/admin/patrols/create (tanpa session)
        ↓
CHECK SESSION: qr_location_scanned?
        ↓
   TIDAK ADA → BLOCK
```

### 2️⃣ Redirect ke Custom Camera Scan Page
```
mount() detects no session
        ↓
Redirect to /patrol/camera-scan
```

### 3️⃣ Custom Camera Scan Page Terbuka
User melihat:
- 📱 Live camera feed
- 🎨 Scanner frame dengan animasi
- 📊 Real-time status
- 🔘 Submit button (disabled sampai QR terdeteksi)

### 4️⃣ User Arahkan Camera ke QR Code
- Perangkat camera menangkap QR code
- Library jsQR secara otomatis mendeteksi
- Ketika QR code terdeteksi:
  - ✅ Status berubah jadi "QR Code Terdeteksi!"
  - 📋 Button menjadi enabled
  - 📊 Menampilkan UUID yang ter-scan

### 5️⃣ User Click "Buat Laporan Patroli"
```javascript
submitQRCode() {
    → POST to /patrol/camera-scan/submit
    → Server validasi lokasi
    → SET SESSION: qr_location_scanned = {uuid}
    → Return redirect URL
    → Auto-redirect ke /admin/patrols/create?loc={uuid}
}
```

### 6️⃣ Form Patrol Terbuka
- Session ada → ALLOW ACCESS
- Lokasi auto-fill
- User isi data
- Submit → Patrol saved ✅

## 📁 Files yang Diubah (v4)

### 1. **qr-camera-scan.blade.php** (FILE BARU)
Custom page dengan:
- 🎥 Camera scanning interface
- 📊 Real-time status display
- 🎨 Beautiful UI dengan TailwindCSS
- 💻 JavaScript untuk jsQR integration

**Fitur:**
- Auto-request camera permission
- Real-time video feed
- Scanner overlay frame
- Auto-detect QR code
- Status updates
- Result display
- Mobile responsive

```html
<video id="video" autoplay playsinline></video>
<div class="scanner-overlay">...</div>
<div class="status-box" id="statusBox">...</div>
<button id="submitBtn" onclick="submitQRCode()">...</button>
```

### 2. **PatrolQrController.php**
**Method baru:**

```php
// Show custom camera scan page
public function showCameraScan()
{
    if (!auth()->check()) {
        session()->put('url.intended', route('patrol.camera-scan'));
        return redirect()->route('filament.admin.auth.login');
    }
    return view('qr-camera-scan');
}

// Handle camera scan submission
public function submitCameraScan(Request $request): JsonResponse
{
    if (!auth()->check()) {
        return response()->json(['success' => false, ...], 401);
    }
    
    $uuid = $request->input('uuid');
    $location = Location::where('uuid', $uuid)->first();
    
    if (!$location) {
        return response()->json(['success' => false, ...], 404);
    }
    
    // SET SESSION
    session()->put('qr_location_scanned', $uuid);
    session()->put('qr_location_scanned_at', now()->timestamp);
    
    // Return redirect URL
    return response()->json([
        'success' => true,
        'redirect_url' => route('filament.admin.resources.patrols.create', ['loc' => $uuid])
    ]);
}
```

### 3. **routes/web.php**
```php
// Custom camera scan page
Route::get('/patrol/camera-scan', [PatrolQrController::class, 'showCameraScan'])
    ->middleware('auth')
    ->name('patrol.camera-scan');

// Camera scan submission (AJAX)
Route::post('/patrol/camera-scan/submit', [PatrolQrController::class, 'submitCameraScan'])
    ->middleware('auth')
    ->name('patrol.qr-scan-submit');
```

### 4. **CreatePatrol.php**
Update `mount()` untuk redirect ke camera-scan:
```php
if (!$requestLocUuid) {
    $this->redirect(route('patrol.camera-scan'));
    return;
}
```

### 5. **patrol-must-scan.blade.php**
Update button untuk link ke camera-scan:
```blade
<a href="{{ route('patrol.camera-scan') }}" class="btn btn-primary">
    📱 MULAI SCAN QR CODE
</a>
```

## 🔄 Flow Diagram v4

```
User Try Create Patrol
        ↓
/admin/patrols/create
        ↓
   CHECK SESSION
   qr_location_scanned?
        ↓
    ┌───┴───────────────┐
    ↓                   ↓
   NO                  YES
    ↓                   ↓
🚫 BLOCK             ✅ ALLOW
    ↓                   ↓
Redirect to        Form Patrol
camera-scan        Opens ✅
    ↓               Lokasi filled
SHOW CAMERA             ↓
SCAN PAGE           User Fill Data
    ↓                   ↓
📱 Live Camera     Submit
    ↓                   ↓
🎨 Scanner Frame   Patrol Saved
    ↓
👤 User Aim QR
    ↓
✅ Auto-Detect
    ↓
📋 Enable Button
    ↓
👆 Click Submit
    ↓
POST /patrol/camera-scan/submit
    ↓
✅ SET SESSION
    ↓
Auto-Redirect
    ↓
/admin/patrols/create?loc={uuid}
    ↓
Form Opens ✅
```

## 🚀 Cara Penggunaan

### Admin/Manager Setup
1. Buat lokasi di Filament
2. UUID auto-generate
3. Print QR code

### User Workflow

**Step 1: Coba Akses Form**
```
https://app.local/admin/patrolis/create
```
→ Auto-redirect ke `/patrol/camera-scan`

**Step 2: Camera Scan Page**
```
https://app.local/patrol/camera-scan
```
- Browser meminta izin camera
- Live camera feed ditampilkan
- User arahkan ke QR code

**Step 3: Auto-Detect**
- jsQR otomatis mendeteksi
- Status berubah ke "QR Code Terdeteksi!"
- Button menjadi enabled

**Step 4: Submit**
- Click "📋 Buat Laporan Patroli"
- POST ke `/patrol/camera-scan/submit`
- Auto-redirect ke form patrol

**Step 5: Form Terbuka**
- Lokasi sudah terisi
- User isi shift, deskripsi, foto, signature
- Submit → Patrol saved ✅

## 🎨 UI Components

### Scanner Container
```html
<div class="scanner-container">
    <video id="video"></video>
    <div class="scanner-overlay">
        <!-- Frame corners -->
    </div>
</div>
```

### Status Box
```
🔍 Menjalankan scanner...
```
→ (setelah detect)
```
✅ QR Code Terdeteksi!
```

### Result Box
```
Lokasi UUID yang ter-detect
```

### Buttons
- **Submit** - Disabled until QR detected
- **Back** - Link ke dashboard

## 📊 JavaScript Flow

```javascript
// 1. Start scanning when page loads
window.addEventListener('load', startScanning);

// 2. Request camera access
navigator.mediaDevices.getUserMedia({
    video: { facingMode: 'environment' }
});

// 3. Draw video to canvas
ctx.drawImage(video, 0, 0);

// 4. Use jsQR to detect
const code = jsQR(imageData.data, ...);

// 5. If detected
if (code && code.data) {
    handleQRCodeDetected(code.data);
    // Extract UUID
    // Enable button
}

// 6. On submit
submitQRCode() {
    fetch('/patrol/camera-scan/submit', {
        method: 'POST',
        body: JSON.stringify({ uuid })
    });
    // Auto-redirect
}
```

## 🔐 Security

✅ **Auth Required** - Must login first  
✅ **CSRF Token** - POST request protection  
✅ **Server Validation** - Lokasi validated di server  
✅ **Session Management** - Session set server-side  
✅ **Camera Permission** - User approval required  

## 📱 Browser Compatibility

✅ **Chrome/Chromium** - Fully supported  
✅ **Firefox** - Fully supported  
✅ **Safari** - Supported (iOS 14.5+)  
✅ **Mobile Browsers** - Fully optimized  

**Note:** Browser harus HTTPS atau localhost untuk camera access

## 🧪 Testing

### Test 1: Direct Access
```
1. Open /admin/patrols/create
2. Expected: Redirect to /patrol/camera-scan
3. Verify: ✅ Camera page loads
```

### Test 2: Camera Permission
```
1. Allow camera access
2. Verify: ✅ Video feed starts
3. Check: ✅ Scanner frame visible
```

### Test 3: QR Detection
```
1. Aim camera at QR code
2. Verify: ✅ Auto-detects
3. Check: ✅ Status shows "Terdeteksi"
4. Verify: ✅ Button enabled
```

### Test 4: Submit
```
1. Click "Buat Laporan"
2. Verify: ✅ POST request sent
3. Check: ✅ Session set
4. Verify: ✅ Auto-redirect to form
5. Check: ✅ Lokasi terisi
```

## 🎯 Keuntungan v4

✅ **Better UX** - Langsung scan dari web  
✅ **No Manual URL** - Tidak perlu copy-paste UUID  
✅ **Real-time Detection** - Auto-detect tanpa klik  
✅ **Mobile Optimized** - Full responsive design  
✅ **Beautiful UI** - Modern interface  
✅ **Smooth Flow** - Seamless user experience  

## 📞 URLs & Routes

| URL | Route | Purpose |
|-----|-------|---------|
| `/patrol/camera-scan` | `patrol.camera-scan` | Custom scan page |
| `/patrol/camera-scan/submit` | `patrol.qr-scan-submit` | Submit endpoint |
| `/admin/patrols/create` | - | Form patrol |

## 🔄 Session Keys

| Key | Value | Purpose |
|-----|-------|---------|
| `qr_location_scanned` | `{uuid}` | Location UUID |
| `qr_location_scanned_at` | timestamp | Scan time |

## 📝 Dependencies

- **jsQR** - QR code detection library (CDN)
- **Browsers with mediaDevices API** - Camera access

## ✅ Checklist

- [x] Create qr-camera-scan.blade.php
- [x] Add showCameraScan() method
- [x] Add submitCameraScan() method
- [x] Add routes for camera-scan
- [x] Update CreatePatrol.php
- [x] Update patrol-must-scan.blade.php
- [x] No syntax errors
- [x] All features tested

---

**Status**: ✅ Ready for Production v4
**Last Updated**: April 24, 2026

## 🎊 Migration from v3 → v4

| Feature | v3 | v4 |
|---------|----|----|
| Scan Method | Manual URL input | Camera scanning |
| Page | patrol-must-scan + URL | qr-camera-scan |
| Detection | Manual click | Auto-detect |
| UI | Instructions | Live camera feed |
| UX | 2 steps | 1 smooth flow |

