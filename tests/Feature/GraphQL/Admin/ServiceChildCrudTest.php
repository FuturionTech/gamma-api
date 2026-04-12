<?php

namespace Tests\Feature\GraphQL\Admin;

use App\Models\Administrator;
use App\Models\Service;
use App\Models\ServiceStat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceChildCrudTest extends TestCase
{
    use RefreshDatabase;

    private function createAdminToken(): string
    {
        $admin = Administrator::factory()->create();

        return $admin->createToken('test-token')->plainTextToken;
    }

    private function graphQL(string $query, array $variables = [], array $headers = []): \Illuminate\Testing\TestResponse
    {
        return $this->postJson('/graphql', [
            'query' => $query,
            'variables' => $variables,
        ], $headers);
    }

    // -------------------------------------------------------------------------
    // Create
    // -------------------------------------------------------------------------

    public function test_can_create_service_stat(): void
    {
        $token = $this->createAdminToken();
        $service = Service::factory()->create();

        $response = $this->graphQL(
            '
            mutation ($serviceId: ID!, $input: ServiceStatInput!) {
                createServiceStat(serviceId: $serviceId, input: $input) {
                    id
                    order
                    icon
                    translations { locale value label }
                }
            }
            ',
            [
                'serviceId' => $service->id,
                'input' => [
                    'icon' => 'bi-star',
                    'order' => 0,
                    'translations' => [
                        ['locale' => 'en', 'value' => '100+', 'label' => 'Clients'],
                        ['locale' => 'fr', 'value' => '100+', 'label' => 'Clients FR'],
                    ],
                ],
            ],
            ['Authorization' => "Bearer {$token}"],
        );

        $response->assertOk();
        $this->assertNull($response->json('errors'));
        $this->assertSame(0, $response->json('data.createServiceStat.order'));
        $this->assertSame('bi-star', $response->json('data.createServiceStat.icon'));
        $this->assertCount(2, $response->json('data.createServiceStat.translations'));
    }

    // -------------------------------------------------------------------------
    // Update
    // -------------------------------------------------------------------------

    public function test_can_update_service_stat(): void
    {
        $token = $this->createAdminToken();
        $service = Service::factory()->create();

        $stat = ServiceStat::create([
            'service_id' => $service->id,
            'icon' => 'bi-old',
            'order' => 0,
        ]);
        $stat->translateOrNew('en')->fill(['value' => 'old', 'label' => 'old'])->save();

        $response = $this->graphQL(
            '
            mutation ($id: ID!, $input: ServiceStatInput!) {
                updateServiceStat(id: $id, input: $input) {
                    id
                    icon
                    translations { locale value label }
                }
            }
            ',
            [
                'id' => $stat->id,
                'input' => [
                    'icon' => 'bi-new',
                    'translations' => [
                        ['locale' => 'en', 'value' => '200+', 'label' => 'Updated'],
                    ],
                ],
            ],
            ['Authorization' => "Bearer {$token}"],
        );

        $response->assertOk();
        $this->assertNull($response->json('errors'));
        $this->assertSame('bi-new', $response->json('data.updateServiceStat.icon'));
        $this->assertSame('200+', $response->json('data.updateServiceStat.translations.0.value'));
    }

    // -------------------------------------------------------------------------
    // Delete
    // -------------------------------------------------------------------------

    public function test_can_delete_service_stat(): void
    {
        $token = $this->createAdminToken();
        $service = Service::factory()->create();

        $stat = ServiceStat::create([
            'service_id' => $service->id,
            'icon' => 'bi-star',
            'order' => 0,
        ]);
        $stat->translateOrNew('en')->fill(['value' => 'x', 'label' => 'y'])->save();

        $response = $this->graphQL(
            '
            mutation ($id: ID!) {
                deleteServiceStat(id: $id)
            }
            ',
            ['id' => $stat->id],
            ['Authorization' => "Bearer {$token}"],
        );

        $response->assertOk();
        $this->assertNull($response->json('errors'));
        $this->assertTrue($response->json('data.deleteServiceStat'));
        $this->assertDatabaseMissing('service_stats', ['id' => $stat->id]);
    }

    // -------------------------------------------------------------------------
    // Reorder
    // -------------------------------------------------------------------------

    public function test_can_reorder_service_stats(): void
    {
        $token = $this->createAdminToken();
        $service = Service::factory()->create();

        $stat1 = ServiceStat::create([
            'service_id' => $service->id,
            'order' => 0,
            'icon' => 'bi-1',
        ]);
        $stat1->translateOrNew('en')->fill(['value' => 'A', 'label' => 'First'])->save();

        $stat2 = ServiceStat::create([
            'service_id' => $service->id,
            'order' => 1,
            'icon' => 'bi-2',
        ]);
        $stat2->translateOrNew('en')->fill(['value' => 'B', 'label' => 'Second'])->save();

        // Reorder: put stat2 first
        $response = $this->graphQL(
            '
            mutation ($serviceId: ID!, $orderedIds: [ID!]!) {
                reorderServiceStats(serviceId: $serviceId, orderedIds: $orderedIds) {
                    id
                    order
                }
            }
            ',
            [
                'serviceId' => $service->id,
                'orderedIds' => [(string) $stat2->id, (string) $stat1->id],
            ],
            ['Authorization' => "Bearer {$token}"],
        );

        $response->assertOk();
        $this->assertNull($response->json('errors'));

        $items = $response->json('data.reorderServiceStats');
        $this->assertCount(2, $items);
        // stat2 should now be order 0, stat1 order 1
        $this->assertSame((string) $stat2->id, (string) $items[0]['id']);
        $this->assertSame(0, $items[0]['order']);
        $this->assertSame((string) $stat1->id, (string) $items[1]['id']);
        $this->assertSame(1, $items[1]['order']);
    }

    // -------------------------------------------------------------------------
    // Auth guard
    // -------------------------------------------------------------------------

    public function test_unauthenticated_child_mutation_fails(): void
    {
        $service = Service::factory()->create();

        $response = $this->graphQL(
            '
            mutation ($serviceId: ID!, $input: ServiceStatInput!) {
                createServiceStat(serviceId: $serviceId, input: $input) { id }
            }
            ',
            [
                'serviceId' => $service->id,
                'input' => [
                    'translations' => [['locale' => 'en', 'value' => 'x', 'label' => 'y']],
                ],
            ],
        );

        $response->assertOk();
        $this->assertNotEmpty($response->json('errors'));
    }
}
