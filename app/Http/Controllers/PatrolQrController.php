<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Patrol;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class PatrolQrController extends Controller
{
    /**
     * Generate QR code token for a new patrol
     */
    public function generateToken(): JsonResponse
    {
        $token = Str::random(32);
        
        return response()->json([
            'token' => $token,
            'scan_url' => route('filament.admin.resources.patrols.scan-qr') . '?token=' . $token,
        ]);
    }

    /**
     * Validate patrol via QR code scan
     */
    public function validateQrScan(string $token): JsonResponse
    {
        // Use eager loading to prevent N+1 queries
        $patrol = Patrol::with(['user', 'location', 'shift'])
            ->where('qr_code_token', $token)
            ->first();

        if (!$patrol) {
            return response()->json([
                'success' => false,
                'message' => 'Token QR code tidak valid atau tidak ditemukan',
            ], 404);
        }

        if ($patrol->isValidated()) {
            return response()->json([
                'success' => false,
                'message' => "Patrol sudah ter-validasi pada {$patrol->qr_scanned_at->format('d/m/Y H:i')}",
                'already_validated' => true,
            ], 409);
        }

        if ($patrol->validateWithQrCode($token, request()->ip())) {
            return response()->json([
                'success' => true,
                'message' => 'Patrol berhasil di-validasi',
                'patrol' => [
                    'id' => $patrol->id,
                    'user_name' => $patrol->user->name,
                    'location_name' => $patrol->location->name,
                    'shift_name' => $patrol->shift->name ?? 'N/A',
                    'patrol_time' => $patrol->patrol_time->format('d/m/Y H:i:s'),
                    'qr_scanned_at' => $patrol->qr_scanned_at->format('d/m/Y H:i:s'),
                    'qr_scanned_ip' => $patrol->qr_scanned_ip,
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal memvalidasi patrol. Token tidak sesuai.',
        ], 400);
    }

    /**
     * Public QR scan endpoint: scan location QR code
     * Flow: Scan QR Lokasi → Validate Location → Check Auth → Set Session → Redirect to Patrol Form
     * 
     * User harus scan QR lokasi terlebih dahulu sebelum bisa membuat laporan patrol
     */
    public function publicScan(string $uuid)
    {
        // Validate location exists by UUID
        $location = Location::where('uuid', $uuid)->first();

        if (!$location) {
            return view('qr-scan-result', [
                'success' => false,
                'icon' => '❌',
                'title' => 'QR Lokasi Tidak Valid',
                'message' => 'Lokasi dengan QR code ini tidak ditemukan dalam sistem.',
            ]);
        }

        // If not authenticated → redirect to login, store intended URL
        if (!auth()->check()) {
            session()->put('url.intended', route('patrol.qr-scan', ['uuid' => $uuid]));
            return redirect()->route('filament.admin.auth.login');
        }

        // ✅ Mark that user has scanned a location
        // Store scanned location UUID in session
        session()->put('qr_location_scanned', $uuid);
        session()->put('qr_location_scanned_at', now()->timestamp);

        // Show success message
        return view('qr-scan-result', [
            'success' => true,
            'icon' => '✅',
            'title' => 'QR Lokasi Valid!',
            'message' => "Lokasi {$location->name} berhasil di-validasi. Anda sekarang bisa membuat laporan patroli.",
            'locationData' => [
                'name' => $location->name,
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
                'radius_meters' => $location->radius_meters,
            ],
            'redirectUrl' => route('filament.admin.resources.patrols.create', ['loc' => $uuid]),
        ]);
    }

    /**
     * Handle custom camera scan submission
     * User scan QR code dari custom camera page
     */
    public function submitCameraScan(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus login terlebih dahulu',
            ], 401);
        }

        $uuid = $request->input('uuid');

        if (!$uuid) {
            return response()->json([
                'success' => false,
                'message' => 'UUID lokasi tidak ditemukan',
            ], 400);
        }

        // Validate location exists
        $location = Location::where('uuid', $uuid)->first();

        if (!$location) {
            return response()->json([
                'success' => false,
                'message' => 'Lokasi dengan QR code ini tidak ditemukan dalam sistem',
            ], 404);
        }

        // ✅ SET SESSION
        session()->put('qr_location_scanned', $uuid);
        session()->put('qr_location_scanned_at', now()->timestamp);

        // Return redirect URL
        return response()->json([
            'success' => true,
            'message' => "Lokasi {$location->name} berhasil di-validasi",
            'redirect_url' => route('filament.admin.resources.patrols.create', ['loc' => $uuid]),
        ]);
    }

    /**
     * Show custom camera scan page
     */
    public function showCameraScan()
    {
        if (!auth()->check()) {
            session()->put('url.intended', route('patrol.camera-scan'));
            return redirect()->route('filament.admin.auth.login');
        }

        return view('qr-camera-scan');
    }
}
