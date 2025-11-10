<?php

namespace App\GraphQL\Mutations;

use App\Services\AuthService;

class RefreshToken
{
    public function __construct(
        protected AuthService $authService
    ) {}

    /**
     * Refresh authentication token.
     */
    public function __invoke($rootValue, array $args, $context): array
    {
        return $this->authService->refreshToken($context->user());
    }
}
