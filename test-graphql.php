<?php

// Test ProcessSteps GraphQL queries directly

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

// Test 1: Query all process steps
echo "=== Test 1: Query all process steps ===\n";
$query = '
query {
    processSteps {
        id
        title
        short_description
        step_number
        icon
        icon_color
        slug
        order
        is_active
        items {
            id
            title
            icon
            order
        }
    }
}
';

try {
    $response = \Illuminate\Support\Facades\Http::post('http://localhost/graphql', [
        'query' => $query
    ]);
    echo $response->body() . "\n\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n\n";
}

// Test 2: Query single process step
echo "=== Test 2: Query single process step (ID: 1) ===\n";
$query2 = '
query {
    processStep(id: 1) {
        id
        title
        description
        short_description
        step_number
        icon
        icon_color
        slug
        items {
            id
            title
            description
            icon
            order
        }
    }
}
';

try {
    $response = \Illuminate\Support\Facades\Http::post('http://localhost/graphql', [
        'query' => $query2
    ]);
    echo $response->body() . "\n\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n\n";
}

// Test 3: Check if process steps exist in database
echo "=== Test 3: Check database for process steps ===\n";
$count = \App\Models\ProcessStep::count();
echo "Process steps in database: $count\n";

if ($count > 0) {
    echo "\nFirst process step:\n";
    $step = \App\Models\ProcessStep::with('items')->first();
    echo json_encode($step->toArray(), JSON_PRETTY_PRINT) . "\n";
}
