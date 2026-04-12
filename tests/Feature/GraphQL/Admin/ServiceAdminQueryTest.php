<?php

namespace Tests\Feature\GraphQL\Admin;

use App\Models\Administrator;
use App\Models\Service;
use App\Models\ServiceStat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceAdminQueryTest extends TestCase
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

    public function test_authenticated_admin_can_query_service_with_all_translations(): void
    {
        $token = $this->createAdminToken();

        $service = Service::factory()->create([
            'slug' => 'admin-test',
            'icon' => 'bi-test',
            'category' => 'test',
            'order' => 1,
            'is_active' => true,
        ]);

        // Add FR translation (EN is auto-created by the factory)
        $service->translateOrNew('fr')->fill([
            'title' => 'Titre FR',
            'hero_headline' => 'Titre principal FR',
        ])->save();

        // Add a child stat with two locales
        $stat = ServiceStat::create([
            'service_id' => $service->id,
            'icon' => 'bi-star',
            'order' => 0,
        ]);
        $stat->translateOrNew('en')->fill(['value' => '100', 'label' => 'Clients'])->save();
        $stat->translateOrNew('fr')->fill(['value' => '100', 'label' => 'Clients FR'])->save();

        $response = $this->graphQL(
            '
            query ($id: ID!) {
                serviceForAdmin(id: $id) {
                    id
                    slug
                    isActive
                    translations {
                        locale
                        title
                        heroHeadline
                    }
                    stats {
                        id
                        translations {
                            locale
                            value
                            label
                        }
                    }
                }
            }
            ',
            ['id' => $service->id],
            ['Authorization' => "Bearer {$token}"],
        );

        $response->assertOk();
        $data = $response->json('data.serviceForAdmin');
        $this->assertNotNull($data);
        $this->assertSame('admin-test', $data['slug']);

        // Should have BOTH translations (EN + FR)
        $this->assertCount(2, $data['translations']);
        $locales = collect($data['translations'])->pluck('locale')->sort()->values()->all();
        $this->assertSame(['en', 'fr'], $locales);

        // Stats should have translations too
        $this->assertCount(1, $data['stats']);
        $this->assertCount(2, $data['stats'][0]['translations']);
    }

    public function test_unauthenticated_request_is_rejected(): void
    {
        $response = $this->graphQL(
            '{ serviceForAdmin(id: 1) { id } }',
        );

        // GraphQL always returns 200 but includes errors for guarded queries
        $response->assertOk();
        $this->assertNotEmpty($response->json('errors'));
    }

    public function test_admin_list_query_returns_all_services(): void
    {
        $token = $this->createAdminToken();

        Service::factory()->count(3)->create(['is_active' => true]);
        Service::factory()->inactive()->create();

        $response = $this->graphQL(
            '{ servicesForAdmin { id slug isActive translations { locale title } } }',
            [],
            ['Authorization' => "Bearer {$token}"],
        );

        $response->assertOk();
        $this->assertNull($response->json('errors'));
        // Admin list returns ALL services (active + inactive)
        $this->assertCount(4, $response->json('data.servicesForAdmin'));
    }

    public function test_admin_list_query_can_filter_by_active(): void
    {
        $token = $this->createAdminToken();

        Service::factory()->count(2)->create(['is_active' => true]);
        Service::factory()->inactive()->create();

        $response = $this->graphQL(
            'query ($isActive: Boolean) { servicesForAdmin(isActive: $isActive) { id isActive } }',
            ['isActive' => false],
            ['Authorization' => "Bearer {$token}"],
        );

        $response->assertOk();
        $this->assertNull($response->json('errors'));
        $this->assertCount(1, $response->json('data.servicesForAdmin'));
        $this->assertFalse($response->json('data.servicesForAdmin.0.isActive'));
    }

    public function test_service_for_admin_returns_null_for_nonexistent_id(): void
    {
        $token = $this->createAdminToken();

        $response = $this->graphQL(
            'query ($id: ID!) { serviceForAdmin(id: $id) { id } }',
            ['id' => 99999],
            ['Authorization' => "Bearer {$token}"],
        );

        $response->assertOk();
        $this->assertNull($response->json('data.serviceForAdmin'));
    }
}
