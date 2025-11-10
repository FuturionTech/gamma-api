<?php

namespace App\GraphQL\Mutations;

final class Logout
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args, $context)
    {
        /** @var \App\Models\Administrator $user */
        $user = $context->user();
        
        // Delete current access token
        $user->currentAccessToken()->delete();

        return [
            'success' => true,
            'message' => 'Successfully logged out',
        ];
    }
}

