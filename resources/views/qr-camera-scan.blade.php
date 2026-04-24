<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan QR Code Lokasi</title>
    <script src="https://unpkg.com/html5-qrcode"></script>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .container {
            max-width: 500px;
            width: 100%;
        }

        .card {
            background: #fff;
            border-radius: 1.5rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .header-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .header-subtitle {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .content {
            padding: 2rem;
        }

        .scanner-container {
            position: relative;
            background: #000;
            border-radius: 1rem;
            overflow: hidden;
            margin-bottom: 1.5rem;
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .scanner-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 250px;
            height: 250px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 0.75rem;
            box-shadow: inset 0 0 20px rgba(255, 255, 255, 0.1);
            pointer-events: none;
        }

        .scanner-overlay::before,
        .scanner-overlay::after {
            content: '';
            position: absolute;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .scanner-overlay::before {
            width: 30px;
            height: 30px;
            top: -2px;
            left: -2px;
            border-right: 3px solid transparent;
            border-bottom: 3px solid transparent;
            border-radius: 0.5rem 0 0 0;
        }

        .scanner-overlay::after {
            width: 30px;
            height: 30px;
            top: -2px;
            right: -2px;
            border-left: 3px solid transparent;
            border-bottom: 3px solid transparent;
            border-radius: 0 0.5rem 0 0;
        }

        .scanner-corner {
            position: absolute;
            width: 30px;
            height: 30px;
            border: 3px solid #667eea;
        }

        .scanner-corner.bottom-left {
            bottom: -2px;
            left: -2px;
            border-right: transparent;
            border-top: transparent;
            border-radius: 0 0 0 0.5rem;
        }

        .scanner-corner.bottom-right {
            bottom: -2px;
            right: -2px;
            border-left: transparent;
            border-top: transparent;
            border-radius: 0 0 0.5rem 0;
        }

        .status-box {
            background: #f9fafb;
            border-radius: 0.75rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
            text-align: center;
            min-height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .status-icon {
            font-size: 1.5rem;
            margin-right: 0.5rem;
        }

        .status-text {
            font-size: 0.9rem;
            color: #6b7280;
        }

        .status-text.scanning {
            color: #059669;
            font-weight: 600;
        }

        .status-text.success {
            color: #059669;
        }

        .status-text.error {
            color: #dc2626;
        }

        .result-box {
            display: none;
            background: #f0fdf4;
            border: 2px solid #86efac;
            border-radius: 0.75rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .result-box.show {
            display: block;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .result-title {
            font-weight: 600;
            color: #15803d;
            margin-bottom: 0.5rem;
        }

        .result-value {
            font-size: 0.9rem;
            color: #166534;
        }

        .actions {
            display: flex;
            gap: 1rem;
            flex-direction: column;
        }

        .btn {
            padding: 0.875rem 1.5rem;
            border-radius: 0.75rem;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
        }

        .btn-primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #374151;
        }

        .btn-secondary:hover {
            background: #d1d5db;
        }

        .info-box {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-top: 1rem;
        }

        .info-box-title {
            font-weight: 600;
            color: #1e40af;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .info-box-text {
            font-size: 0.85rem;
            color: #1e3a8a;
            line-height: 1.5;
        }

        .permissions-needed {
            text-align: center;
            padding: 2rem 1rem;
        }

        .permissions-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .permissions-text {
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        @media (max-width: 640px) {
            .header {
                padding: 1.5rem;
            }

            .header-title {
                font-size: 1.25rem;
            }

            .content {
                padding: 1.5rem;
            }

            .scanner-overlay {
                width: 180px;
                height: 180px;
            }

            .scanner-corner {
                width: 24px;
                height: 24px;
            }
        }

        .hidden {
            display: none !important;
        }

        .loader {
            display: inline-block;
            width: 12px;
            height: 12px;
            background-color: #667eea;
            border-radius: 50%;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
            100% {
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <div class="header-title">📱 Scan QR Code</div>
                <div class="header-subtitle">Pindai QR code lokasi patroli Anda</div>
            </div>

            <div class="content">
                <!-- Scanner Container -->
                <div class="scanner-container" id="scannerContainer">
                    <div id="video"></div>
                    <div class="scanner-overlay">
                        <div class="scanner-corner bottom-left"></div>
                        <div class="scanner-corner bottom-right"></div>
                    </div>
                </div>

                <!-- Status Box -->
                <div class="status-box">
                    <span class="status-icon" id="statusIcon">🔍</span>
                    <span class="status-text scanning" id="statusText">Menjalankan scanner...</span>
                </div>

                <!-- Result Box -->
                <div class="result-box" id="resultBox">
                    <div class="result-title">✅ QR Code Terdeteksi!</div>
                    <div class="result-value" id="resultValue"></div>
                </div>

                <!-- Permissions Error -->
                <div class="permissions-needed hidden" id="permissionsError">
                    <div class="permissions-icon">🚫</div>
                    <div class="permissions-text">
                        <strong>Akses Kamera Ditolak</strong><br>
                        Silakan izinkan akses kamera di browser Anda untuk melanjutkan scanning.
                    </div>
                    <button onclick="location.reload()" class="btn btn-primary">🔄 Coba Lagi</button>
                </div>

                <!-- Actions -->
                <div class="actions" id="actions">
                    <button id="submitBtn" class="btn btn-primary" disabled onclick="submitQRCode()">
                        📋 Buat Laporan Patroli
                    </button>
                    <a href="{{ route('filament.admin.pages.dashboard') }}" class="btn btn-secondary">
                        ← Kembali
                    </a>
                </div>

                <!-- Info Box -->
                <div class="info-box">
                    <div class="info-box-title">💡 Tips Scanning</div>
                    <div class="info-box-text">
                        • Posisikan QR code dalam frame<br>
                        • Pastikan cahaya cukup<br>
                        • Tahan perangkat dengan stabil<br>
                        • QR code akan otomatis terdeteksi
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const statusText = document.getElementById('statusText');
        const statusIcon = document.getElementById('statusIcon');
        const resultBox = document.getElementById('resultBox');
        const resultValue = document.getElementById('resultValue');
        const submitBtn = document.getElementById('submitBtn');
        const permissionsError = document.getElementById('permissionsError');
        const scannerContainer = document.getElementById('scannerContainer');
        const video = document.getElementById('video');

        let detectedQRCode = null;
        let html5QrcodeScanner = null;

        // Start QR code scanning
        async function startScanning() {
            try {
                console.log('📱 Initializing html5-qrcode scanner...');
                
                // Create scanner instance
                html5QrcodeScanner = new Html5Qrcode('video', {
                    formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE],
                    verbose: false,
                    useBarCodeDetectorIfAvailable: true,
                });

                const config = {
                    fps: 10,
                    qrbox: { width: 250, height: 250 },
                    aspectRatio: 1.0,
                    disableFlip: false
                };

                // Start scanning
                await html5QrcodeScanner.start(
                    { facingMode: 'environment' },
                    config,
                    onScanSuccess,
                    onScanError
                );
                
                console.log('✅ Camera access granted and scanner started');
                statusIcon.textContent = '🔍';
                statusText.textContent = 'Menjalankan scanner...';
                
            } catch (error) {
                console.error('❌ Error initializing scanner:', error);
                handlePermissionError(error);
            }
        }

        function onScanSuccess(decodedText, decodedResult) {
            console.log('✅ QR Code detected:', decodedText);
            
            if (detectedQRCode && detectedQRCode === decodedText) {
                return; // Already processed this QR code
            }
            
            detectedQRCode = decodedText;
            handleQRCodeDetected(decodedText);
        }

        function onScanError(errorMessage) {
            // Ignore scanning errors, just keep trying
        }

        function handleQRCodeDetected(qrData) {
            console.log('✅ QR Data received:', qrData);

            // If QR data is a full URL containing /scan-qr/, redirect there directly
            if (qrData.includes('/scan-qr/')) {
                console.log('✅ Redirecting to QR URL:', qrData);
                // Stop camera before redirecting
                if (html5QrcodeScanner) {
                    html5QrcodeScanner.stop().catch(() => {});
                }
                window.location.href = qrData;
                return;
            }

            // Otherwise treat it as a UUID and build the URL
            const uuidMatch = qrData.match(/([0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12})/i);
            const uuid = uuidMatch ? uuidMatch[1] : qrData;
            console.log('✅ UUID extracted:', uuid);

            // Show detected result and enable submit
            statusIcon.textContent = '✅';
            statusText.textContent = 'QR Code Terdeteksi!';
            statusText.classList.remove('scanning');
            statusText.classList.add('success');

            resultValue.textContent = uuid;
            resultBox.classList.add('show');

            submitBtn.disabled = false;
            window.detectedUUID = uuid;
        }

        function handlePermissionError(error) {
            console.error('Camera error:', error);
            scannerContainer.classList.add('hidden');
            document.getElementById('actions').classList.add('hidden');
            permissionsError.classList.remove('hidden');

            statusText.textContent = 'Akses kamera ditolak: ' + error.message;
            statusIcon.textContent = '❌';
        }

        function submitQRCode() {
            if (!window.detectedUUID) {
                alert('❌ Belum ada QR code yang terdeteksi');
                return;
            }

            // Redirect to GPS validation page for this location UUID
            submitBtn.disabled = true;
            submitBtn.textContent = '⏳ Mengalihkan...';

            if (html5QrcodeScanner) {
                html5QrcodeScanner.stop().catch(() => {});
            }

            window.location.href = '/scan-qr/' + window.detectedUUID;
        }

        // Start scanning when page loads
        window.addEventListener('load', startScanning);

        // Stop camera when page unloads
        window.addEventListener('beforeunload', () => {
            if (html5QrcodeScanner) {
                html5QrcodeScanner.stop().catch(err => console.log('Error stopping scanner:', err));
            }
        });
    </script>
</body>
</html>
