<?php

namespace Tests\Feature\GraphQL;

use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServicesListQueryTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_active_services_ordered(): void
    {
        Service::factory()->create(['order' => 2, 'is_active' => true, 'slug' => 'service-b']);
        Service::factory()->create(['order' => 1, 'is_active' => true, 'slug' => 'service-a']);

        $response = $this->postJson('/graphql', [
            'query' => '{ services { id slug order } }',
        ]);

        $response->assertOk();
        $response->assertJsonCount(2, 'data.services');
        $response->assertJsonPath('data.services.0.slug', 'service-a');
        $response->assertJsonPath('data.services.1.slug', 'service-b');
    }

    public function test_filters_out_inactive_services_by_default(): void
    {
        Service::factory()->create(['is_active' => true, 'slug' => 'active-service']);
        Service::factory()->create(['is_active' => false, 'slug' => 'inactive-service']);

        $response = $this->postJson('/graphql', [
            'query' => '{ services { id slug is_active } }',
        ]);

        $response->assertOk();
        $response->assertJsonCount(1, 'data.services');
        $response->assertJsonPath('data.services.0.slug', 'active-service');
    }

    public function test_can_explicitly_query_inactive_services(): void
    {
        Service::factory()->create(['is_active' => true, 'slug' => 'active-service']);
        Service::factory()->create(['is_active' => false, 'slug' => 'inactive-service']);

        $response = $this->postJson('/graphql', [
            'query' => '{ services(is_active: false) { id slug is_active } }',
        ]);

        $response->assertOk();
        $response->assertJsonCount(1, 'data.services');
        $response->assertJsonPath('data.services.0.slug', 'inactive-service');
    }

    public function test_can_limit_results(): void
    {
        Service::factory()->count(5)->create(['is_active' => true]);

        $response = $this->postJson('/graphql', [
            'query' => '{ services(limit: 3) { id } }',
        ]);

        $response->assertOk();
        $response->assertJsonCount(3, 'data.services');
    }

    public function test_returns_empty_array_when_no_services(): void
    {
        $response = $this->postJson('/graphql', [
            'query' => '{ services { id } }',
        ]);

        $response->assertOk();
        $response->assertJsonPath('data.services', []);
    }
}
