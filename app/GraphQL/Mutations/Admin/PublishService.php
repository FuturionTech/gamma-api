<?php

namespace App\GraphQL\Mutations\Admin;

use App\GraphQL\Queries\Admin\ServiceForAdminQuery;
use App\Models\Service;
use App\Models\ServiceTranslation;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

final class PublishService
{
    public function publish(mixed $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): array
    {
        $service = Service::findOrFail($args['id']);
        $service->update(['published_at' => now()]);

        return ServiceForAdminQuery::projectService(
            $service->fresh()->load(ServiceForAdminQuery::EAGER_LOADS)
        );
    }

    public function unpublish(mixed $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): array
    {
        $service = Service::findOrFail($args['id']);
        $service->update(['published_at' => null]);

        return ServiceForAdminQuery::projectService(
            $service->fresh()->load(ServiceForAdminQuery::EAGER_LOADS)
        );
    }

    public function publishTranslation(mixed $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): array
    {
        $translation = ServiceTranslation::where('service_id', $args['serviceId'])
            ->where('locale', $args['locale'])
            ->firstOrFail();

        $translation->update(['published_at' => now()]);

        return ServiceForAdminQuery::projectTranslation($translation->fresh());
    }

    public function unpublishTranslation(mixed $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): array
    {
        $translation = ServiceTranslation::where('service_id', $args['serviceId'])
            ->where('locale', $args['locale'])
            ->firstOrFail();

        $translation->update(['published_at' => null]);

        return ServiceForAdminQuery::projectTranslation($translation->fresh());
    }
}
