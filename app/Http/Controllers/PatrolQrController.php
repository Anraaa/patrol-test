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
     * Flow: Scan QR Lokasi → GPS Validation Page → Validate Distance → Redirect
     * 
     * Supports both external QR scanner (phone camera) and in-app camera scanner.
     */
    public function publicScan(string $uuid)
    {
        $location = Location::where('uuid', $uuid)->first();

        if (!$location) {
            return view('qr-scan-result', [
                'success' => false,
                'icon' => '❌',
                'title' => 'QR Lokasi Tidak Valid',
                'message' => 'Lokasi dengan QR code ini tidak ditemukan dalam sistem.',
            ]);
        }

        // Show GPS distance validation page
        return view('qr-scan-validate', [
            'location' => $location,
            'isAuthenticated' => auth()->check(),
        ]);
    }

    /**
     * Validate GPS distance from patrol location.
     * Called via AJAX from the qr-scan-validate page.
     * If valid: sets session and returns redirect URL (patrol form or login).
     */
    public function validateGpsDistance(\Illuminate\Http\Request $request, string $uuid): JsonResponse
    {
        $location = Location::where('uuid', $uuid)->first();

        if (!$location) {
            return response()->json([
                'valid' => false,
                'message' => 'Lokasi tidak ditemukan dalam sistem.',
            ], 404);
        }

        $lat = $request->input('latitude');
        $lng = $request->input('longitude');

        if ($lat === null || $lng === null) {
            return response()->json([
                'valid' => false,
                'message' => 'Koordinat GPS tidak tersedia.',
            ], 422);
        }

        $lat = (float) $lat;
        $lng = (float) $lng;

        if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
            return response()->json([
                'valid' => false,
                'message' => 'Koordinat GPS tidak valid.',
            ], 422);
        }

        $distance = $location->distanceTo($lat, $lng);
        $radius   = $location->radius_meters ?? 100;
        $isValid  = $distance <= $radius;

        if ($isValid) {
            // Set session so patrol form knows the user has scanned
            session()->put('qr_location_scanned', $uuid);
            session()->put('qr_location_scanned_at', now()->timestamp);

            if (auth()->check()) {
                $redirectUrl = route('filament.admin.resources.patrols.create', ['loc' => $uuid]);
            } else {
                // Store intended URL so after login user lands on patrol form
                session()->put('url.intended', route('filament.admin.resources.patrols.create', ['loc' => $uuid]));
                $redirectUrl = route('filament.admin.auth.login');
            }

            return response()->json([
                'valid'            => true,
                'distance'         => (int) round($distance),
                'radius'           => $radius,
                'location_name'    => $location->name,
                'redirect_url'     => $redirectUrl,
                'is_authenticated' => auth()->check(),
            ]);
        }

        return response()->json([
            'valid'    => false,
            'distance' => (int) round($distance),
            'radius'   => $radius,
            'message'  => 'Anda berada di luar radius lokasi yang diizinkan.',
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
