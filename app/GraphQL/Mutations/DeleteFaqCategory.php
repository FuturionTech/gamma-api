<?php

namespace App\GraphQL\Mutations;

use App\Models\FaqCategory;

class DeleteFaqCategory
{
    public function __invoke($rootValue, array $args): array
    {
        try {
            FaqCategory::findOrFail($args['id'])->delete();
            return ['success' => true, 'message' => 'FAQ category deleted successfully.'];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Failed to delete FAQ category: '.$e->getMessage()];
        }
    }
}
