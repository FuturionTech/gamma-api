<?php

namespace App\GraphQL\Queries;

use App\Models\Solution;

final class LocalizedSolutions
{
    /**
     * Resolve solutions with optional locale override.
     *
     * @param  array{locale?: string, is_active?: bool, limit?: int}  $args
     * @return \Illuminate\Database\Eloquent\Collection<int, Solution>
     */
    public function __invoke($_, array $args)
    {
        if (isset($args['locale'])) {
            app()->setLocale($args['locale']);
        }

        $query = Solution::query()->orderBy('order');

        if (isset($args['is_active'])) {
            $query->where('is_active', $args['is_active']);
        }

        if (isset($args['limit'])) {
            $query->limit($args['limit']);
        }

        return $query->get();
    }
}
