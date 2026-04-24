<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validasi Lokasi Patroli</title>
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
            max-width: 480px;
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
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
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

        /* Location info card */
        .location-card {
            background: #f8f9ff;
            border: 1px solid #e0e7ff;
            border-radius: 1rem;
            padding: 1.25rem;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .location-icon {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .location-name {
            font-size: 1.15rem;
            font-weight: 700;
            color: #1e1b4b;
            margin-bottom: 0.35rem;
        }

        .location-radius {
            font-size: 0.85rem;
            color: #6b7280;
        }

        /* State boxes */
        .state-box {
            border-radius: 1rem;
            padding: 1.75rem 1.5rem;
            text-align: center;
            margin-bottom: 1.25rem;
        }

        .state-checking {
            background: #f0f4ff;
            border: 2px solid #c7d2fe;
        }

        .state-valid {
            background: #f0fdf4;
            border: 2px solid #86efac;
        }

        .state-invalid {
            background: #fef2f2;
            border: 2px solid #fca5a5;
        }

        .state-error {
            background: #fffbeb;
            border: 2px solid #fcd34d;
        }

        .state-icon {
            font-size: 3rem;
            margin-bottom: 0.75rem;
            display: block;
        }

        .state-icon.spinning {
            display: inline-block;
            animation: spin 1.5s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }

        .state-title {
            font-size: 1.15rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #111827;
        }

        .state-desc {
            font-size: 0.9rem;
            color: #6b7280;
            line-height: 1.5;
            margin-bottom: 1rem;
        }

        .distance-badge {
            display: inline-block;
            padding: 0.4rem 1rem;
            border-radius: 2rem;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .distance-badge.valid {
            background: #dcfce7;
            color: #15803d;
        }

        .distance-badge.invalid {
            background: #fee2e2;
            color: #b91c1c;
        }

        .auth-notice {
            background: #fffbeb;
            border: 1px solid #fcd34d;
            border-radius: 0.5rem;
            padding: 0.65rem 1rem;
            font-size: 0.85rem;
            color: #92400e;
            margin-bottom: 1rem;
        }

        .countdown {
            font-size: 0.9rem;
            color: #059669;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        /* Buttons */
        .btn {
            display: block;
            width: 100%;
            padding: 0.875rem 1.5rem;
            border-radius: 0.75rem;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
            margin-bottom: 0.75rem;
        }

        .btn:last-child {
            margin-bottom: 0;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
        }

        .btn-outline {
            background: transparent;
            color: #667eea;
            border: 2px solid #667eea;
        }

        .btn-outline:hover {
            background: #667eea;
            color: white;
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #6b7280;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
        }

        /* Progress pulse indicator */
        .pulse-indicator {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
        }

        .pulse-dot {
            width: 10px;
            height: 10px;
            background: #667eea;
            border-radius: 50%;
            animation: pulseDot 1.4s ease-in-out infinite;
        }

        .pulse-dot:nth-child(2) { animation-delay: 0.2s; }
        .pulse-dot:nth-child(3) { animation-delay: 0.4s; }

        @keyframes pulseDot {
            0%, 80%, 100% { transform: scale(0.6); opacity: 0.4; }
            40% { transform: scale(1); opacity: 1; }
        }

        .back-section {
            margin-top: 0.5rem;
        }

        .hidden {
            display: none !important;
        }

        @media (max-width: 480px) {
            .content { padding: 1.5rem; }
            .header  { padding: 1.5rem; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <div class="header-title">📍 Validasi Lokasi</div>
                <div class="header-subtitle">Memverifikasi jarak Anda dari lokasi patroli</div>
            </div>

            <div class="content">

                {{-- Location Info --}}
                <div class="location-card">
                    <div class="location-icon">🏢</div>
                    <div class="location-name">{{ $location->name }}</div>
                    @if($location->radius_meters)
                        <div class="location-radius">Radius yang diizinkan: {{ $location->radius_meters }} meter</div>
                    @endif
                </div>

                {{-- State: Checking GPS --}}
                <div id="stateChecking" class="state-box state-checking">
                    <div class="pulse-indicator">
                        <div class="pulse-dot"></div>
                        <div class="pulse-dot"></div>
                        <div class="pulse-dot"></div>
                    </div>
                    <div class="state-title">Memeriksa Lokasi Anda</div>
                    <div class="state-desc">Sedang mendapatkan GPS dan memvalidasi jarak ke lokasi patroli...</div>
                </div>

                {{-- State: Valid --}}
                <div id="stateValid" class="state-box state-valid hidden">
                    <span class="state-icon">✅</span>
                    <div class="state-title">Lokasi Valid!</div>
                    <div class="state-desc">Anda berada dalam radius lokasi patroli yang diizinkan.</div>
                    <div class="distance-badge valid" id="distanceBadgeValid"></div>
                    <div id="authNotice"></div>
                    <div class="countdown hidden" id="countdownBox">
                        Mengalihkan dalam <span id="countdownNum">3</span> detik...
                    </div>
                    <button onclick="doRedirect()" class="btn btn-primary" id="redirectBtn">
                        Lanjutkan →
                    </button>
                </div>

                {{-- State: Invalid Distance --}}
                <div id="stateInvalid" class="state-box state-invalid hidden">
                    <span class="state-icon">❌</span>
                    <div class="state-title">Lokasi Tidak Valid</div>
                    <div class="state-desc" id="invalidDesc">Anda terlalu jauh dari lokasi patroli.</div>
                    <div class="distance-badge invalid" id="distanceBadgeInvalid"></div>
                    <button onclick="retryValidation()" class="btn btn-outline">
                        🔄 Coba Lagi
                    </button>
                </div>

                {{-- State: GPS Error --}}
                <div id="stateGpsError" class="state-box state-error hidden">
                    <span class="state-icon">📵</span>
                    <div class="state-title">GPS Tidak Tersedia</div>
                    <div class="state-desc" id="gpsErrorDesc">
                        Tidak dapat mendapatkan lokasi Anda. Pastikan GPS aktif dan izinkan akses lokasi di browser.
                    </div>
                    <button onclick="retryValidation()" class="btn btn-outline">
                        🔄 Coba Lagi
                    </button>
                </div>

                {{-- Back Button --}}
                <div class="back-section">
                    @if($isAuthenticated)
                        <a href="{{ route('filament.admin.pages.dashboard') }}" class="btn btn-secondary">← Kembali ke Dashboard</a>
                    @else
                        <a href="/" class="btn btn-secondary">← Kembali</a>
                    @endif
                </div>

            </div>
        </div>
    </div>

    <script>
        const VALIDATE_URL = '/scan-qr/{{ $location->uuid }}/validate-gps';
        const CSRF_TOKEN   = '{{ csrf_token() }}';

        let redirectUrl      = null;
        let countdownInterval = null;

        // ── Entry point ─────────────────────────────────────────────────────────
        window.addEventListener('load', startValidation);

        function startValidation() {
            showState('checking');

            if (!navigator.geolocation) {
                showState('gpsError');
                document.getElementById('gpsErrorDesc').textContent =
                    'Browser Anda tidak mendukung geolocation. Gunakan browser yang lebih baru.';
                return;
            }

            navigator.geolocation.getCurrentPosition(
                onGpsSuccess,
                onGpsError,
                { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
            );
        }

        // ── GPS callbacks ────────────────────────────────────────────────────────
        async function onGpsSuccess(position) {
            const { latitude, longitude } = position.coords;

            try {
                const response = await fetch(VALIDATE_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                    },
                    body: JSON.stringify({ latitude, longitude }),
                });

                if (!response.ok) {
                    throw new Error(`Server error: ${response.status} ${response.statusText}`);
                }

                const data = await response.json();

                if (data.valid) {
                    window.location.href = data.redirect_url;
                } else {
                    showInvalidState(data);
                }
            } catch (err) {
                showState('gpsError');
                document.getElementById('gpsErrorDesc').innerHTML =
                    'Gagal menghubungi server untuk validasi jarak.<br><small style="color:#92400e">' + err.message + '</small>';
            }
        }

        function onGpsError(error) {
            showState('gpsError');
            const messages = {
                [error.PERMISSION_DENIED]:    'Akses lokasi ditolak. Silakan izinkan akses lokasi di pengaturan browser Anda.',
                [error.POSITION_UNAVAILABLE]: 'Informasi lokasi tidak tersedia. Pastikan GPS perangkat Anda aktif.',
                [error.TIMEOUT]:              'Waktu habis saat mendapatkan lokasi GPS. Coba lagi.',
            };
            document.getElementById('gpsErrorDesc').textContent =
                messages[error.code] || 'Terjadi kesalahan saat mendapatkan lokasi.';
        }

        // ── State renderers ──────────────────────────────────────────────────────
        function showValidState(data) {
            document.getElementById('distanceBadgeValid').textContent =
                `📏 Jarak Anda: ${data.distance}m — Radius: ${data.radius}m`;

            if (!data.is_authenticated) {
                document.getElementById('authNotice').innerHTML =
                    '<div class="auth-notice">⚠️ Anda belum login — akan diarahkan ke halaman login terlebih dahulu</div>';
            }

            showState('valid');
            startCountdown();
        }

        function showInvalidState(data) {
            document.getElementById('invalidDesc').textContent =
                `Anda berada ${data.distance}m dari lokasi ini, sedangkan batas radius hanya ${data.radius}m.`;
            document.getElementById('distanceBadgeInvalid').textContent =
                `📏 Jarak: ${data.distance}m | Batas: ${data.radius}m`;
            showState('invalid');
        }

        function showState(state) {
            ['stateChecking', 'stateValid', 'stateInvalid', 'stateGpsError'].forEach(id => {
                document.getElementById(id).classList.add('hidden');
            });
            const map = {
                checking: 'stateChecking',
                valid:     'stateValid',
                invalid:   'stateInvalid',
                gpsError:  'stateGpsError',
            };
            if (map[state]) document.getElementById(map[state]).classList.remove('hidden');
        }

        // ── Countdown & redirect ─────────────────────────────────────────────────
        function startCountdown() {
            let count = 3;
            const box  = document.getElementById('countdownBox');
            const num  = document.getElementById('countdownNum');

            box.classList.remove('hidden');
            num.textContent = count;

            countdownInterval = setInterval(() => {
                count--;
                num.textContent = count;
                if (count <= 0) {
                    clearInterval(countdownInterval);
                    doRedirect();
                }
            }, 1000);
        }

        function doRedirect() {
            if (countdownInterval) clearInterval(countdownInterval);
            if (redirectUrl) window.location.href = redirectUrl;
        }

        function retryValidation() {
            startValidation();
        }
    </script>
</body>
</html>
