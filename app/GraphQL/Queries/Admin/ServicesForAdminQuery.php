<?php

namespace App\GraphQL\Queries\Admin;

use App\Models\Service;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

final class ServicesForAdminQuery
{
    public function __invoke(mixed $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): array
    {
        $query = Service::query()
            ->with(['translations'])
            ->orderBy('order');

        if (array_key_exists('isActive', $args) && $args['isActive'] !== null) {
            $query->where('is_active', (bool) $args['isActive']);
        }

        return $query->get()->map(function (Service $service) {
            return [
                'id'           => $service->id,
                'slug'         => $service->slug,
                'icon'         => $service->icon,
                'iconColor'    => $service->icon_color,
                'category'     => $service->category,
                'order'        => $service->order,
                'isActive'     => $service->is_active,
                'publishedAt'  => $service->published_at,
                'translations' => $service->translations->map(fn ($t) => [
                    'locale' => $t->locale,
                    'title'  => $t->title,
                ])->all(),
                'createdAt'    => $service->created_at,
                'updatedAt'    => $service->updated_at,
            ];
        })->all();
    }
}
