<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpCode extends Model
{
    protected $fillable = [
        'identifier',
        'code',
        'delivery_method',
        'language',
        'expires_at',
        'verified_at',
        'attempts',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'attempts' => 'integer',
    ];

    /**
     * Check if the OTP code is valid.
     */
    public function isValid(): bool
    {
        return $this->verified_at === null
            && $this->expires_at > now()
            && $this->attempts < 5;
    }

    /**
     * Check if the OTP code has expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at <= now();
    }

    /**
     * Check if the OTP has been verified.
     */
    public function isVerified(): bool
    {
        return $this->verified_at !== null;
    }

    /**
     * Check if maximum attempts have been reached.
     */
    public function hasMaxAttemptsReached(): bool
    {
        return $this->attempts >= 5;
    }

    /**
     * Increment failed verification attempts.
     */
    public function incrementAttempts(): void
    {
        $this->increment('attempts');
    }

    /**
     * Mark the OTP as verified.
     */
    public function markAsVerified(): void
    {
        $this->update(['verified_at' => now()]);
    }

    /**
     * Generate a random 6-digit OTP code.
     */
    public static function generateCode(): string
    {
        return str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Invalidate all previous unverified OTP codes for the identifier.
     */
    public static function invalidatePrevious(string $identifier, string $deliveryMethod): void
    {
        static::where('identifier', $identifier)
            ->where('delivery_method', $deliveryMethod)
            ->whereNull('verified_at')
            ->delete();
    }
}
