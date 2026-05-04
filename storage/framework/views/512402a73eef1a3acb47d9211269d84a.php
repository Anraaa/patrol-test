
<div
    x-data="patrolCheckpoint()"
    x-init="init()"
    class="w-full"
>
    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-900 space-y-5">

        
        <div class="flex items-center justify-center gap-4 text-xs font-medium">
            <template x-for="(s, idx) in steps" :key="idx">
                <div class="flex items-center gap-1">
                    <div
                        class="flex h-7 w-7 items-center justify-center rounded-full text-white text-xs font-bold transition-all"
                        :class="{
                            'bg-primary-600': currentStepIndex === idx,
                            'bg-success-500': currentStepIndex > idx,
                            'bg-gray-200 dark:bg-gray-700 !text-gray-400': currentStepIndex < idx
                        }"
                        x-text="currentStepIndex > idx ? '✓' : (idx + 1)"
                    ></div>
                    <span
                        class="hidden sm:inline transition-colors"
                        :class="{
                            'text-primary-600 dark:text-primary-400': currentStepIndex === idx,
                            'text-success-600 dark:text-success-400': currentStepIndex > idx,
                            'text-gray-400': currentStepIndex < idx
                        }"
                        x-text="s.label"
                    ></span>
                    <div x-show="idx < steps.length - 1" class="h-px w-8 sm:w-12 bg-gray-200 dark:bg-gray-700 mx-1"></div>
                </div>
            </template>
        </div>

        
        <div x-show="state === 'photo'">
            <div class="text-center mb-4">
                <div class="text-3xl mb-2">🤳</div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Foto Muka Petugas</h3>
                <p class="text-xs text-gray-400 mt-1">Ambil selfie sebagai bukti kehadiran di pos patroli ini</p>
            </div>

            <input type="file" id="cp-face-input" accept="image/*" capture="user" class="hidden" @change="onFacePhoto($event)">

            <div
                @click="document.getElementById('cp-face-input').click()"
                class="cursor-pointer flex flex-col items-center justify-center gap-3 rounded-xl border-2 border-dashed transition-colors p-6"
                :class="facePhotoPreview ? 'border-success-400 bg-success-50 dark:bg-success-950/20' : 'border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 hover:border-primary-400'"
            >
                <template x-if="!facePhotoPreview">
                    <div class="text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-200">Tap untuk buka kamera depan</p>
                    </div>
                </template>
                <template x-if="facePhotoPreview">
                    <div class="text-center">
                        <img :src="facePhotoPreview" alt="Foto muka" class="h-36 w-36 rounded-full object-cover border-4 border-success-400 mx-auto shadow-lg">
                        <p class="text-xs text-success-600 dark:text-success-400 mt-2 font-medium">✅ Foto berhasil — tap untuk ganti</p>
                    </div>
                </template>
            </div>

            <div x-show="photoError" class="mt-2 text-sm text-red-600 dark:text-red-400" x-text="photoError"></div>

            <button
                type="button"
                @click="proceedToSign()"
                x-show="facePhotoPreview"
                class="mt-4 w-full inline-flex items-center justify-center gap-2 rounded-lg bg-primary-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-primary-500 transition-colors"
            >
                Lanjut ke Tanda Tangan →
            </button>
        </div>

        
        <div x-show="state === 'sign'">
            <div class="text-center mb-4">
                <div class="text-3xl mb-2">✍️</div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Tanda Tangan Petugas</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Tanda tangani di kotak di bawah ini</p>
            </div>

            <div class="relative rounded-xl border-2 border-gray-300 dark:border-gray-600 overflow-hidden bg-white" style="touch-action: none;">
                <canvas
                    id="cp-sig-canvas"
                    class="w-full block"
                    style="height: 200px; cursor: crosshair; touch-action: none;"
                    @pointerdown="sigStart($event)"
                    @pointermove="sigMove($event)"
                    @pointerup="sigEnd($event)"
                    @pointercancel="sigEnd($event)"
                ></canvas>
                <div x-show="!hasSignature" class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <p class="text-gray-300 dark:text-gray-600 text-sm select-none">Tanda tangan di sini…</p>
                </div>
            </div>

            <div class="mt-2 flex justify-between items-center">
                <span x-show="hasSignature" class="text-xs text-success-600 dark:text-success-400 font-medium">✅ Tanda tangan terekam</span>
                <span x-show="!hasSignature" class="text-xs text-gray-400">Belum ada tanda tangan</span>
                <button type="button" @click="clearSignature()" class="text-xs text-gray-500 hover:text-red-500 border border-gray-200 dark:border-gray-700 rounded px-2 py-1 transition-colors">
                    Hapus
                </button>
            </div>

            <div x-show="signError" class="mt-2 text-sm text-red-600 dark:text-red-400" x-text="signError"></div>

            <div class="mt-4 flex gap-3">
                <button type="button" @click="state = 'photo'" class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg bg-gray-100 dark:bg-gray-700 px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-200 transition-colors">
                    ← Kembali
                </button>
                <button type="button" @click="saveCheckpoint()" class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg bg-success-600 px-4 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-success-500 transition-colors">
                    <template x-if="!saving">
                        <span>✅ Selesai & Simpan</span>
                    </template>
                    <template x-if="saving">
                        <span class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                            </svg>
                            Menyimpan…
                        </span>
                    </template>
                </button>
            </div>
        </div>

        
        <div x-show="state === 'done'" class="text-center py-6">
            <div class="text-5xl mb-4">✅</div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Absensi Berhasil!</h3>
            <p class="text-xs text-gray-400 mt-2">Klik <strong>Simpan Laporan Patroli</strong> di bawah untuk menyelesaikan.</p>
            <div class="mt-4 inline-flex items-center gap-2 rounded-lg bg-success-50 dark:bg-success-950/30 border border-success-200 dark:border-success-800 px-4 py-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-success-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-sm font-medium text-success-700 dark:text-success-300">Foto muka & tanda tangan tersimpan</span>
            </div>
        </div>

    </div>
</div>

<script>
function patrolCheckpoint() {
    return {
        state: 'photo', // photo | sign | done
        steps: [
            { label: 'Foto Muka' },
            { label: 'Tanda Tangan' },
        ],
        get currentStepIndex() {
            return { photo: 0, sign: 1, done: 2 }[this.state] ?? 0;
        },

        facePhotoBase64: '',
        facePhotoPreview: '',
        photoError: '',

        sigCanvas: null,
        sigCtx: null,
        sigDrawing: false,
        hasSignature: false,
        signatureDataUrl: '',
        signError: '',
        saving: false,

        init() {
            this.$nextTick(() => this.initSignatureCanvas());
        },

        onFacePhoto(event) {
            const file = event.target.files[0];
            if (!file) return;
            this.photoError = '';
            const reader = new FileReader();
            reader.onload = (e) => {
                this.facePhotoPreview = e.target.result;
                this.facePhotoBase64  = e.target.result;
            };
            reader.readAsDataURL(file);
        },

        proceedToSign() {
            if (!this.facePhotoBase64) {
                this.photoError = 'Foto muka wajib diambil terlebih dahulu.';
                return;
            }
            this.state = 'sign';
            this.$nextTick(() => this.initSignatureCanvas());
        },

        initSignatureCanvas() {
            const el = document.getElementById('cp-sig-canvas');
            if (!el) return;
            this.sigCanvas = el;
            this.sigCtx = el.getContext('2d');
            const rect = el.getBoundingClientRect();
            el.width  = rect.width  || 600;
            el.height = rect.height || 200;
            
            // Always use white background with black stroke (both light and dark mode)
            this.sigCtx.fillStyle = '#ffffff';
            this.sigCtx.strokeStyle = '#000000';
            
            // Clear canvas with white background
            this.sigCtx.fillRect(0, 0, el.width, el.height);
            this.sigCtx.lineWidth   = 2.5;
            this.sigCtx.lineCap     = 'round';
            this.sigCtx.lineJoin    = 'round';
        },

        getCanvasPos(e) {
            const r  = this.sigCanvas.getBoundingClientRect();
            const sx = this.sigCanvas.width  / r.width;
            const sy = this.sigCanvas.height / r.height;
            return [(e.clientX - r.left) * sx, (e.clientY - r.top) * sy];
        },

        sigStart(e) {
            if (!this.sigCanvas) this.initSignatureCanvas();
            this.sigDrawing = true;
            this.sigCtx.beginPath();
            this.sigCtx.moveTo(...this.getCanvasPos(e));
            this.sigCanvas.setPointerCapture(e.pointerId);
        },

        sigMove(e) {
            if (!this.sigDrawing) return;
            this.sigCtx.lineTo(...this.getCanvasPos(e));
            this.sigCtx.stroke();
        },

        sigEnd() {
            this.sigDrawing = false;
            this.hasSignature = true;
            this.signatureDataUrl = this.sigCanvas.toDataURL('image/png');
        },

        clearSignature() {
            if (!this.sigCanvas) return;
            
            // Always use white background (both light and dark mode)
            this.sigCtx.fillStyle = '#ffffff';
            
            this.sigCtx.fillRect(0, 0, this.sigCanvas.width, this.sigCanvas.height);
            this.hasSignature = false;
            this.signatureDataUrl = '';
        },

        async saveCheckpoint() {
            if (!this.hasSignature || !this.signatureDataUrl) {
                this.signError = 'Tanda tangan wajib diisi sebelum melanjutkan.';
                return;
            }
            this.signError = '';
            this.saving = true;

            if (typeof Livewire !== 'undefined') {
                // Get location_id dari form state
                const formLocation = document.querySelector('input[name="data[location_id]"]')?.value 
                                  || document.querySelector('select[name="data[location_id]"]')?.value;
                
                const payload = {
                    location_id:        parseInt(formLocation) || null,
                    face_photo_base64:  this.facePhotoBase64,
                    signature_data_url: this.signatureDataUrl,
                };
                
                console.log('🔄 Dispatching checkpointDataCollected:', payload);
                
                Livewire.dispatch('checkpointDataCollected', payload);
            }

            this.saving = false;
            this.state = 'done';
        },
    };
}
</script>
<?php /**PATH /root/gawe/PatrolHR/resources/views/filament/forms/components/qr-checkpoint.blade.php ENDPATH**/ ?>