<?php

namespace App\GraphQL\Mutations;

use App\Services\AuthService;

class RequestOtp
{
    public function __construct(
        protected AuthService $authService
    ) {}

    /**
     * Request OTP for administrator authentication.
     */
    public function __invoke($rootValue, array $args): array
    {
        return $this->authService->requestOtp(
            $args['identifier'],
            $args['method'],
            $args['language']
        );
    }
}
