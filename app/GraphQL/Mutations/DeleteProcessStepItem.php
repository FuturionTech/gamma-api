<?php

namespace App\GraphQL\Mutations;

use App\Models\ProcessStepItem;

class DeleteProcessStepItem
{
    /**
     * Delete a process step item.
     */
    public function __invoke($rootValue, array $args): array
    {
        try {
            $processStepItem = ProcessStepItem::findOrFail($args['id']);
            $processStepItem->delete();

            return [
                'success' => true,
                'message' => 'Process step item deleted successfully.',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete process step item: ' . $e->getMessage(),
            ];
        }
    }
}
