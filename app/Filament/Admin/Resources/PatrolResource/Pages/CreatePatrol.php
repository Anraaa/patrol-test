<?php

namespace App\Filament\Admin\Resources\PatrolResource\Pages;

use App\Filament\Admin\Resources\PatrolResource;
use App\Models\Location;
use App\Models\PatrolCheckpoint;
use App\Models\Shift;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;

class CreatePatrol extends CreateRecord
{
    protected static string $resource = PatrolResource::class;

    protected ?string $scannedLocationId = null;

    public bool $viaScan = false;

    // ── Checkpoint data — semua nullable string ────────────────────────────
    public ?int    $checkpointLocationId   = null;
    public ?string $checkpointUuid         = null;
    public ?string $checkpointFacePhotoB64 = null;
    public ?string $checkpointSignature    = null;
    public bool    $checkpointCompleted    = false;

    // ── Livewire v3: gunakan #[On] attribute, bukan getListeners() ──────────
    #[On('checkpointLocationSet')]
    public function onCheckpointLocationSet(
        ?int    $locationId = null,
        ?string $uuid       = null,
        ?string $locationName = null,
    ): void {
        $this->checkpointLocationId = $locationId;
        $this->checkpointUuid       = $uuid;

        if ($this->checkpointLocationId) {
            try {
                $this->form->fill([
                    ...$this->form->getState(),
                    'location_id' => $this->checkpointLocationId,
                ]);
            } catch (\Throwable) {}
        }
    }

    #[On('checkpointDataCollected')]
    public function onCheckpointDataCollected(
        ?int    $location_id        = null,
        ?string $uuid              = null,
        ?string $location_name     = null,
        ?string $face_photo_base64 = null,
        ?string $signature_data_url = null,
    ): void {
        \Illuminate\Support\Facades\Log::debug('Listener onCheckpointDataCollected called', [
            'location_id' => $location_id,
            'face_photo_base64' => $face_photo_base64 ? '✓ ada' : '✗ kosong',
            'signature_data_url' => $signature_data_url ? '✓ ada' : '✗ kosong',
        ]);

        // Priority: use passed location_id, fallback to form state
        if ($location_id) {
            $this->checkpointLocationId = $location_id;
        } else {
            // Get from form state if not passed from event
            $formState = $this->form->getState();
            $this->checkpointLocationId = $formState['location_id'] ?? null;
        }
        
        $this->checkpointUuid         = $uuid;
        $this->checkpointFacePhotoB64 = $face_photo_base64;
        $this->checkpointSignature    = $signature_data_url;
        $this->checkpointCompleted    = true;

        \Illuminate\Support\Facades\Log::debug('Checkpoint properties set', [
            'checkpointLocationId' => $this->checkpointLocationId,
            'checkpointCompleted' => $this->checkpointCompleted,
        ]);

        // Update form fields so they persist in form submission
        try {
            $this->form->fill([
                ...$this->form->getState(),
                'checkpoint_face_photo_b64' => $face_photo_base64,
                'checkpoint_signature'      => $signature_data_url,
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Form fill failed', ['error' => $e->getMessage()]);
        }

        Notification::make()
            ->title('Checkpoint Terekam')
            ->body('📍 Foto muka & tanda tangan berhasil disimpan. Klik "Simpan Laporan" untuk menyelesaikan.')
            ->success()
            ->send();
    }

    public function authorizeAccess(): void
    {
        if ($this->viaScan) {
            abort_unless(auth()->check(), 403);
            return;
        }

        $requestLocUuid = request()->query('loc');
        $scannedLocationUuid = session('qr_location_scanned');

        // If accessing with ?loc param, verify location exists
        if ($requestLocUuid && Location::where('uuid', $requestLocUuid)->exists()) {
            abort_unless(auth()->check(), 403);
            $this->viaScan = true;
            return;
        }

        parent::authorizeAccess();
    }

    public function mount(): void
    {
        parent::mount();

        // Check if user has scanned a location
        $scannedLocationUuid = session('qr_location_scanned');
        $requestLocUuid = request()->query('loc') ?? $scannedLocationUuid;

        // MUST SCAN: User harus scan QR terlebih dahulu sebelum membuat laporan
        if (!$requestLocUuid) {
            $this->redirect(route('patrol.camera-scan'));
            return;
        }

        // Verify location exists
        $location = Location::where('uuid', $requestLocUuid)->first();
        if (!$location) {
            $this->redirect(route('patrol.camera-scan'));
            return;
        }

        $this->scannedLocationId = $location->id;

        $this->form->fill(['location_id' => $location->id]);

        Notification::make()
            ->title('Lokasi Terdeteksi dari QR Code')
            ->body("📍 {$location->name} — Lokasi sudah terisi otomatis.")
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['patrol_time'] = now();
        $data['user_id']     = auth()->id();
        $data['shift_id']    = $this->resolveShiftId();

        if ($this->checkpointLocationId && empty($data['location_id'])) {
            $data['location_id'] = $this->checkpointLocationId;
        }

        // ── Set QR validation data if user has scanned QR location ──────────
        $qrLocationScanned = session('qr_location_scanned');
        
        if ($qrLocationScanned) {
            $data['qr_code_token'] = \Illuminate\Support\Str::random(32);
            $data['qr_scanned_at'] = now();
            $data['qr_scanned_ip'] = request()?->ip();
            
            session()->forget('qr_location_scanned');
            session()->forget('qr_location_scanned_at');
        }
        elseif ($this->checkpointCompleted && $this->checkpointLocationId) {
            $data['qr_code_token'] = \Illuminate\Support\Str::random(32);
            $data['qr_scanned_at'] = now();
            $data['qr_scanned_ip'] = request()?->ip();
        }

        // ── Process checkpoint face_photo & signature → save to Patrol ─────
        if ($this->checkpointFacePhotoB64 && str_starts_with($this->checkpointFacePhotoB64, 'data:')) {
            try {
                [$meta, $b64] = explode(',', $this->checkpointFacePhotoB64, 2);
                $ext  = str_contains($meta, 'jpeg') ? 'jpg' : 'png';
                $path = 'checkpoint-face-photos/' . uniqid('face_') . '.' . $ext;
                Storage::disk('public')->put($path, base64_decode($b64));
                $data['face_photo'] = $path;
                \Illuminate\Support\Facades\Log::info('Face photo saved to patrol', ['path' => $path]);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Face photo save failed', ['error' => $e->getMessage()]);
            }
        }

        if ($this->checkpointSignature && str_starts_with($this->checkpointSignature, 'data:')) {
            try {
                [, $b64] = explode(',', $this->checkpointSignature, 2);
                $path = 'checkpoint-signatures/' . uniqid('sig_') . '.png';
                Storage::disk('public')->put($path, base64_decode($b64));
                $data['signature'] = $path;
                \Illuminate\Support\Facades\Log::info('Signature saved to patrol', ['path' => $path]);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Signature save failed', ['error' => $e->getMessage()]);
            }
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        \Illuminate\Support\Facades\Log::debug('afterCreate called', [
            'checkpointCompleted'   => $this->checkpointCompleted,
            'checkpointLocationId'  => $this->checkpointLocationId,
            'hasFacePhoto'          => !empty($this->checkpointFacePhotoB64),
            'hasSignature'          => !empty($this->checkpointSignature),
        ]);

        if (! $this->checkpointCompleted || ! $this->checkpointLocationId) {
            \Illuminate\Support\Facades\Log::warning('Checkpoint skipped - missing data', [
                'completed' => $this->checkpointCompleted,
                'locationId' => $this->checkpointLocationId,
            ]);
            return;
        }

        $facePhotoPath = null;
        $signaturePath = null;

        if ($this->checkpointFacePhotoB64 && str_starts_with($this->checkpointFacePhotoB64, 'data:')) {
            try {
                [$meta, $b64] = explode(',', $this->checkpointFacePhotoB64, 2);
                $ext  = str_contains($meta, 'jpeg') ? 'jpg' : 'png';
                $path = 'checkpoint-face-photos/' . uniqid('face_') . '.' . $ext;
                Storage::disk('public')->put($path, base64_decode($b64));
                $facePhotoPath = $path;
                \Illuminate\Support\Facades\Log::info('Face photo saved', ['path' => $path]);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Face photo save failed', ['error' => $e->getMessage()]);
            }
        }

        if ($this->checkpointSignature && str_starts_with($this->checkpointSignature, 'data:')) {
            try {
                [, $b64] = explode(',', $this->checkpointSignature, 2);
                $path = 'checkpoint-signatures/' . uniqid('sig_') . '.png';
                Storage::disk('public')->put($path, base64_decode($b64));
                $signaturePath = $path;
                \Illuminate\Support\Facades\Log::info('Signature saved', ['path' => $path]);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Signature save failed', ['error' => $e->getMessage()]);
            }
        }

        $checkpoint = PatrolCheckpoint::create([
            'patrol_id'   => $this->record->id,
            'location_id' => $this->checkpointLocationId,
            'user_id'     => auth()->id(),
            'face_photo'  => $facePhotoPath,
            'signature'   => $signaturePath,
            'scanned_at'  => now(),
        ]);

        \Illuminate\Support\Facades\Log::info('Checkpoint created', [
            'checkpoint_id' => $checkpoint->id,
            'face_photo' => $facePhotoPath,
            'signature' => $signaturePath,
        ]);

        Notification::make()
            ->title('Patroli & Checkpoint Tersimpan!')
            ->body('✅ Laporan patroli dan checkpoint berhasil dicatat.')
            ->success()
            ->send();
    }

    private function resolveShiftId(): ?int
    {
        $hour = (int) now()->format('G');

        $number = match(true) {
            $hour >= 7  && $hour < 15 => 1,
            $hour >= 15 && $hour < 23 => 2,
            default                   => 3,
        };

        $shift = Shift::where('name', 'like', "Shift {$number}%")->first()
            ?? Shift::orderBy('id')->skip($number - 1)->first()
            ?? Shift::orderBy('id')->first();

        return $shift?->id;
    }
}