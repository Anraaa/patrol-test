<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Validasi QR Code' }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .card {
            background: #fff;
            border-radius: 1.5rem;
            box-shadow: 0 20px 60px rgba(0,0,0,.3);
            padding: 3rem 2rem;
            max-width: 420px;
            width: 100%;
            text-align: center;
        }
        .icon {
            font-size: 5rem;
            margin-bottom: 1.5rem;
            display: inline-block;
        }
        .title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 0.75rem;
        }
        .message {
            font-size: 1rem;
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }
        .details {
            background: #f9fafb;
            border-radius: 0.75rem;
            padding: 1.25rem;
            text-align: left;
            margin-bottom: 1.5rem;
            display: none;
        }
        .details.show { display: block; }
        .detail-item {
            font-size: 0.9rem;
            color: #6b7280;
            margin-bottom: 0.75rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #e5e7eb;
        }
        .detail-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #374151;
            display: block;
            font-size: 0.85rem;
        }
        .detail-value {
            margin-top: 0.25rem;
            color: #6b7280;
        }
        .actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        .btn {
            flex: 1;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        .btn-secondary {
            background: #e5e7eb;
            color: #374151;
        }
        .btn-secondary:hover {
            background: #d1d5db;
        }
        .badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1.25rem;
        }
        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }
        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }
        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }
        .spinner {
            border: 4px solid #f3f4f6;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 1.5rem auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="card">
        @if ($success)
            <div class="badge badge-success">✓ Berhasil</div>
            <div class="icon">✅</div>
            <h1 class="title">{{ $title ?? 'QR Lokasi Valid' }}</h1>
            <p class="message">{{ $message ?? 'QR lokasi berhasil divalidasi' }}</p>
            <div class="details show">
                @if(isset($locationData))
                    <div class="detail-item">
                        <span class="detail-label">Nama Lokasi</span>
                        <span class="detail-value">{{ $locationData['name'] ?? '-' }}</span>
                    </div>
                    @if(isset($locationData['latitude']) && $locationData['latitude'])
                        <div class="detail-item">
                            <span class="detail-label">Koordinat GPS</span>
                            <span class="detail-value">{{ $locationData['latitude'] }}, {{ $locationData['longitude'] }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Radius Verifikasi</span>
                            <span class="detail-value">{{ $locationData['radius_meters'] ?? 50 }} meter</span>
                        </div>
                    @endif
                @endif
            </div>
            <div class="actions">
                <a href="{{ $redirectUrl ?? route('filament.admin.pages.dashboard') }}" class="btn btn-primary">← Kembali</a>
            </div>
        @else
            <div class="badge {{ isset($icon) && strpos($icon, '⚠️') !== false ? 'badge-warning' : 'badge-danger' }}">
                {{ isset($icon) && strpos($icon, '⚠️') !== false ? '⚠️ Perhatian' : '❌ Gagal' }}
            </div>
            <div class="icon">{{ $icon ?? '❌' }}</div>
            <h1 class="title">{{ $title ?? 'QR Lokasi Tidak Valid' }}</h1>
            <p class="message">{{ $message ?? 'QR lokasi tidak dapat divalidasi' }}</p>
            <div class="actions">
                <button onclick="window.history.back()" class="btn btn-secondary">← Kembali</button>
                <a href="{{ route('filament.admin.pages.dashboard') }}" class="btn btn-primary">Dashboard</a>
            </div>
        @endif
    </div>
</body>
</html>
