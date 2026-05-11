<?php

namespace App\Filament\Admin\Resources\PatrolResource\Pages;

use App\Events\PatrolQrScanned;
use App\Filament\Admin\Resources\PatrolResource;
use App\Models\Patrol;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Str;
use Livewire\Attributes\On;

class ScanQrCode extends Page
{
    protected static string $resource = PatrolResource::class;
    protected static string $view = 'filament.admin.resources.patrol-resource.pages.scan-qr-code';
    protected static ?string $title = 'Scan QR Code Patrol';
    protected static ?string $navigationIcon = 'heroicon-o-qrcode';

    public ?string $qrToken = null;
    public ?Patrol $scannedPatrol = null;
    public ?string $statusMessage = null;
    public ?string $statusType = null;
    public ?float $userLat = null;
    public ?float $userLng = null;
    public bool $isGpsVerifying = false;
    public ?string $locationVerificationStatus = null;

    public function mount(): void
    {
        // Get token dari URL if available
        $token = request()->query('token');
        if ($token) {
            $this->qrToken = $token;
            $this->handleQrScan();
        }
    }

    public function handleQrScan(): void
    {
        if (empty($this->qrToken)) {
            $this->statusType = 'error';
            $this->statusMessage = 'Token QR code tidak ditemukan';
            return;
        }

        // Find patrol dengan qr_code_token yang sesuai - preload relationships
        $patrol = Patrol::with(['user', 'location'])->where('qr_code_token', $this->qrToken)->first();

        if (!$patrol) {
            $this->statusType = 'error';
            $this->statusMessage = 'Token QR code tidak valid atau tidak ditemukan';
            $this->qrToken = null;
            return;
        }

        // Cek apakah sudah di-scan sebelumnya
        if ($patrol->isValidated()) {
            $this->statusType = 'warning';
            $this->statusMessage = "Patrol sudah ter-validasi pada {$patrol->qr_scanned_at->format('d/m/Y H:i')}";
            $this->scannedPatrol = $patrol;
            return;
        }

        // Cek validasi lokasi jika lokasi memiliki GPS requirement
        if ($patrol->location && $patrol->location->require_gps_validation) {
            if ($this->userLat === null || $this->userLng === null) {
                $this->statusType = 'warning';
                $this->statusMessage = 'Menunggu verifikasi GPS lokasi...';
                $this->isGpsVerifying = true;
                $this->dispatch('requestGpsLocation');
                return;
            }

            // Hitung jarak antara user dan lokasi
            $distance = $this->calculateDistance(
                $this->userLat,
                $this->userLng,
                $patrol->location->latitude,
                $patrol->location->longitude
            );

            $maxRadius = $patrol->location->radius_meters ?? 50; // default 50 meter

            if ($distance > $maxRadius) {
                $this->statusType = 'error';
                $this->statusMessage = "Anda berada {$distance}m dari lokasi. Harus dalam radius {$maxRadius}m dari titik '{$patrol->location->name}'";
                $this->locationVerificationStatus = "Distance: {$distance}m / Radius: {$maxRadius}m";
                $this->qrToken = null;
                return;
            }

            $this->locationVerificationStatus = "✓ Verifikasi lokasi: {$distance}m dari titik";
        }

        // Validate with QR code
        if ($patrol->validateWithQrCode($this->qrToken, request()->ip())) {
            $userName = $patrol->user?->name ?? 'Petugas';
            $locationName = $patrol->location?->name ?? 'Lokasi';
            $this->statusType = 'success';
            $this->statusMessage = "✓ Patrol berhasil di-validasi! User: {$userName}, Lokasi: {$locationName}";
            $this->scannedPatrol = $patrol;

            // Emit event untuk dashboard update
            PatrolQrScanned::dispatch($patrol);
            $this->dispatch('patrolQrScanned', patrolId: $patrol->id);

            Notification::make()
                ->title('Validasi QR Code Berhasil')
                ->body("Patrol oleh {$userName} di {$locationName} telah ter-validasi")
                ->success()
                ->send();
        } else {
            $this->statusType = 'error';
            $this->statusMessage = 'Gagal memvalidasi patrol. Token tidak sesuai.';
        }

        $this->qrToken = null;
        $this->isGpsVerifying = false;
    }

    /**
     * Receive GPS coordinates from frontend (Haversine formula)
     */
    #[On('gpsLocationReceived')]
    public function receiveGpsLocation(array $data): void
    {
        $this->userLat = $data['latitude'];
        $this->userLng = $data['longitude'];
        $this->isGpsVerifying = false;
        
        // Retry scan validation dengan GPS data yang baru
        if (!empty($this->qrToken)) {
            $this->handleQrScan();
        }
    }

    /**
     * Calculate distance between two coordinates (Haversine formula)
     * Returns distance in meters
     */
    private function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadiusM = 6371000; // Earth radius in meters
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) * sin($dLng / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadiusM * $c;
        
        return round($distance, 1);
    }

    protected function getFormActions(): array
    {
        return [];
    }
}
