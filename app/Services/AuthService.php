<?php

namespace App\Services;

use App\Models\Administrator;
use App\Models\OtpCode;
use App\Notifications\OtpCodeNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        protected SmsService $smsService
    ) {}

    /**
     * Request OTP for administrator authentication.
     *
     * @param string $identifier Email or phone number
     * @param string $method EMAIL or SMS
     * @param string $language FR or EN
     * @return array
     * @throws ValidationException
     */
    public function requestOtp(string $identifier, string $method, string $language): array
    {
        // Validate identifier based on method
        if ($method === 'EMAIL') {
            if (!filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
                throw ValidationException::withMessages([
                    'identifier' => ['The identifier must be a valid email address.'],
                ]);
            }

            // Check if administrator exists with this email
            $administrator = Administrator::where('email', $identifier)->first();
            if (!$administrator) {
                throw ValidationException::withMessages([
                    'identifier' => ['No administrator found with this email address.'],
                ]);
            }
        } elseif ($method === 'SMS') {
            // Check if administrator exists with this phone number
            $administrator = Administrator::where('phone_number', $identifier)->first();
            if (!$administrator) {
                throw ValidationException::withMessages([
                    'identifier' => ['No administrator found with this phone number.'],
                ]);
            }
        }

        // Check if account is active
        if (!$administrator->isActive()) {
            throw ValidationException::withMessages([
                'identifier' => ['Your account is not active. Please contact an administrator.'],
            ]);
        }

        // Invalidate previous OTP codes
        OtpCode::invalidatePrevious($identifier, $method);

        // Generate new OTP code
        $code = OtpCode::generateCode();

        // Store OTP in database
        OtpCode::create([
            'identifier' => $identifier,
            'code' => $code,
            'delivery_method' => $method,
            'language' => $language,
            'expires_at' => now()->addMinutes(10),
        ]);

        // Send OTP based on method
        if ($method === 'EMAIL') {
            $this->sendOtpViaEmail($identifier, $code, $language, $administrator);
        } elseif ($method === 'SMS') {
            $this->sendOtpViaSms($identifier, $code, $language, $administrator);
        }

        $messages = [
            'FR' => [
                'EMAIL' => 'Un code OTP a été envoyé à votre adresse email.',
                'SMS' => 'Un code OTP a été envoyé à votre numéro de téléphone.',
            ],
            'EN' => [
                'EMAIL' => 'An OTP code has been sent to your email address.',
                'SMS' => 'An OTP code has been sent to your phone number.',
            ],
        ];

        return [
            'success' => true,
            'message' => $messages[$language][$method] ?? $messages['EN'][$method],
        ];
    }

    /**
     * Verify OTP and authenticate administrator.
     *
     * @param string $identifier Email or phone number
     * @param string $otpCode The OTP code to verify
     * @param string $method EMAIL or SMS
     * @return array
     * @throws ValidationException
     */
    public function verifyOtp(string $identifier, string $otpCode, string $method): array
    {
        // Find the most recent valid OTP code
        $otp = OtpCode::where('identifier', $identifier)
            ->where('delivery_method', $method)
            ->whereNull('verified_at')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$otp) {
            throw ValidationException::withMessages([
                'otp_code' => ['No valid OTP code found. Please request a new code.'],
            ]);
        }

        // Check if OTP has expired
        if ($otp->isExpired()) {
            throw ValidationException::withMessages([
                'otp_code' => ['The OTP code has expired. Please request a new code.'],
            ]);
        }

        // Check if maximum attempts reached
        if ($otp->hasMaxAttemptsReached()) {
            throw ValidationException::withMessages([
                'otp_code' => ['Maximum verification attempts reached. Please request a new code.'],
            ]);
        }

        // Verify the code
        if ($otp->code !== $otpCode) {
            $otp->incrementAttempts();

            throw ValidationException::withMessages([
                'otp_code' => ['The OTP code is incorrect.'],
            ]);
        }

        // Mark OTP as verified
        $otp->markAsVerified();

        // Find administrator by identifier
        $administrator = $method === 'EMAIL'
            ? Administrator::where('email', $identifier)->first()
            : Administrator::where('phone_number', $identifier)->first();

        if (!$administrator) {
            throw ValidationException::withMessages([
                'identifier' => ['Administrator not found.'],
            ]);
        }

        // Check if account is active
        if (!$administrator->isActive()) {
            throw ValidationException::withMessages([
                'identifier' => ['Your account is not active. Please contact an administrator.'],
            ]);
        }

        // Update last login details
        $administrator->update([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
            'failed_login_attempts' => 0,
        ]);

        // Create Sanctum token
        $token = $administrator->createToken('auth_token', ['*'], now()->addDays(7));

        return [
            'administrator' => $administrator->load('roles.permissions'),
            'token' => $token->plainTextToken,
            'expires_at' => $token->accessToken->expires_at,
        ];
    }

    /**
     * Logout administrator by revoking current token.
     */
    public function logout(Administrator $administrator): bool
    {
        // Revoke current token
        $administrator->currentAccessToken()->delete();

        return true;
    }

    /**
     * Refresh authentication token.
     */
    public function refreshToken(Administrator $administrator): array
    {
        // Revoke old token
        $administrator->currentAccessToken()->delete();

        // Create new token
        $token = $administrator->createToken('auth_token', ['*'], now()->addDays(7));

        return [
            'administrator' => $administrator->load('roles.permissions'),
            'token' => $token->plainTextToken,
            'expires_at' => $token->accessToken->expires_at,
        ];
    }

    /**
     * Send OTP via email.
     */
    protected function sendOtpViaEmail(string $email, string $code, string $language, Administrator $administrator): void
    {
        try {
            // Send email notification to the administrator
            $administrator->notify(new OtpCodeNotification($code, $language));

            Log::info("OTP email sent to {$email}", [
                'administrator_id' => $administrator->id,
                'language' => $language,
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send OTP email to {$email}: " . $e->getMessage());
            throw ValidationException::withMessages([
                'identifier' => ['Failed to send OTP email. Please try again later.'],
            ]);
        }
    }

    /**
     * Send OTP via SMS.
     */
    protected function sendOtpViaSms(string $phoneNumber, string $code, string $language, Administrator $administrator): void
    {
        $messages = [
            'FR' => "Votre code OTP Gamma est: {$code}. Il expire dans 10 minutes.",
            'EN' => "Your Gamma OTP code is: {$code}. It expires in 10 minutes.",
        ];

        $message = $messages[$language] ?? $messages['EN'];

        try {
            $this->smsService->send($phoneNumber, $message);
        } catch (\Exception $e) {
            Log::error("Failed to send OTP SMS to {$phoneNumber}: " . $e->getMessage());
            throw ValidationException::withMessages([
                'identifier' => ['Failed to send OTP. Please try again later.'],
            ]);
        }
    }
}
