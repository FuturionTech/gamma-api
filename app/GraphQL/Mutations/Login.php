<?php

namespace App\GraphQL\Mutations;

use App\Models\Administrator;
use Illuminate\Support\Facades\Hash;
use GraphQL\Error\Error;

final class Login
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        $administrator = Administrator::where('email', $args['email'])->first();

        if (!$administrator || !Hash::check($args['password'], $administrator->password)) {
            throw new Error('Invalid credentials');
        }

        // Create token with abilities
        $token = $administrator->createToken(
            'api-token',
            ['*']
        )->plainTextToken;

        return [
            'token' => $token,
            'administrator' => $administrator,
        ];
    }
}

