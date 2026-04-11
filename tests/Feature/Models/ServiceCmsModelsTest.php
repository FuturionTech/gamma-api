<?php

namespace Tests\Feature\Models;

use App\Models\Service;
use App\Models\ServiceApproachStep;
use App\Models\ServiceBusinessImpact;
use App\Models\ServiceCapabilityGroup;
use App\Models\ServiceCapabilityItem;
use App\Models\ServiceDeliveryItem;
use App\Models\ServiceDifferentiator;
use App\Models\ServiceIndustryApplication;
use App\Models\ServiceIndustryUseCase;
use App\Models\ServicePainPoint;
use App\Models\ServiceStat;
use App\Models\ServiceTechnology;
use App\Models\ServiceUseCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceCmsModelsTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_stat_can_be_created_with_translations(): void
    {
        $service = Service::factory()->create();

        $stat = ServiceStat::factory()->create([
            'service_id' => $service->id,
            'icon' => 'bi-people',
        ]);

        $stat->translateOrNew('en')->fill(['value' => '500+', 'label' => 'Clients'])->save();
        $stat->translateOrNew('fr')->fill(['value' => '500+', 'label' => 'Clients'])->save();

        $this->assertDatabaseHas('service_stat_translations', [
            'service_stat_id' => $stat->id,
            'locale' => 'en',
            'value' => '500+',
            'label' => 'Clients',
        ]);

        app()->setLocale('en');
        $this->assertSame('500+', $stat->fresh()->value);
        $this->assertSame('Clients', $stat->fresh()->label);

        app()->setLocale('fr');
        $this->assertSame('500+', $stat->fresh()->value);
    }

    public function test_service_pain_point_translations_work(): void
    {
        $service = Service::factory()->create();
        $point = ServicePainPoint::factory()->create(['service_id' => $service->id]);

        $point->translateOrNew('en')->fill(['text' => 'Slow onboarding'])->save();
        $point->translateOrNew('fr')->fill(['text' => 'Intégration lente'])->save();

        app()->setLocale('en');
        $this->assertSame('Slow onboarding', $point->fresh()->text);

        app()->setLocale('fr');
        $this->assertSame('Intégration lente', $point->fresh()->text);
    }

    public function test_service_capability_item_belongs_to_group(): void
    {
        $group = ServiceCapabilityGroup::factory()->create();
        $item = ServiceCapabilityItem::factory()->create([
            'service_capability_group_id' => $group->id,
        ]);

        $item->translateOrNew('en')->fill(['name' => 'API Design'])->save();

        $this->assertSame($group->id, $item->group->id);
        $this->assertDatabaseHas('service_capability_item_translations', [
            'service_capability_item_id' => $item->id,
            'locale' => 'en',
            'name' => 'API Design',
        ]);
    }

    public function test_industry_use_case_belongs_to_industry_application(): void
    {
        $industry = ServiceIndustryApplication::factory()->create();
        $useCase = ServiceIndustryUseCase::factory()->create([
            'service_industry_application_id' => $industry->id,
        ]);

        $useCase->translateOrNew('en')->fill(['text' => 'Fraud detection'])->save();

        $this->assertSame($industry->id, $useCase->industryApplication->id);
    }

    public function test_service_has_all_new_relations(): void
    {
        $service = Service::factory()->create();

        ServiceStat::factory()->create(['service_id' => $service->id]);
        ServicePainPoint::factory()->create(['service_id' => $service->id]);
        ServiceDeliveryItem::factory()->create(['service_id' => $service->id]);
        ServiceCapabilityGroup::factory()->create(['service_id' => $service->id]);
        ServiceUseCase::factory()->create(['service_id' => $service->id]);
        ServiceApproachStep::factory()->create(['service_id' => $service->id]);
        ServiceIndustryApplication::factory()->create(['service_id' => $service->id]);
        ServiceTechnology::factory()->create(['service_id' => $service->id]);
        ServiceBusinessImpact::factory()->create(['service_id' => $service->id]);
        ServiceDifferentiator::factory()->create(['service_id' => $service->id]);

        $fresh = $service->fresh();

        $this->assertCount(1, $fresh->stats);
        $this->assertCount(1, $fresh->painPoints);
        $this->assertCount(1, $fresh->deliveryItems);
        $this->assertCount(1, $fresh->capabilityGroups);
        $this->assertCount(1, $fresh->useCases);
        $this->assertCount(1, $fresh->approachSteps);
        $this->assertCount(1, $fresh->industryApplications);
        $this->assertCount(1, $fresh->technologies);
        $this->assertCount(1, $fresh->businessImpacts);
        $this->assertCount(1, $fresh->differentiators);
    }
}
