<?php

namespace App\GraphQL\Queries;

use App\Models\Service;

final class LocalizedServices
{
    /**
     * Resolve services with optional locale override.
     *
     * @param  array{locale?: string, is_active?: bool, limit?: int}  $args
     * @return \Illuminate\Database\Eloquent\Collection<int, Service>
     */
    public function __invoke($_, array $args)
    {
        if (isset($args['locale'])) {
            app()->setLocale($args['locale']);
        }

        $query = Service::query()->orderBy('order');

        if (isset($args['is_active'])) {
            $query->where('is_active', $args['is_active']);
        }

        if (isset($args['limit'])) {
            $query->limit($args['limit']);
        }

        return $query->get();
    }
}
