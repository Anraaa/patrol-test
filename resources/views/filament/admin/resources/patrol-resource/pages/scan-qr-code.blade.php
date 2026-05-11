<x-filament::page>
    <div class="space-y-6">
        {{-- QR Code Scanner Header --}}
        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-800/80 shadow-lg p-6">
            <div class="flex items-center gap-4 mb-6">
                <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 shadow-lg">
                    <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 0 1-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m9.75 0h7.5c.621 0 1.125.504 1.125 1.125v1.5m0 0V5.625m0 0V4.875c0-.621-.504-1.125-1.125-1.125H4.5c-.621 0-1.125.504-1.125 1.125v15.75" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Scan QR Code</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Pindai QR code untuk memvalidasi patrol</p>
                </div>
            </div>

            {{-- Input area --}}
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Token QR Code
                    </label>
                    <input 
                        wire:model="qrToken"
                        type="text"
                        id="qrTokenInput"
                        placeholder="Masukkan atau pindai token QR code..."
                        @keydown.enter="$wire.handleQrScan()"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-4 py-3 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition"
                        autofocus
                    />
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        💡 PDT Scanner: Langsung validasi saat selesai scan (auto-detect)
                    </p>
                </div>

                {{-- GPS Status Indicator --}}
                @if($isGpsVerifying)
                    <div class="flex items-center gap-2 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-900/30 px-4 py-3">
                        <svg class="h-5 w-5 text-blue-600 dark:text-blue-400 animate-spin" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm font-semibold text-blue-800 dark:text-blue-200">Mendeteksi lokasi GPS...</span>
                    </div>
                @endif

                @if($locationVerificationStatus)
                    <div class="flex items-center gap-2 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-900/30 px-4 py-3">
                        <svg class="h-5 w-5 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-semibold text-emerald-800 dark:text-emerald-200">{{ $locationVerificationStatus }}</span>
                    </div>
                @endif

                <button
                    wire:click="handleQrScan"
                    {{ $isGpsVerifying ? 'disabled' : '' }}
                    class="w-full rounded-lg bg-gradient-to-r from-primary-500 to-primary-600 px-4 py-3 text-white font-semibold shadow-lg hover:shadow-xl hover:from-primary-600 hover:to-primary-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <svg class="inline-block h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 9h.01M9 12h.01M9 15h.01M12 9h.01M12 12h.01M12 15h.01M15 9h.01M15 12h.01M15 15h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    {{ $isGpsVerifying ? 'Validasi Lokasi...' : 'Validasi' }}
                </button>
            </div>
        </div>

        {{-- Status Message --}}
        @if($statusMessage)
            @php
                $statusClasses = match($statusType) {
                    'success' => 'border-emerald-200 dark:border-emerald-900/30 bg-emerald-50 dark:bg-emerald-900/20',
                    'error' => 'border-rose-200 dark:border-rose-900/30 bg-rose-50 dark:bg-rose-900/20',
                    'warning' => 'border-amber-200 dark:border-amber-900/30 bg-amber-50 dark:bg-amber-900/20',
                    default => 'border-blue-200 dark:border-blue-900/30 bg-blue-50 dark:bg-blue-900/20',
                };
                $textColor = match($statusType) {
                    'success' => 'text-emerald-800 dark:text-emerald-200',
                    'error' => 'text-rose-800 dark:text-rose-200',
                    'warning' => 'text-amber-800 dark:text-amber-200',
                    default => 'text-blue-800 dark:text-blue-200',
                };
                $icon = match($statusType) {
                    'success' => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
                    'error' => 'M6 18L18 6M6 6l12 12',
                    'warning' => 'M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z',
                    default => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
                };
            @endphp
            <div class="rounded-2xl border {{ $statusClasses }} shadow-md p-6">
                <div class="flex items-start gap-4">
                    <svg class="h-6 w-6 mt-0.5 flex-shrink-0 {{ $textColor }}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" />
                    </svg>
                    <div class="flex-1">
                        <p class="font-semibold {{ $textColor }}">{{ $statusMessage }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Scanned Patrol Details --}}
        @if($scannedPatrol)
            <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-lg overflow-hidden">
                <div class="border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-primary-50 to-primary-100/50 dark:from-primary-900/20 dark:to-primary-900/10 px-6 py-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Detail Patrol Ter-validasi</h3>
                </div>

                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- User Info --}}
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">User Petugas</label>
                            <p class="mt-1 text-lg font-bold text-gray-900 dark:text-white">{{ $scannedPatrol->user->name }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $scannedPatrol->user->email }}</p>
                        </div>

                        {{-- Location Info --}}
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Lokasi Patrol</label>
                            <p class="mt-1 text-lg font-bold text-gray-900 dark:text-white">{{ $scannedPatrol->location->name }}</p>
                        </div>

                        {{-- Shift Info --}}
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Shift</label>
                            <p class="mt-1 text-lg font-bold text-gray-900 dark:text-white">{{ $scannedPatrol->shift->name ?? 'N/A' }}</p>
                        </div>

                        {{-- Validation Time --}}
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Waktu Validasi</label>
                            <p class="mt-1 text-lg font-bold text-gray-900 dark:text-white">
                                @if($scannedPatrol->qr_scanned_at)
                                    {{ $scannedPatrol->qr_scanned_at->format('d/m/Y H:i:s') }}
                                @else
                                    Belum ter-validasi
                                @endif
                            </p>
                        </div>

                        {{-- Patrol Time --}}
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Waktu Patrol</label>
                            <p class="mt-1 text-lg font-bold text-gray-900 dark:text-white">
                                {{ $scannedPatrol->patrol_time?->format('d/m/Y H:i:s') ?? 'N/A' }}
                            </p>
                        </div>

                        {{-- IP Address --}}
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">IP Address Pemindai</label>
                            <p class="mt-1 text-lg font-bold text-gray-900 dark:text-white font-mono">
                                {{ $scannedPatrol->qr_scanned_ip ?? '-' }}
                            </p>
                        </div>
                    </div>

                    {{-- Status Badge --}}
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        @if($scannedPatrol->isValidated())
                            <div class="flex items-center gap-2 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-900/30 px-4 py-3">
                                <svg class="h-5 w-5 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span class="font-semibold text-emerald-800 dark:text-emerald-200">Patrol Tervalidasi</span>
                            </div>
                        @else
                            <div class="flex items-center gap-2 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-900/30 px-4 py-3">
                                <svg class="h-5 w-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                                </svg>
                                <span class="font-semibold text-amber-800 dark:text-amber-200">Belum Tervalidasi</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Quick Actions --}}
        <div class="rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Informasi Penting</h3>
            <ul class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                <li class="flex items-start gap-3">
                    <svg class="h-5 w-5 mt-0.5 flex-shrink-0 text-primary-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span>Scan QR code yang tertera pada lokasi patrol untuk memvalidasi patrol</span>
                </li>
                <li class="flex items-start gap-3">
                    <svg class="h-5 w-5 mt-0.5 flex-shrink-0 text-primary-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span>Setiap patrol harus dilakukan di semua titik lokasi yang ditugaskan</span>
                </li>
                <li class="flex items-start gap-3">
                    <svg class="h-5 w-5 mt-0.5 flex-shrink-0 text-primary-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span>Data IP address pemindai akan tercatat untuk audit trail keamanan</span>
                </li>
                <li class="flex items-start gap-3">
                    <svg class="h-5 w-5 mt-0.5 flex-shrink-0 text-primary-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span>Setiap lokasi harus memiliki QR code yang unik untuk keamanan data</span>
                </li>
            </ul>
        </div>
    </div>

    <script>
        /**
         * PDT Scanner Auto-Submission & GPS Detection
         * ============================================
         * - Detects when PDT scanner finishes reading QR code
         * - Auto-captures GPS location if location validation required
         * - Auto-submits form without manual click
         */
        
        const qrTokenInput = document.getElementById('qrTokenInput');
        let lastTokenValue = '';
        let gpsDetectionTimeout = null;

        // Listen for 'requestGpsLocation' event from Livewire
        window.addEventListener('requestGpsLocation', function() {
            captureGpsLocation();
        });

        // Detect PDT scanner input completion
        // PDT scanners typically clear the field and input new data in rapid succession
        qrTokenInput.addEventListener('change', function() {
            const currentValue = this.value.trim();
            
            // Debounce: wait for scanner to finish (PDT sends all data quickly)
            clearTimeout(gpsDetectionTimeout);
            
            gpsDetectionTimeout = setTimeout(() => {
                if (currentValue && currentValue !== lastTokenValue) {
                    lastTokenValue = currentValue;
                    
                    // Auto-trigger validation with GPS detection
                    console.log('🔍 PDT Scanner detected. Token:', currentValue);
                    
                    // First try to get GPS if available
                    if (navigator.geolocation) {
                        captureGpsLocation();
                    } else {
                        // No GPS, proceed with validation only
                        window.Livewire.dispatch('handleQrScan');
                    }
                }
            }, 500); // Wait 500ms for scanner to finish
        });

        // Manual input detection for non-PDT scenarios
        qrTokenInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const currentValue = this.value.trim();
                if (currentValue) {
                    lastTokenValue = currentValue;
                    // Manual Enter press - try GPS then submit
                    if (navigator.geolocation) {
                        captureGpsLocation();
                    } else {
                        window.Livewire.dispatch('handleQrScan');
                    }
                }
            }
        });

        /**
         * Capture user's GPS location
         * Sends coordinates back to Livewire component
         */
        function captureGpsLocation() {
            if (!navigator.geolocation) {
                console.warn('⚠️ GPS not available, proceeding with QR validation only');
                window.Livewire.dispatch('handleQrScan');
                return;
            }

            console.log('📍 Requesting GPS location...');
            
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    const accuracy = position.coords.accuracy;
                    
                    console.log(`✅ GPS captured: ${lat.toFixed(6)}, ${lng.toFixed(6)} (±${accuracy.toFixed(0)}m)`);
                    
                    // Send coordinates to Livewire
                    window.Livewire.dispatch('gpsLocationReceived', {
                        latitude: lat,
                        longitude: lng,
                        accuracy: accuracy
                    });
                },
                function(error) {
                    console.warn('⚠️ GPS error:', error.message);
                    
                    // Proceed without GPS if user denies or GPS unavailable
                    if (error.code !== 1) { // Not PERMISSION_DENIED
                        console.log('Proceeding with QR validation (GPS unavailable)');
                        window.Livewire.dispatch('handleQrScan');
                    }
                },
                {
                    enableHighAccuracy: true,
                    timeout: 8000,        // 8 second timeout
                    maximumAge: 0         // Don't use cached position
                }
            );
        }

        // Expose for manual debugging
        window.manualScanRequest = function() {
            if (qrTokenInput.value) {
                qrTokenInput.dispatchEvent(new Event('change', { bubbles: true }));
            }
        };
    </script>
</x-filament::page>
