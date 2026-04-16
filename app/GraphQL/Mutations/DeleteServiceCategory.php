<?php

namespace App\GraphQL\Mutations;

use App\Models\ServiceCategory;

class DeleteServiceCategory
{
    public function __invoke($rootValue, array $args): array
    {
        try {
            $category = ServiceCategory::findOrFail($args['id']);
            $category->delete();

            return [
                'success' => true,
                'message' => 'Service category deleted successfully.',
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete service category: '.$e->getMessage(),
            ];
        }
    }
}
