<?php

namespace App\GraphQL\Mutations\Admin;

use App\Models\Service;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

final class DeleteService
{
    public function __invoke(mixed $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): bool
    {
        $service = Service::findOrFail($args['id']);
        $service->delete();

        return true;
    }
}
