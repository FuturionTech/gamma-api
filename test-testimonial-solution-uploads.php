<?php

// Test Testimonial and Solution image uploads

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Testimonial and Solution Image Uploads ===\n\n";

// Get authentication token
echo "Step 1: Getting authentication token...\n";
$admin = \App\Models\Administrator::first();
if (!$admin) {
    echo "Error: No administrator found. Please run seeders first.\n";
    exit(1);
}

$token = $admin->createToken('upload-test-token')->plainTextToken;
echo "Token created successfully\n\n";

// Test 1: Create Testimonial with base64 image
echo "=== Test 1: Create Testimonial with base64 image ===\n";

// Create a simple 10x10 red image in base64
$image = imagecreatetruecolor(10, 10);
$red = imagecolorallocate($image, 255, 0, 0);
imagefill($image, 0, 0, $red);
ob_start();
imagejpeg($image, null, 90);
$imageData = ob_get_clean();
imagedestroy($image);
$base64Image = 'data:image/jpeg;base64,' . base64_encode($imageData);

$createTestimonialMutation = '
mutation($input: CreateTestimonialInput!) {
    createTestimonial(input: $input) {
        id
        name
        content
        image_url
        rating
    }
}
';

$timestamp = time();
$variables = [
    'input' => [
        'name' => 'Test Client ' . $timestamp,
        'content' => 'This is a test testimonial with image upload.',
        'image' => $base64Image,
        'company' => 'Test Company',
        'position' => 'CEO',
        'rating' => 5
    ]
];

try {
    $response = \Illuminate\Support\Facades\Http::withToken($token)->post('http://localhost/graphql', [
        'query' => $createTestimonialMutation,
        'variables' => $variables
    ]);

    $result = $response->json();

    if (isset($result['errors'])) {
        echo "✗ CREATE failed: " . ($result['errors'][0]['message'] ?? 'Unknown error') . "\n";
    } else {
        $testimonialId = $result['data']['createTestimonial']['id'] ?? null;
        $imageUrl = $result['data']['createTestimonial']['image_url'] ?? null;

        if ($testimonialId) {
            echo "✓ Testimonial created successfully (ID: {$testimonialId})\n";
        }

        if ($imageUrl) {
            echo "✓ Image uploaded: {$imageUrl}\n";
        } else {
            echo "✗ No image URL returned\n";
        }
    }
} catch (\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 2: Create Solution with base64 image
echo "=== Test 2: Create Solution with base64 hero image ===\n";

// Create a simple 10x10 blue image in base64
$image = imagecreatetruecolor(10, 10);
$blue = imagecolorallocate($image, 0, 0, 255);
imagefill($image, 0, 0, $blue);
ob_start();
imagejpeg($image, null, 90);
$imageData = ob_get_clean();
imagedestroy($image);
$base64Image = 'data:image/jpeg;base64,' . base64_encode($imageData);

$createSolutionMutation = '
mutation($input: CreateSolutionInput!) {
    createSolution(input: $input) {
        id
        title
        hero_image_url
        slug
    }
}
';

$timestamp2 = time();
$variables = [
    'input' => [
        'title' => 'Test Solution with Hero Image ' . $timestamp2,
        'subtitle' => 'Testing hero image upload',
        'description' => 'This is a test solution with hero image upload.',
        'hero_image' => $base64Image,
        'industry_category' => 'HEALTHCARE'
    ]
];

try {
    $response = \Illuminate\Support\Facades\Http::withToken($token)->post('http://localhost/graphql', [
        'query' => $createSolutionMutation,
        'variables' => $variables
    ]);

    $result = $response->json();

    if (isset($result['errors'])) {
        echo "✗ CREATE failed: " . ($result['errors'][0]['message'] ?? 'Unknown error') . "\n";
    } else {
        $solutionId = $result['data']['createSolution']['id'] ?? null;
        $heroImageUrl = $result['data']['createSolution']['hero_image_url'] ?? null;

        if ($solutionId) {
            echo "✓ Solution created successfully (ID: {$solutionId})\n";
        }

        if ($heroImageUrl) {
            echo "✓ Hero image uploaded: {$heroImageUrl}\n";
        } else {
            echo "✗ No hero image URL returned\n";
        }
    }
} catch (\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";
echo "=== Tests Complete ===\n";
