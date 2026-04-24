<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan QR Lokasi Terlebih Dahulu</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
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
            background: #ffffff;
            border-radius: 1.5rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 3rem 2rem;
            text-align: center;
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

        .icon {
            font-size: 5rem;
            margin-bottom: 1.5rem;
            display: inline-block;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-20px);
            }
        }

        .title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 1rem;
            line-height: 1.3;
        }

        .subtitle {
            font-size: 1rem;
            color: #6b7280;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .badge {
            display: inline-block;
            background: #fef3c7;
            color: #92400e;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .steps {
            background: #f9fafb;
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
            text-align: left;
        }

        .step {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1.25rem;
            gap: 1rem;
        }

        .step:last-child {
            margin-bottom: 0;
        }

        .step-number {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 2rem;
            height: 2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50%;
            font-weight: 700;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .step-content {
            flex: 1;
        }

        .step-title {
            font-weight: 600;
            color: #111827;
            margin-bottom: 0.25rem;
        }

        .step-desc {
            font-size: 0.9rem;
            color: #6b7280;
        }

        .info-box {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 2rem;
            text-align: left;
        }

        .info-box-title {
            font-weight: 600;
            color: #1e40af;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-box-text {
            font-size: 0.9rem;
            color: #1e3a8a;
            line-height: 1.5;
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

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #374151;
        }

        .btn-secondary:hover {
            background: #d1d5db;
        }

        .qr-visual {
            display: inline-block;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 0.75rem;
        }

        .qr-box {
            background: white;
            padding: 1.5rem;
            border-radius: 0.5rem;
            width: 180px;
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
        }

        @media (max-width: 640px) {
            .card {
                padding: 2rem 1.5rem;
            }

            .title {
                font-size: 1.5rem;
            }

            .icon {
                font-size: 4rem;
                margin-bottom: 1rem;
            }

            .steps {
                padding: 1rem;
            }

            .step {
                gap: 0.75rem;
            }

            .step-number {
                width: 1.75rem;
                height: 1.75rem;
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="qr-visual">
                <div class="qr-box">📱</div>
            </div>

            <div class="badge">⚠️ AKSI DIPERLUKAN</div>

            <h1 class="title">Scan QR Lokasi Terlebih Dahulu</h1>

            <p class="subtitle">
                Anda harus memindai QR code lokasi sebelum bisa membuat laporan patroli.
            </p>

            <div class="info-box">
                <div class="info-box-title">💡 Mengapa?</div>
                <div class="info-box-text">
                    Memindai QR code memastikan Anda berada di lokasi yang tepat dan data patroli terekam dengan akurat.
                </div>
            </div>

            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <div class="step-title">Cari QR Code</div>
                        <div class="step-desc">
                            Temukan QR code yang dipasang di lokasi patroli Anda
                        </div>
                    </div>
                </div>

                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <div class="step-title">Pindai dengan Kamera</div>
                        <div class="step-desc">
                            Gunakan kamera ponsel untuk memindai QR code
                        </div>
                    </div>
                </div>

                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <div class="step-title">Validasi Lokasi</div>
                        <div class="step-desc">
                            Sistem akan memvalidasi lokasi dan lokasinya akan otomatis terisi
                        </div>
                    </div>
                </div>

                <div class="step">
                    <div class="step-number">4</div>
                    <div class="step-content">
                        <div class="step-title">Buat Laporan</div>
                        <div class="step-desc">
                            Sekarang Anda bisa mengisi formulir laporan patroli
                        </div>
                    </div>
                </div>
            </div>

            <div class="actions">
                <a href="{{ route('patrol.camera-scan') }}" class="btn btn-primary">
                    📱 MULAI SCAN QR CODE
                </a>
                <a href="{{ route('filament.admin.pages.dashboard') }}" class="btn btn-secondary">
                    ← Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>

</body>
</html>
