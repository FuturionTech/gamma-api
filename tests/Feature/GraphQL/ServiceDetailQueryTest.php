<?php

namespace Tests\Feature\GraphQL;

use App\Models\Service;
use App\Models\ServiceApproachStep;
use App\Models\ServiceBusinessImpact;
use App\Models\ServiceCapabilityGroup;
use App\Models\ServiceCapabilityItem;
use App\Models\ServiceDeliveryItem;
use App\Models\ServiceDifferentiator;
use App\Models\ServiceFeature;
use App\Models\ServiceIndustryApplication;
use App\Models\ServiceIndustryUseCase;
use App\Models\ServicePainPoint;
use App\Models\ServiceStat;
use App\Models\ServiceTechnology;
use App\Models\ServiceUseCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceDetailQueryTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_full_tree_in_english(): void
    {
        $service = $this->seedFullService();

        // Default locale is 'en' when no Accept-Language header is sent.
        $response = $this->postJson('/graphql', [
            'query' => '{
                service(slug: "'.$service->slug.'") {
                    id
                    slug
                    title
                    description
                    hero {
                        tagline
                        headline
                        ctaPrimaryLabel
                        stats {
                            value
                            label
                        }
                    }
                    challenge {
                        title
                        description
                        painPoints { text }
                    }
                    howWeDeliver {
                        title
                        items { text }
                    }
                    capabilities {
                        title
                        groups {
                            name
                            items { name }
                        }
                    }
                    keyUseCases {
                        title
                        items { text }
                    }
                    ourApproach {
                        title
                        items { title description }
                    }
                    industryApplications {
                        title
                        industries {
                            name
                            useCases { text }
                        }
                    }
                    technologies {
                        title
                        items { name }
                    }
                    businessImpact {
                        title
                        items { title description }
                    }
                    differentiators {
                        title
                        points { title description }
                    }
                    closing {
                        title
                        subtitle
                    }
                    features { id title }
                    benefits { id title }
                }
            }',
        ]);

        $response->assertOk();
        $response->assertJsonPath('data.service.slug', $service->slug);
        $response->assertJsonPath('data.service.title', 'Test Service');
        $response->assertJsonPath('data.service.hero.headline', 'Hero Headline EN');
        $response->assertJsonPath('data.service.hero.stats.0.value', '100+');
        $response->assertJsonPath('data.service.hero.stats.0.label', 'Clients');
        $response->assertJsonPath('data.service.challenge.title', 'Challenge Title EN');
        $response->assertJsonPath('data.service.challenge.painPoints.0.text', 'Pain point 1 EN');
        $response->assertJsonPath('data.service.howWeDeliver.items.0.text', 'Deliver item 1 EN');
        $response->assertJsonPath('data.service.capabilities.groups.0.name', 'Group 1 EN');
        $response->assertJsonPath('data.service.capabilities.groups.0.items.0.name', 'Capability 1 EN');
        $response->assertJsonPath('data.service.keyUseCases.items.0.text', 'Use case 1 EN');
        $response->assertJsonPath('data.service.ourApproach.items.0.title', 'Approach step 1 EN');
        $response->assertJsonPath('data.service.industryApplications.industries.0.name', 'Industry 1 EN');
        $response->assertJsonPath('data.service.industryApplications.industries.0.useCases.0.text', 'Industry use case 1 EN');
        $response->assertJsonPath('data.service.technologies.items.0.name', 'Tech 1 EN');
        $response->assertJsonPath('data.service.businessImpact.items.0.title', 'Impact 1 EN');
        $response->assertJsonPath('data.service.differentiators.points.0.title', 'Differentiator 1 EN');
        $response->assertJsonPath('data.service.closing.title', 'Closing Title EN');
        $response->assertJsonPath('data.service.features.0.title', 'Feature 1 EN');
    }

    public function test_returns_full_tree_in_french(): void
    {
        $service = $this->seedFullService();

        // Locale is resolved from Accept-Language header via SetLocaleFromHeader middleware.
        $response = $this->postJson('/graphql', [
            'query' => '{
                service(slug: "'.$service->slug.'") {
                    title
                    hero { headline }
                    challenge { title painPoints { text } }
                }
            }',
        ], ['Accept-Language' => 'fr']);

        $response->assertOk();
        $response->assertJsonPath('data.service.title', 'Service Test');
        $response->assertJsonPath('data.service.hero.headline', 'Titre Hero FR');
        $response->assertJsonPath('data.service.challenge.title', 'Titre Défi FR');
        $response->assertJsonPath('data.service.challenge.painPoints.0.text', 'Douleur 1 FR');
    }

    public function test_falls_back_to_en_when_french_translation_missing(): void
    {
        $service = Service::create([
            'slug' => 'fallback-test',
            'icon' => 'bi-test',
            'category' => 'test',
            'order' => 1,
            'is_active' => true,
        ]);
        $service->translateOrNew('en')->fill([
            'title' => 'English Only',
            'hero_headline' => 'Only EN headline',
        ])->save();
        // No FR translation created

        // Locale is resolved from Accept-Language header via SetLocaleFromHeader middleware.
        $response = $this->postJson('/graphql', [
            'query' => '{
                service(slug: "fallback-test") {
                    title
                    hero { headline }
                }
            }',
        ], ['Accept-Language' => 'fr']);

        $response->assertOk();
        // use_property_fallback => true means FR read returns EN value
        $response->assertJsonPath('data.service.title', 'English Only');
        $response->assertJsonPath('data.service.hero.headline', 'Only EN headline');
    }

    public function test_returns_null_for_unknown_slug(): void
    {
        $response = $this->postJson('/graphql', [
            'query' => '{ service(slug: "does-not-exist") { id title } }',
        ]);

        $response->assertOk();
        $response->assertJsonPath('data.service', null);
    }

    public function test_returns_null_for_inactive_service(): void
    {
        Service::factory()->create([
            'slug' => 'inactive-service',
            'is_active' => false,
        ]);

        $response = $this->postJson('/graphql', [
            'query' => '{ service(slug: "inactive-service") { id title } }',
        ]);

        $response->assertOk();
        $response->assertJsonPath('data.service', null);
    }

    /**
     * Create a service with one row per child table, in both EN and FR.
     */
    private function seedFullService(): Service
    {
        $service = Service::create([
            'slug' => 'test-service-full-'.uniqid(),
            'icon' => 'bi-test',
            'icon_color' => 'primary',
            'category' => 'test',
            'order' => 1,
            'is_active' => true,
        ]);

        $service->translateOrNew('en')->fill([
            'title' => 'Test Service',
            'short_description' => 'Short EN',
            'description' => 'Long EN',
            'hero_tagline' => 'Hero Tagline EN',
            'hero_headline' => 'Hero Headline EN',
            'hero_subheadline' => 'Hero Subheadline EN',
            'hero_cta_primary_label' => 'CTA Primary EN',
            'hero_cta_secondary_label' => 'CTA Secondary EN',
            'challenge_title' => 'Challenge Title EN',
            'challenge_description' => 'Challenge Desc EN',
            'delivery_title' => 'Delivery Title EN',
            'delivery_description' => 'Delivery Desc EN',
            'capabilities_title' => 'Capabilities Title EN',
            'use_cases_title' => 'Use Cases Title EN',
            'use_cases_description' => 'Use Cases Desc EN',
            'approach_title' => 'Approach Title EN',
            'approach_description' => 'Approach Desc EN',
            'industry_title' => 'Industry Title EN',
            'industry_description' => 'Industry Desc EN',
            'technologies_title' => 'Technologies Title EN',
            'technologies_description' => 'Technologies Desc EN',
            'business_impact_title' => 'Business Impact Title EN',
            'business_impact_description' => 'Business Impact Desc EN',
            'differentiators_title' => 'Differentiators Title EN',
            'closing_title' => 'Closing Title EN',
            'closing_subtitle' => 'Closing Subtitle EN',
        ])->save();

        $service->translateOrNew('fr')->fill([
            'title' => 'Service Test',
            'hero_headline' => 'Titre Hero FR',
            'challenge_title' => 'Titre Défi FR',
        ])->save();

        $stat = ServiceStat::create(['service_id' => $service->id, 'icon' => 'bi-people', 'order' => 0]);
        $stat->translateOrNew('en')->fill(['value' => '100+', 'label' => 'Clients'])->save();
        $stat->translateOrNew('fr')->fill(['value' => '100+', 'label' => 'Clients FR'])->save();

        $painPoint = ServicePainPoint::create(['service_id' => $service->id, 'order' => 0]);
        $painPoint->translateOrNew('en')->fill(['text' => 'Pain point 1 EN'])->save();
        $painPoint->translateOrNew('fr')->fill(['text' => 'Douleur 1 FR'])->save();

        $delivery = ServiceDeliveryItem::create(['service_id' => $service->id, 'icon' => 'bi-check', 'order' => 0]);
        $delivery->translateOrNew('en')->fill(['text' => 'Deliver item 1 EN'])->save();
        $delivery->translateOrNew('fr')->fill(['text' => 'Livrer item 1 FR'])->save();

        $group = ServiceCapabilityGroup::create(['service_id' => $service->id, 'icon' => 'bi-gear', 'order' => 0]);
        $group->translateOrNew('en')->fill(['name' => 'Group 1 EN'])->save();
        $group->translateOrNew('fr')->fill(['name' => 'Groupe 1 FR'])->save();

        $capItem = ServiceCapabilityItem::create(['service_capability_group_id' => $group->id, 'order' => 0]);
        $capItem->translateOrNew('en')->fill(['name' => 'Capability 1 EN'])->save();
        $capItem->translateOrNew('fr')->fill(['name' => 'Capacité 1 FR'])->save();

        $useCase = ServiceUseCase::create(['service_id' => $service->id, 'order' => 0]);
        $useCase->translateOrNew('en')->fill(['text' => 'Use case 1 EN'])->save();
        $useCase->translateOrNew('fr')->fill(['text' => 'Cas 1 FR'])->save();

        $approach = ServiceApproachStep::create(['service_id' => $service->id, 'icon' => 'bi-1', 'order' => 0]);
        $approach->translateOrNew('en')->fill(['title' => 'Approach step 1 EN', 'description' => 'Desc EN'])->save();
        $approach->translateOrNew('fr')->fill(['title' => 'Étape 1 FR', 'description' => 'Desc FR'])->save();

        $industry = ServiceIndustryApplication::create(['service_id' => $service->id, 'icon' => 'bi-building', 'order' => 0]);
        $industry->translateOrNew('en')->fill(['name' => 'Industry 1 EN', 'description' => 'Industry desc EN'])->save();
        $industry->translateOrNew('fr')->fill(['name' => 'Industrie 1 FR', 'description' => 'Desc FR'])->save();

        $indUseCase = ServiceIndustryUseCase::create(['service_industry_application_id' => $industry->id, 'order' => 0]);
        $indUseCase->translateOrNew('en')->fill(['text' => 'Industry use case 1 EN'])->save();
        $indUseCase->translateOrNew('fr')->fill(['text' => 'Cas industrie 1 FR'])->save();

        $tech = ServiceTechnology::create(['service_id' => $service->id, 'icon' => 'bi-code', 'order' => 0]);
        $tech->translateOrNew('en')->fill(['name' => 'Tech 1 EN'])->save();
        $tech->translateOrNew('fr')->fill(['name' => 'Tech 1 FR'])->save();

        $impact = ServiceBusinessImpact::create(['service_id' => $service->id, 'icon' => 'bi-graph-up', 'order' => 0]);
        $impact->translateOrNew('en')->fill(['title' => 'Impact 1 EN', 'description' => 'Desc EN'])->save();
        $impact->translateOrNew('fr')->fill(['title' => 'Impact 1 FR', 'description' => 'Desc FR'])->save();

        $diff = ServiceDifferentiator::create(['service_id' => $service->id, 'icon' => 'bi-star', 'order' => 0]);
        $diff->translateOrNew('en')->fill(['title' => 'Differentiator 1 EN', 'description' => 'Desc EN'])->save();
        $diff->translateOrNew('fr')->fill(['title' => 'Différentiel 1 FR', 'description' => 'Desc FR'])->save();

        $feature = ServiceFeature::create(['service_id' => $service->id, 'icon' => 'bi-check', 'order' => 0]);
        $feature->translateOrNew('en')->fill(['title' => 'Feature 1 EN', 'description' => 'Desc EN'])->save();
        $feature->translateOrNew('fr')->fill(['title' => 'Fonctionnalité 1 FR', 'description' => 'Desc FR'])->save();

        return $service->fresh();
    }
}
