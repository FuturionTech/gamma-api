<?php

namespace App\GraphQL\Mutations\Admin;

use App\Models\SiteSetting;

final class UpdateSiteSetting
{
    public function __invoke(mixed $root, array $args): SiteSetting
    {
        $setting = SiteSetting::where('key', $args['key'])->firstOrFail();
        $setting->update(['value' => $args['value']]);

        return $setting->fresh();
    }
}
