<?php

namespace Tests\Feature\GraphQL\Admin;

use App\Models\Administrator;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceAdminMutationTest extends TestCase
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

    public function test_can_create_service_with_translations(): void
    {
        $token = $this->createAdminToken();

        $response = $this->graphQL(
            '
            mutation ($input: CreateServiceAdminInput!) {
                createServiceAdmin(input: $input) {
                    id
                    slug
                    translations { locale title heroHeadline }
                }
            }
            ',
            [
                'input' => [
                    'slug' => 'new-service',
                    'icon' => 'bi-star',
                    'category' => 'test',
                    'order' => 1,
                    'isActive' => true,
                    'translations' => [
                        ['locale' => 'en', 'title' => 'New Service', 'heroHeadline' => 'Welcome'],
                        ['locale' => 'fr', 'title' => 'Nouveau Service', 'heroHeadline' => 'Bienvenue'],
                    ],
                ],
            ],
            ['Authorization' => "Bearer {$token}"],
        );

        $response->assertOk();
        $this->assertNull($response->json('errors'));
        $this->assertSame('new-service', $response->json('data.createServiceAdmin.slug'));
        $this->assertCount(2, $response->json('data.createServiceAdmin.translations'));

        $this->assertDatabaseHas('services', ['slug' => 'new-service']);
        $this->assertDatabaseHas('service_translations', [
            'locale' => 'en',
            'title' => 'New Service',
        ]);
        $this->assertDatabaseHas('service_translations', [
            'locale' => 'fr',
            'title' => 'Nouveau Service',
        ]);
    }

    public function test_can_update_service(): void
    {
        $token = $this->createAdminToken();
        $service = Service::factory()->create(['slug' => 'update-me']);

        $response = $this->graphQL(
            '
            mutation ($id: ID!, $input: UpdateServiceAdminInput!) {
                updateServiceAdmin(id: $id, input: $input) {
                    id
                    icon
                    translations { locale title }
                }
            }
            ',
            [
                'id' => $service->id,
                'input' => [
                    'icon' => 'bi-updated',
                    'translations' => [
                        ['locale' => 'en', 'title' => 'Updated Title'],
                    ],
                ],
            ],
            ['Authorization' => "Bearer {$token}"],
        );

        $response->assertOk();
        $this->assertNull($response->json('errors'));
        $this->assertSame('bi-updated', $response->json('data.updateServiceAdmin.icon'));
    }

    public function test_can_delete_service(): void
    {
        $token = $this->createAdminToken();
        $service = Service::factory()->create();

        $response = $this->graphQL(
            '
            mutation ($id: ID!) {
                deleteServiceAdmin(id: $id)
            }
            ',
            ['id' => $service->id],
            ['Authorization' => "Bearer {$token}"],
        );

        $response->assertOk();
        $this->assertNull($response->json('errors'));
        $this->assertTrue($response->json('data.deleteServiceAdmin'));
        $this->assertDatabaseMissing('services', ['id' => $service->id]);
    }

    public function test_can_publish_service(): void
    {
        $token = $this->createAdminToken();
        $service = Service::factory()->create(['published_at' => null]);

        $response = $this->graphQL(
            '
            mutation ($id: ID!) {
                publishService(id: $id) {
                    id
                    publishedAt
                }
            }
            ',
            ['id' => $service->id],
            ['Authorization' => "Bearer {$token}"],
        );

        $response->assertOk();
        $this->assertNull($response->json('errors'));
        $this->assertNotNull($response->json('data.publishService.publishedAt'));
    }

    public function test_can_unpublish_service(): void
    {
        $token = $this->createAdminToken();
        $service = Service::factory()->create(['published_at' => now()]);

        $response = $this->graphQL(
            '
            mutation ($id: ID!) {
                unpublishService(id: $id) {
                    id
                    publishedAt
                }
            }
            ',
            ['id' => $service->id],
            ['Authorization' => "Bearer {$token}"],
        );

        $response->assertOk();
        $this->assertNull($response->json('errors'));
        $this->assertNull($response->json('data.unpublishService.publishedAt'));
    }

    public function test_unauthenticated_create_is_rejected(): void
    {
        $response = $this->graphQL(
            '
            mutation ($input: CreateServiceAdminInput!) {
                createServiceAdmin(input: $input) { id }
            }
            ',
            [
                'input' => [
                    'slug' => 'nope',
                    'translations' => [['locale' => 'en', 'title' => 'No']],
                ],
            ],
        );

        $response->assertOk();
        $this->assertNotEmpty($response->json('errors'));
    }
}
