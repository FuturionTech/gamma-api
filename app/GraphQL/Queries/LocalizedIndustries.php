<?php

namespace App\GraphQL\Queries;

use App\Models\Industry;

final class LocalizedIndustries
{
    /**
     * Resolve industries with optional locale override.
     *
     * @param  array{locale?: string, is_active?: bool, category?: string, limit?: int}  $args
     * @return \Illuminate\Database\Eloquent\Collection<int, Industry>
     */
    public function __invoke($_, array $args)
    {
        if (isset($args['locale'])) {
            app()->setLocale($args['locale']);
        }

        $query = Industry::query()->ordered();

        if (isset($args['is_active'])) {
            $query->where('is_active', $args['is_active']);
        }

        if (isset($args['category'])) {
            $query->where('category', $args['category']);
        }

        if (isset($args['limit'])) {
            $query->limit($args['limit']);
        }

        return $query->get();
    }
}
