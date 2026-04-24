<?php

namespace App\Http\Controllers;

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
     * Public QR scan endpoint: validate QR, check auth, redirect to form
     * Flow: Scan QR → Validate → Redirect to login or patrol form
     */
    public function publicScan(string $token)
    {
        // Validate QR token exists
        $patrol = Patrol::where('qr_code_token', $token)
            ->with(['user', 'location', 'shift'])
            ->first();

        if (!$patrol) {
            return view('qr-scan-result', [
                'success' => false,
                'icon' => '❌',
                'title' => 'QR Code Tidak Valid',
                'message' => 'Token QR code tidak ditemukan atau tidak valid.',
            ]);
        }

        // Check if QR already validated
        if ($patrol->isValidated()) {
            return view('qr-scan-result', [
                'success' => false,
                'icon' => '⚠️',
                'title' => 'QR Code Sudah Dipindai',
                'message' => "Patrol sudah ter-validasi pada {$patrol->qr_scanned_at->format('d/m/Y H:i')}",
            ]);
        }

        // If not authenticated → redirect to login
        if (!auth()->check()) {
            session()->put('url.intended', route('patrol.qr-scan', ['token' => $token]));
            return redirect()->route('filament.admin.auth.login');
        }

        // If authenticated → redirect to patrol form with QR token
        // Store token in session to pass to form
        session()->put('qr_scan_token', $token);
        
        return redirect()->route('filament.admin.resources.patrols.create');
    }
}
