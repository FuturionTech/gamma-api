<?php

namespace App\GraphQL\Queries;

use App\Models\Administrator;

class Me
{
    /**
     * Get the authenticated administrator.
     */
    public function __invoke($rootValue, array $args, $context): ?Administrator
    {
        return $context->user();
    }
}
