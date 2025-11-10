<?php

// Comprehensive Image Upload Test Script
// Tests upload functionality for: Partner, Client, Team, Banner, BlogPost, Project

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Image Uploads for All Entities ===\n\n";

// Get authentication token
echo "Step 1: Getting authentication token...\n";
$admin = \App\Models\Administrator::first();
if (!$admin) {
    echo "Error: No administrator found. Please run seeders first.\n";
    exit(1);
}

$token = $admin->createToken('upload-test-token')->plainTextToken;
echo "Token created successfully\n\n";

// Helper function to create a test image file
function createTestImageFile($filename = 'test-image.jpg')
{
    $imagePath = sys_get_temp_dir() . '/' . $filename;

    // Create a simple 100x100 red image
    $image = imagecreatetruecolor(100, 100);
    $red = imagecolorallocate($image, 255, 0, 0);
    imagefill($image, 0, 0, $red);
    imagejpeg($image, $imagePath);
    imagedestroy($image);

    return $imagePath;
}

// Helper function to test entity upload
function testEntityUpload($entityName, $createMutation, $updateMutation, $queryField, $imagePath, $token)
{
    echo "=== Testing {$entityName} Upload ===\n";

    // Test CREATE with upload
    echo "1. Testing CREATE {$entityName} with image upload...\n";
    try {
        $response = \Illuminate\Support\Facades\Http::withToken($token)
            ->attach('operations', json_encode([
                'query' => $createMutation['query'],
                'variables' => $createMutation['variables']
            ]), 'operations.json')
            ->attach('map', json_encode(['0' => ['variables.input.logo']]), 'map.json')
            ->attach('0', file_get_contents($imagePath), basename($imagePath))
            ->post('http://localhost/graphql');

        $result = $response->json();

        if (isset($result['errors'])) {
            echo "✗ CREATE failed: " . ($result['errors'][0]['message'] ?? 'Unknown error') . "\n";
            return null;
        }

        $createdId = $result['data'][$createMutation['field']]['id'] ?? null;
        if ($createdId) {
            echo "✓ {$entityName} created successfully (ID: {$createdId})\n";
            $imageUrl = $result['data'][$createMutation['field']][$createMutation['imageField']] ?? null;
            if ($imageUrl) {
                echo "✓ Image uploaded: {$imageUrl}\n";
            }
        }

        return $createdId;
    } catch (\Exception $e) {
        echo "✗ CREATE exception: " . $e->getMessage() . "\n";
        return null;
    }
}

$imagePath = createTestImageFile();

// Test 1: Partner Upload
echo "\n" . str_repeat("=", 60) . "\n";
$partnerMutation = [
    'query' => 'mutation($input: CreatePartnerInput!) { createPartner(input: $input) { id name logo_url } }',
    'variables' => ['input' => ['name' => 'Test Partner']],
    'field' => 'createPartner',
    'imageField' => 'logo_url'
];
testEntityUpload('Partner', $partnerMutation, null, 'partners', $imagePath, $token);

// Test 2: Client Upload
echo "\n" . str_repeat("=", 60) . "\n";
$clientMutation = [
    'query' => 'mutation($input: CreateClientInput!) { createClient(input: $input) { id name logo_url } }',
    'variables' => ['input' => ['name' => 'Test Client']],
    'field' => 'createClient',
    'imageField' => 'logo_url'
];
testEntityUpload('Client', $clientMutation, null, 'clients', $imagePath, $token);

// Test 3: Team Upload
echo "\n" . str_repeat("=", 60) . "\n";
$teamMutation = [
    'query' => 'mutation($input: CreateTeamInput!) { createTeam(input: $input) { id name profile_picture_url } }',
    'variables' => ['input' => ['name' => 'Test Team Member', 'role' => 'Developer']],
    'field' => 'createTeam',
    'imageField' => 'profile_picture_url'
];
testEntityUpload('Team', $teamMutation, null, 'teams', $imagePath, $token);

// Test 4: Banner Upload
echo "\n" . str_repeat("=", 60) . "\n";
$bannerMutation = [
    'query' => 'mutation($input: CreateBannerInput!) { createBanner(input: $input) { id title image_url } }',
    'variables' => ['input' => ['title' => 'Test Banner']],
    'field' => 'createBanner',
    'imageField' => 'image_url'
];
testEntityUpload('Banner', $bannerMutation, null, 'banners', $imagePath, $token);

// Test 5: BlogPost Upload
echo "\n" . str_repeat("=", 60) . "\n";
$blogPostMutation = [
    'query' => 'mutation($input: CreateBlogPostInput!) { createBlogPost(input: $input) { id title featured_image_url } }',
    'variables' => ['input' => ['title' => 'Test Blog Post', 'content' => 'Test content']],
    'field' => 'createBlogPost',
    'imageField' => 'featured_image_url'
];
testEntityUpload('BlogPost', $blogPostMutation, null, 'blogPosts', $imagePath, $token);

// Test 6: Project Upload
echo "\n" . str_repeat("=", 60) . "\n";
$projectMutation = [
    'query' => 'mutation($input: CreateProjectInput!) { createProject(input: $input) { id title featured_image_url } }',
    'variables' => ['input' => ['title' => 'Test Project']],
    'field' => 'createProject',
    'imageField' => 'featured_image_url'
];
testEntityUpload('Project', $projectMutation, null, 'projects', $imagePath, $token);

// Cleanup
unlink($imagePath);

echo "\n" . str_repeat("=", 60) . "\n";
echo "\n=== Image Upload Tests Complete ===\n";
echo "\nNote: This script tests multipart file uploads.\n";
echo "For base64 uploads, the same mutations accept base64 strings.\n";
echo "Example: { logo: \"data:image/jpeg;base64,...\" }\n";
