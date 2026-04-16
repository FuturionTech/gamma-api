<?php

namespace App\GraphQL\Mutations;

use App\Models\CertificationCategory;

class DeleteCertificationCategory
{
    public function __invoke($rootValue, array $args): array
    {
        try {
            $category = CertificationCategory::findOrFail($args['id']);

            if ($category->certifications()->exists()) {
                return [
                    'success' => false,
                    'message' => 'Cannot delete a category that still has certifications attached.',
                ];
            }

            $category->delete();

            return [
                'success' => true,
                'message' => 'Certification category deleted successfully.',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete certification category: '.$e->getMessage(),
            ];
        }
    }
}
