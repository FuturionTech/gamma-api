<?php

namespace App\GraphQL\Mutations;

use App\Services\AuthService;

class VerifyOtp
{
    public function __construct(
        protected AuthService $authService
    ) {}

    /**
     * Verify OTP and authenticate administrator.
     */
    public function __invoke($rootValue, array $args): array
    {
        return $this->authService->verifyOtp(
            $args['identifier'],
            $args['otp_code'],
            $args['method']
        );
    }
}
