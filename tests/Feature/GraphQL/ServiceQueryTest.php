<?php

namespace Tests\Feature\GraphQL;

use App\Models\Application;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceQueryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create application
        Application::create([
            'name' => 'Test Application',
        ]);
    }

    public function test_can_query_services(): void
    {
        // Create test services
        Service::factory()->count(3)->create(['application_id' => 1]);

        $query = '
            query {
                services(application_id: 1, is_active: true) {
                    id
                    title
                    description
                    icon
                    category
                    slug
                }
            }
        ';

        $response = $this->graphQL($query);

        $response->assertJsonCount(3, 'data.services');
        $response->assertJsonStructure([
            'data' => [
                'services' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'icon',
                        'category',
                        'slug',
                    ],
                ],
            ],
        ]);
    }

    public function test_can_filter_services_by_active_status(): void
    {
        // Create active and inactive services
        Service::factory()->count(2)->create(['application_id' => 1, 'is_active' => true]);
        Service::factory()->count(1)->inactive()->create(['application_id' => 1]);

        $query = '
            query {
                services(application_id: 1, is_active: true) {
                    id
                    is_active
                }
            }
        ';

        $response = $this->graphQL($query);

        $response->assertJsonCount(2, 'data.services');
        
        foreach ($response->json('data.services') as $service) {
            $this->assertTrue($service['is_active']);
        }
    }

    public function test_can_query_single_service_by_id(): void
    {
        $service = Service::factory()->create(['application_id' => 1, 'title' => 'Test Service']);

        $query = '
            query($id: ID!) {
                service(id: $id) {
                    id
                    title
                    description
                }
            }
        ';

        $response = $this->graphQL($query, ['id' => $service->id]);

        $response->assertJson([
            'data' => [
                'service' => [
                    'id' => (string) $service->id,
                    'title' => 'Test Service',
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

