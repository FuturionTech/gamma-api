<?php

namespace Tests\Feature\GraphQL;

use App\Models\Administrator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected Administrator $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an administrator for testing
        $this->admin = Administrator::create([
            'employee_number' => 'EMP-TEST-001',
            'email' => 'test@example.com',
            'first_name' => 'Test',
            'last_name' => 'Admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
    }

    public function test_can_query_me_with_valid_token(): void
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $query = '
            query {
                me {
                    id
                    email
                    first_name
                    last_name
                }
            }
        ';

        $response = $this->graphQL($query, [], ['Authorization' => "Bearer {$token}"]);

        $response->assertJson([
            'data' => [
                'me' => [
                    'email' => 'test@example.com',
                    'first_name' => 'Test',
                    'last_name' => 'Admin',
                ],
            ],
        ]);
    }

    public function test_cannot_query_me_without_token(): void
    {
        $query = '
            query {
                me {
                    id
                    email
                }
            }
        ';

        $response = $this->graphQL($query);

        $this->assertNull($response->json('data.me'));
    }

    public function test_can_logout_with_valid_token(): void
    {
        $token = $this->admin->createToken('test-token')->plainTextToken;

        $query = '
            mutation {
                logout {
                    success
                    message
                }
            }
        ';

        $response = $this->graphQL($query, [], ['Authorization' => "Bearer {$token}"]);

        $response->assertJson([
            'data' => [
                'logout' => [
                    'success' => true,
                ],
            ],
        ]);
    }

    /**
     * Helper method to make GraphQL requests
     */
    protected function graphQL(string $query, array $variables = [], array $headers = [])
    {
        return $this->postJson('/graphql', [
            'query' => $query,
            'variables' => $variables,
        ], $headers);
    }
}
