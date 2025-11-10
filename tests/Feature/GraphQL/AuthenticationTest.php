<?php

namespace Tests\Feature\GraphQL;

use App\Models\Administrator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create an administrator for testing
        Administrator::create([
            'name' => 'Test Admin',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);
    }

    public function test_can_login_with_valid_credentials(): void
    {
        $query = '
            mutation {
                login(input: {
                    email: "test@example.com"
                    password: "password123"
                }) {
                    token
                    administrator {
                        id
                        name
                        email
                    }
                }
            }
        ';

        $response = $this->graphQL($query);

        $response->assertJson([
            'data' => [
                'login' => [
                    'administrator' => [
                        'name' => 'Test Admin',
                        'email' => 'test@example.com',
                    ],
                ],
            ],
        ]);

        $this->assertArrayHasKey('token', $response->json('data.login'));
    }

    public function test_cannot_login_with_invalid_credentials(): void
    {
        $query = '
            mutation {
                login(input: {
                    email: "test@example.com"
                    password: "wrong_password"
                }) {
                    token
                }
            }
        ';

        $response = $this->graphQL($query);

        $response->assertGraphQLErrorMessage('Invalid credentials');
    }

    public function test_can_logout_with_valid_token(): void
    {
        $admin = Administrator::where('email', 'test@example.com')->first();
        $token = $admin->createToken('test-token')->plainTextToken;

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

