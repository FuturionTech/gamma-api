<?php

namespace App\GraphQL\Mutations;

use App\Models\ProcessStep;

class DeleteProcessStep
{
    /**
     * Delete a process step.
     */
    public function __invoke($rootValue, array $args): array
    {
        try {
            $processStep = ProcessStep::findOrFail($args['id']);
            $processStep->delete();

            return [
                'success' => true,
                'message' => 'Process step deleted successfully.',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete process step: ' . $e->getMessage(),
            ];
        }
    }
}
