<?php

namespace App\GraphQL\Queries;

use App\Models\SiteSetting;

final class SiteSettingsQuery
{
    /**
     * @return array<int, SiteSetting>
     */
    public function __invoke(mixed $root, array $args): array
    {
        $query = SiteSetting::query();

        if (isset($args['group'])) {
            $query->where('group', $args['group']);
        }

        return $query->get()->all();
    }

    public function single(mixed $root, array $args): ?SiteSetting
    {
        return SiteSetting::where('key', $args['key'])->first();
    }
}
