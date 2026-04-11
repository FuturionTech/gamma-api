<?php

namespace Tests\Feature\GraphQL;

use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleHeaderTest extends TestCase
{
    use RefreshDatabase;

    public function test_accept_language_header_switches_locale(): void
    {
        $service = Service::create([
            'slug' => 'locale-test',
            'icon' => 'bi-test',
            'category' => 'test',
            'order' => 1,
            'is_active' => true,
        ]);
        $service->translateOrNew('en')->fill(['title' => 'English Title'])->save();
        $service->translateOrNew('fr')->fill(['title' => 'Titre Français'])->save();

        $enResponse = $this->postJson('/graphql', [
            'query' => '{ service(slug: "locale-test") { title } }',
        ], ['Accept-Language' => 'en']);

        $enResponse->assertOk();
        $enResponse->assertJsonPath('data.service.title', 'English Title');

        $frResponse = $this->postJson('/graphql', [
            'query' => '{ service(slug: "locale-test") { title } }',
        ], ['Accept-Language' => 'fr']);

        $frResponse->assertOk();
        $frResponse->assertJsonPath('data.service.title', 'Titre Français');
    }

    public function test_x_locale_header_overrides_accept_language(): void
    {
        $service = Service::create([
            'slug' => 'x-locale-test',
            'icon' => 'bi-test',
            'category' => 'test',
            'order' => 1,
            'is_active' => true,
        ]);
        $service->translateOrNew('en')->fill(['title' => 'EN'])->save();
        $service->translateOrNew('fr')->fill(['title' => 'FR'])->save();

        $response = $this->postJson('/graphql', [
            'query' => '{ service(slug: "x-locale-test") { title } }',
        ], ['Accept-Language' => 'en', 'X-Locale' => 'fr']);

        $response->assertOk();
        $response->assertJsonPath('data.service.title', 'FR');
    }

    public function test_falls_back_to_default_locale_when_no_headers(): void
    {
        $service = Service::create([
            'slug' => 'no-header-test',
            'icon' => 'bi-test',
            'category' => 'test',
            'order' => 1,
            'is_active' => true,
        ]);
        $service->translateOrNew('en')->fill(['title' => 'Default EN'])->save();
        $service->translateOrNew('fr')->fill(['title' => 'Default FR'])->save();

        $response = $this->postJson('/graphql', [
            'query' => '{ service(slug: "no-header-test") { title } }',
        ]);

        $response->assertOk();
        // Default locale is EN per config/translatable.php fallback_locale
        $response->assertJsonPath('data.service.title', 'Default EN');
    }

    public function test_unsupported_locale_falls_back_to_default(): void
    {
        $service = Service::create([
            'slug' => 'unsupported-test',
            'icon' => 'bi-test',
            'category' => 'test',
            'order' => 1,
            'is_active' => true,
        ]);
        $service->translateOrNew('en')->fill(['title' => 'Fallback EN'])->save();

        $response = $this->postJson('/graphql', [
            'query' => '{ service(slug: "unsupported-test") { title } }',
        ], ['Accept-Language' => 'de']); // German not in supported locales

        $response->assertOk();
        $response->assertJsonPath('data.service.title', 'Fallback EN');
    }
}
