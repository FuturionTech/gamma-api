<?php

// Test Industries GraphQL mutations with authentication

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Industries GraphQL Mutations ===\n\n";

// Get an admin user and create a token
echo "Step 1: Getting authentication token...\n";
$admin = \App\Models\Administrator::first();
if (!$admin) {
    echo "Error: No administrator found. Please run seeders first.\n";
    exit(1);
}

$token = $admin->createToken('test-token')->plainTextToken;
echo "Token created successfully\n\n";

// Test 1: Create a new Industry
echo "=== Test 1: Create new Industry ===\n";
$createMutation = '
mutation {
    createIndustry(input: {
        title: "Test Industry"
        description: "This is a test industry for verification"
        short_description: "Test industry verification"
        icon: "test-icon"
        icon_color: "#FF0000"
        category: RETAIL
        order: 99
        is_active: true
    }) {
        id
        title
        description
        short_description
        icon
        icon_color
        category
        slug
        order
        is_active
    }
}
';

try {
    $response = \Illuminate\Support\Facades\Http::withToken($token)->post('http://localhost/graphql', [
        'query' => $createMutation
    ]);
    $result = $response->json();
    echo json_encode($result, JSON_PRETTY_PRINT) . "\n\n";
    $newIndustryId = $result['data']['createIndustry']['id'] ?? null;
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n\n";
    $newIndustryId = null;
}

// Test 2: Update the Industry
if ($newIndustryId) {
    echo "=== Test 2: Update Industry ===\n";
    $updateMutation = '
    mutation {
        updateIndustry(id: ' . $newIndustryId . ', input: {
            title: "Updated Test Industry"
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
            'query' => $updateMutation
        ]);
        echo json_encode($response->json(), JSON_PRETTY_PRINT) . "\n\n";
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n\n";
    }
}

// Test 3: Query the updated industry
if ($newIndustryId) {
    echo "=== Test 3: Query updated industry ===\n";
    $queryUpdated = '
    query {
        industry(id: ' . $newIndustryId . ') {
            id
            title
            short_description
            icon_color
            slug
        }
    }
    ';

    try {
        $response = \Illuminate\Support\Facades\Http::post('http://localhost/graphql', [
            'query' => $queryUpdated
        ]);
        echo json_encode($response->json(), JSON_PRETTY_PRINT) . "\n\n";
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n\n";
    }
}

// Test 4: Delete the Industry
if ($newIndustryId) {
    echo "=== Test 4: Delete Industry ===\n";
    $deleteMutation = '
    mutation {
        deleteIndustry(id: ' . $newIndustryId . ') {
            success
            message
        }
    }
    ';

    try {
        $response = \Illuminate\Support\Facades\Http::withToken($token)->post('http://localhost/graphql', [
            'query' => $deleteMutation
        ]);
        echo json_encode($response->json(), JSON_PRETTY_PRINT) . "\n\n";
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n\n";
    }
}

// Test 5: Verify deletion
echo "=== Test 5: Verify final state ===\n";
$verifyQuery = '
query {
    industries {
        id
        title
    }
}
';

try {
    $response = \Illuminate\Support\Facades\Http::post('http://localhost/graphql', [
        'query' => $verifyQuery
    ]);
    $result = $response->json();
    echo "Total industries: " . count($result['data']['industries']) . " (Should be 6 - original seeded data)\n\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n\n";
}

// Test 6: Test authentication requirement (should fail without token)
echo "=== Test 6: Test authentication requirement ===\n";
$createWithoutAuth = '
mutation {
    createIndustry(input: {
        title: "Unauthorized Test"
        category: RETAIL
    }) {
        id
        title
    }
}
';

try {
    $response = \Illuminate\Support\Facades\Http::post('http://localhost/graphql', [
        'query' => $createWithoutAuth
    ]);
    $result = $response->json();
    if (isset($result['errors'])) {
        echo "✓ Authentication required (as expected)\n";
        echo "Error message: " . ($result['errors'][0]['message'] ?? 'Unknown error') . "\n\n";
    } else {
        echo "✗ Unexpected: Mutation succeeded without authentication\n\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n\n";
}

echo "=== All mutation tests complete ===\n";
