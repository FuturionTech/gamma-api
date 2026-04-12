<?php

namespace App\GraphQL\Mutations\Admin;

use App\GraphQL\Queries\Admin\ServiceForAdminQuery;
use App\Models\Service;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

final class UpsertService
{
    public function create(mixed $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): array
    {
        $input = $args['input'];

        return DB::transaction(function () use ($input) {
            $service = Service::create([
                'slug'       => $input['slug'],
                'icon'       => $input['icon'] ?? null,
                'icon_color' => $input['iconColor'] ?? null,
                'category'   => $input['category'] ?? null,
                'order'      => $input['order'] ?? 0,
                'is_active'  => $input['isActive'] ?? true,
            ]);

            $this->syncTranslations($service, $input['translations'] ?? []);

            return $this->resolveService($service);
        });
    }

    public function update(mixed $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): array
    {
        $service = Service::findOrFail($args['id']);
        $input = $args['input'];

        return DB::transaction(function () use ($service, $input) {
            $updates = [];

            if (isset($input['slug'])) {
                $updates['slug'] = $input['slug'];
            }
            if (array_key_exists('icon', $input)) {
                $updates['icon'] = $input['icon'];
            }
            if (array_key_exists('iconColor', $input)) {
                $updates['icon_color'] = $input['iconColor'];
            }
            if (array_key_exists('category', $input)) {
                $updates['category'] = $input['category'];
            }
            if (isset($input['order'])) {
                $updates['order'] = $input['order'];
            }
            if (isset($input['isActive'])) {
                $updates['is_active'] = $input['isActive'];
            }

            if ($updates) {
                $service->update($updates);
            }

            if (isset($input['translations'])) {
                $this->syncTranslations($service, $input['translations']);
            }

            return $this->resolveService($service);
        });
    }

    /**
     * Sync translations from camelCase GraphQL input to snake_case DB columns.
     */
    private function syncTranslations(Service $service, array $translations): void
    {
        foreach ($translations as $trans) {
            $locale = $trans['locale'];
            unset($trans['locale']);

            $mapped = [];
            foreach ($trans as $key => $value) {
                $mapped[Str::snake($key)] = $value;
            }

            $service->translateOrNew($locale)->fill($mapped)->save();
        }
    }

    /**
     * Re-fetch the service with all relations and return the admin projection.
     */
    private function resolveService(Service $service): array
    {
        return ServiceForAdminQuery::projectService(
            $service->fresh()->load(ServiceForAdminQuery::EAGER_LOADS)
        );
    }
}
