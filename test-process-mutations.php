<?php

// Test ProcessSteps GraphQL mutations with authentication

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing ProcessSteps GraphQL Mutations ===\n\n";

// Get an admin user and create a token
echo "Step 1: Getting authentication token...\n";
$admin = \App\Models\Administrator::first();
if (!$admin) {
    echo "Error: No administrator found. Please run seeders first.\n";
    exit(1);
}

$token = $admin->createToken('test-token')->plainTextToken;
echo "Token created successfully\n\n";

// Test 1: Create a new ProcessStep
echo "=== Test 1: Create new ProcessStep ===\n";
$createStepMutation = '
mutation {
    createProcessStep(input: {
        title: "Test Step"
        description: "This is a test step for verification"
        short_description: "Test step verification"
        step_number: 99
        icon: "test-icon"
        icon_color: "#FF0000"
        order: 99
        is_active: true
    }) {
        id
        title
        description
        step_number
        icon
        icon_color
        slug
        order
        is_active
    }
}
';

try {
    $response = \Illuminate\Support\Facades\Http::withToken($token)->post('http://localhost/graphql', [
        'query' => $createStepMutation
    ]);
    $result = $response->json();
    echo json_encode($result, JSON_PRETTY_PRINT) . "\n\n";
    $newStepId = $result['data']['createProcessStep']['id'] ?? null;
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n\n";
    $newStepId = null;
}

// Test 2: Create a ProcessStepItem for the new step
if ($newStepId) {
    echo "=== Test 2: Create ProcessStepItem ===\n";
    $createItemMutation = '
    mutation {
        createProcessStepItem(input: {
            process_step_id: ' . $newStepId . '
            title: "Test Item"
            description: "This is a test item"
            icon: "test-check"
            order: 1
        }) {
            id
            process_step_id
            title
            description
            icon
            order
        }
    }
    ';

    try {
        $response = \Illuminate\Support\Facades\Http::withToken($token)->post('http://localhost/graphql', [
            'query' => $createItemMutation
        ]);
        $result = $response->json();
        echo json_encode($result, JSON_PRETTY_PRINT) . "\n\n";
        $newItemId = $result['data']['createProcessStepItem']['id'] ?? null;
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n\n";
        $newItemId = null;
    }
}

// Test 3: Update the ProcessStep
if ($newStepId) {
    echo "=== Test 3: Update ProcessStep ===\n";
    $updateStepMutation = '
    mutation {
        updateProcessStep(id: ' . $newStepId . ', input: {
            title: "Updated Test Step"
            short_description: "Updated description"
            icon_color: "#00FF00"
        }) {
            id
            title
            short_description
            icon_color
        }
    }
    ';

    try {
        $response = \Illuminate\Support\Facades\Http::withToken($token)->post('http://localhost/graphql', [
            'query' => $updateStepMutation
        ]);
        echo json_encode($response->json(), JSON_PRETTY_PRINT) . "\n\n";
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n\n";
    }
}

// Test 4: Update the ProcessStepItem
if ($newItemId ?? null) {
    echo "=== Test 4: Update ProcessStepItem ===\n";
    $updateItemMutation = '
    mutation {
        updateProcessStepItem(id: ' . $newItemId . ', input: {
            title: "Updated Test Item"
            icon: "updated-check"
        }) {
            id
            title
            icon
        }
    }
    ';

    try {
        $response = \Illuminate\Support\Facades\Http::withToken($token)->post('http://localhost/graphql', [
            'query' => $updateItemMutation
        ]);
        echo json_encode($response->json(), JSON_PRETTY_PRINT) . "\n\n";
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n\n";
    }
}

// Test 5: Delete the ProcessStepItem
if ($newItemId ?? null) {
    echo "=== Test 5: Delete ProcessStepItem ===\n";
    $deleteItemMutation = '
    mutation {
        deleteProcessStepItem(id: ' . $newItemId . ') {
            success
            message
        }
    }
    ';

    try {
        $response = \Illuminate\Support\Facades\Http::withToken($token)->post('http://localhost/graphql', [
            'query' => $deleteItemMutation
        ]);
        echo json_encode($response->json(), JSON_PRETTY_PRINT) . "\n\n";
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n\n";
    }
}

// Test 6: Delete the ProcessStep
if ($newStepId) {
    echo "=== Test 6: Delete ProcessStep ===\n";
    $deleteStepMutation = '
    mutation {
        deleteProcessStep(id: ' . $newStepId . ') {
            success
            message
        }
    }
    ';

    try {
        $response = \Illuminate\Support\Facades\Http::withToken($token)->post('http://localhost/graphql', [
            'query' => $deleteStepMutation
        ]);
        echo json_encode($response->json(), JSON_PRETTY_PRINT) . "\n\n";
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n\n";
    }
}

// Test 7: Verify deletion by querying all steps
echo "=== Test 7: Verify final state ===\n";
$verifyQuery = '
query {
    processSteps {
        id
        title
        step_number
    }
}
';

try {
    $response = \Illuminate\Support\Facades\Http::post('http://localhost/graphql', [
        'query' => $verifyQuery
    ]);
    $result = $response->json();
    echo "Total process steps: " . count($result['data']['processSteps']) . "\n";
    echo "Should be 6 (original seeded data)\n\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n\n";
}

echo "=== All mutation tests complete ===\n";
