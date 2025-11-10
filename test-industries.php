<?php

// Test Industries GraphQL queries and mutations

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Industries GraphQL API ===\n\n";

// Test 1: Query all industries
echo "=== Test 1: Query all industries ===\n";
$query = '
query {
    industries {
        id
        title
        short_description
        description
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
    $response = \Illuminate\Support\Facades\Http::post('http://localhost/graphql', [
        'query' => $query
    ]);
    $result = $response->json();
    echo "Total industries: " . count($result['data']['industries'] ?? []) . "\n";
    echo json_encode($result, JSON_PRETTY_PRINT) . "\n\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n\n";
}

// Test 2: Query single industry by ID
echo "=== Test 2: Query single industry by ID (id: 1) ===\n";
$query2 = '
query {
    industry(id: 1) {
        id
        title
        description
        short_description
        icon
        icon_color
        category
        slug
    }
}
';

try {
    $response = \Illuminate\Support\Facades\Http::post('http://localhost/graphql', [
        'query' => $query2
    ]);
    echo json_encode($response->json(), JSON_PRETTY_PRINT) . "\n\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n\n";
}

// Test 3: Query industry by slug
echo "=== Test 3: Query industry by slug (slug: 'healthcare-services') ===\n";
$query3 = '
query {
    industryBySlug(slug: "healthcare-services") {
        id
        title
        description
        icon
        icon_color
        category
    }
}
';

try {
    $response = \Illuminate\Support\Facades\Http::post('http://localhost/graphql', [
        'query' => $query3
    ]);
    echo json_encode($response->json(), JSON_PRETTY_PRINT) . "\n\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n\n";
}

// Test 4: Query industries filtered by category
echo "=== Test 4: Query industries filtered by category (HEALTHCARE) ===\n";
$query4 = '
query {
    industries(category: HEALTHCARE) {
        id
        title
        category
    }
}
';

try {
    $response = \Illuminate\Support\Facades\Http::post('http://localhost/graphql', [
        'query' => $query4
    ]);
    echo json_encode($response->json(), JSON_PRETTY_PRINT) . "\n\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n\n";
}

// Test 5: Query active industries only
echo "=== Test 5: Query active industries only ===\n";
$query5 = '
query {
    industries(is_active: true) {
        id
        title
        is_active
    }
}
';

try {
    $response = \Illuminate\Support\Facades\Http::post('http://localhost/graphql', [
        'query' => $query5
    ]);
    $result = $response->json();
    echo "Active industries: " . count($result['data']['industries'] ?? []) . "\n";
    echo json_encode($result, JSON_PRETTY_PRINT) . "\n\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n\n";
}

// Test 6: Verify database count
echo "=== Test 6: Verify database ===\n";
$count = \App\Models\Industry::count();
echo "Industries in database: $count (Expected: 6)\n\n";

if ($count > 0) {
    echo "Sample industry:\n";
    $industry = \App\Models\Industry::first();
    echo "- Title: {$industry->title}\n";
    echo "- Category: {$industry->category}\n";
    echo "- Icon: {$industry->icon}\n";
    echo "- Color: {$industry->icon_color}\n";
    echo "- Slug: {$industry->slug}\n\n";
}

echo "=== Public Query Tests Complete ===\n";
