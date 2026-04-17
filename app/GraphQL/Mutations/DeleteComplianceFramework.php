<?php

namespace App\GraphQL\Mutations;

use App\Models\ComplianceFramework;

class DeleteComplianceFramework
{
    public function __invoke($rootValue, array $args): array
    {
        try {
            ComplianceFramework::findOrFail($args['id'])->delete();
            return ['success' => true, 'message' => 'Compliance framework deleted successfully.'];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Failed to delete: '.$e->getMessage()];
        }
    }
}
