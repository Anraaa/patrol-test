<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patrol extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_id',
        'shift_id',
        'location_id',
        'violation_id',
        'action_id',
        'description',
        'photos',
        'signature',
        'face_photo',
        'patrol_time',
        'qr_code_token',
        'qr_scanned_at',
        'qr_scanned_ip',
    ];

    protected function casts(): array
    {
        return [
            'patrol_time' => 'datetime:Y-m-d H:i:s',
            'qr_scanned_at' => 'datetime:Y-m-d H:i:s',
            'photos' => 'array',
        ];
    }

    /**
     * Get qr_scanned_at with app timezone
     */
    public function getQrScannedAtAttribute($value)
    {
        if (!$value) return null;
        
        // Value sudah Carbon karena casting, tinggal convert timezone
        if ($value instanceof \Carbon\Carbon) {
            return $value->setTimezone(config('app.timezone'));
        }
        
        // Fallback jika value string
        if (is_string($value)) {
            try {
                $dateTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $value, 'UTC');
                return $dateTime->setTimezone(config('app.timezone'));
            } catch (\Exception $e) {
                return null;
            }
        }
        
        return $value;
    }

    /**
     * Set qr_scanned_at - ensure it's stored in UTC
     */
    public function setQrScannedAtAttribute($value)
    {
        if (!$value) {
            $this->attributes['qr_scanned_at'] = null;
            return;
        }

        // Convert app timezone to UTC for storage
        if ($value instanceof \Carbon\Carbon) {
            $this->attributes['qr_scanned_at'] = $value->setTimezone('UTC')->format('Y-m-d H:i:s');
        } elseif (is_string($value) && !empty($value)) {
            try {
                $parsed = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $value, config('app.timezone'));
                $this->attributes['qr_scanned_at'] = $parsed->setTimezone('UTC')->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                // Jika parsing gagal, coba format lain atau gunakan strtotime
                $this->attributes['qr_scanned_at'] = null;
            }
        }
    }

    /**
     * Get patrol_time with app timezone
     */
    public function getPatrolTimeAttribute($value)
    {
        if (!$value) return null;
        
        // Value sudah Carbon karena casting, tinggal convert timezone
        if ($value instanceof \Carbon\Carbon) {
            return $value->setTimezone(config('app.timezone'));
        }
        
        // Fallback jika value string
        if (is_string($value)) {
            try {
                $dateTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $value, 'UTC');
                return $dateTime->setTimezone(config('app.timezone'));
            } catch (\Exception $e) {
                return null;
            }
        }
        
        return $value;
    }

    /**
     * Set patrol_time - ensure it's stored in UTC
     */
    public function setPatrolTimeAttribute($value)
    {
        // Jika tidak ada value, gunakan current timestamp
        if (!$value) {
            $this->attributes['patrol_time'] = \Carbon\Carbon::now('UTC')->format('Y-m-d H:i:s');
            return;
        }

        // Convert app timezone to UTC for storage
        if ($value instanceof \Carbon\Carbon) {
            $this->attributes['patrol_time'] = $value->setTimezone('UTC')->format('Y-m-d H:i:s');
        } elseif (is_string($value) && !empty($value)) {
            try {
                $parsed = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $value, config('app.timezone'));
                $this->attributes['patrol_time'] = $parsed->setTimezone('UTC')->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                // Jika parsing gagal, gunakan current timestamp
                $this->attributes['patrol_time'] = \Carbon\Carbon::now('UTC')->format('Y-m-d H:i:s');
            }
        } else {
            // Tipe data tidak dikenali, gunakan current timestamp
            $this->attributes['patrol_time'] = \Carbon\Carbon::now('UTC')->format('Y-m-d H:i:s');
        }
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function violation(): BelongsTo
    {
        return $this->belongsTo(Violation::class);
    }

    public function action(): BelongsTo
    {
        return $this->belongsTo(Action::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(PatrolAttachment::class);
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class);
    }

    public function checkpoints(): HasMany
    {
        return $this->hasMany(PatrolCheckpoint::class);
    }

    /**
     * Check if patrol is validated via QR code scan
     */
    public function isValidated(): bool
    {
        return $this->qr_scanned_at !== null && !empty($this->qr_code_token);
    }

    /**
     * Mark patrol as validated with QR code scan
     */
    public function validateWithQrCode(string $token, ?string $ipAddress = null): bool
    {
        if ($this->qr_code_token === $token) {
            $this->update([
                'qr_scanned_at' => now(),
                'qr_scanned_ip' => $ipAddress ?? request()?->ip(),
            ]);
            return true;
        }
        return false;
    }

    /**
     * Generate unique QR code token for this patrol
     */
    public function generateQrToken(): void
    {
        if (empty($this->qr_code_token)) {
            $this->update(['qr_code_token' => \Illuminate\Support\Str::random(32)]);
        }
    }
}
