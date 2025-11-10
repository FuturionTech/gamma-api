<?php

namespace App\GraphQL\Mutations;

use App\Models\Industry;

class DeleteIndustry
{
    /**
     * Delete an industry.
     */
    public function __invoke($rootValue, array $args): array
    {
        try {
            $industry = Industry::findOrFail($args['id']);
            $industry->delete();

            return [
                'success' => true,
                'message' => 'Industry deleted successfully.',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete industry: ' . $e->getMessage(),
            ];
        }
    }
}
