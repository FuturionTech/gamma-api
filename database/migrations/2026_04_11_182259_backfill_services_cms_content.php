<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // When RefreshDatabase boots the schema during the test suite, skip the
        // fixture backfill so factory-based tests keep their clean slate. The
        // dedicated tests in ServiceCmsDataMigrationTest opt-in explicitly by
        // setting the FORCE_SERVICES_CMS_BACKFILL env flag before invoking up().
        if (app()->runningUnitTests() && ! env('FORCE_SERVICES_CMS_BACKFILL')) {
            return;
        }

        $fixturePath = database_path('data/services-content-backfill.json');
        if (! file_exists($fixturePath)) {
            throw new \RuntimeException("Fixture not found at {$fixturePath}");
        }

        $fixture = json_decode(file_get_contents($fixturePath), true);
        if (! is_array($fixture) || ! isset($fixture['services']) || ! is_array($fixture['services'])) {
            throw new \RuntimeException('Invalid fixture structure');
        }

        DB::transaction(function () use ($fixture) {
            foreach ($fixture['services'] as $service) {
                $this->backfillService($service);
            }
        });
    }

    public function down(): void
    {
        // Intentionally empty.
    }

    private function backfillService(array $serviceData): void
    {
        $now = now();

        $baseRow = [
            'slug' => $serviceData['slug'],
            'icon' => $serviceData['icon'] ?? null,
            'icon_color' => $serviceData['iconColor'] ?? null,
            'category' => $serviceData['category'] ?? null,
            'order' => $serviceData['order'] ?? 0,
            'is_active' => true,
            'updated_at' => $now,
        ];

        $existing = DB::table('services')->where('slug', $serviceData['slug'])->first();

        if ($existing) {
            DB::table('services')->where('id', $existing->id)->update($baseRow);
            $serviceId = $existing->id;
        } else {
            // services table still has spatie JSON title/description columns at this point.
            // Write minimal JSON content there so NOT NULL constraints are satisfied.
            // The astrotomic translations get populated below.
            $insertRow = array_merge($baseRow, [
                'title' => json_encode(['en' => $serviceData['translations']['en']['title'] ?? $serviceData['slug']]),
                'description' => isset($serviceData['translations']['en']['description'])
                    ? json_encode(['en' => $serviceData['translations']['en']['description']])
                    : null,
                'short_description' => isset($serviceData['translations']['en']['shortDescription'])
                    ? json_encode(['en' => $serviceData['translations']['en']['shortDescription']])
                    : null,
                'created_at' => $now,
            ]);
            $serviceId = DB::table('services')->insertGetId($insertRow);
        }

        foreach ($serviceData['translations'] as $locale => $trans) {
            $this->upsertServiceTranslation($serviceId, $locale, $trans, $now);
        }

        $this->resyncStats($serviceId, $serviceData, $now);
        $this->resyncPainPoints($serviceId, $serviceData, $now);
        $this->resyncDeliveryItems($serviceId, $serviceData, $now);
        $this->resyncCapabilityGroups($serviceId, $serviceData, $now);
        $this->resyncUseCases($serviceId, $serviceData, $now);
        $this->resyncApproachSteps($serviceId, $serviceData, $now);
        $this->resyncIndustryApplications($serviceId, $serviceData, $now);
        $this->resyncTechnologies($serviceId, $serviceData, $now);
        $this->resyncBusinessImpacts($serviceId, $serviceData, $now);
        $this->resyncDifferentiators($serviceId, $serviceData, $now);
        $this->resyncFeatures($serviceId, $serviceData, $now);
        $this->resyncBenefits($serviceId, $serviceData, $now);
    }

    private function upsertServiceTranslation(int $serviceId, string $locale, array $trans, $now): void
    {
        $hero = $trans['hero'] ?? [];
        $challenge = $trans['challenge'] ?? [];
        $delivery = $trans['howWeDeliver'] ?? [];
        $caps = $trans['capabilities'] ?? [];
        $useCases = $trans['keyUseCases'] ?? [];
        $approach = $trans['ourApproach'] ?? [];
        $industry = $trans['industryApplications'] ?? [];
        $technologies = $trans['technologies'] ?? [];
        $impact = $trans['businessImpact'] ?? [];
        $differentiators = $trans['differentiators'] ?? [];
        $closing = $trans['closing'] ?? [];

        $row = [
            'title' => $trans['title'] ?? '',
            'short_description' => $trans['shortDescription'] ?? null,
            'description' => $trans['description'] ?? null,
            'meta_title' => $trans['metaTitle'] ?? null,
            'meta_description' => $trans['metaDescription'] ?? null,
            'meta_keywords' => $trans['metaKeywords'] ?? null,
            'hero_tagline' => $hero['tagline'] ?? null,
            'hero_headline' => $hero['headline'] ?? null,
            'hero_subheadline' => $hero['subheadline'] ?? null,
            'hero_cta_primary_label' => $hero['ctaPrimaryLabel'] ?? null,
            'hero_cta_secondary_label' => $hero['ctaSecondaryLabel'] ?? null,
            'challenge_title' => $challenge['title'] ?? null,
            'challenge_description' => $challenge['description'] ?? null,
            'delivery_title' => $delivery['title'] ?? null,
            'delivery_description' => $delivery['description'] ?? null,
            'capabilities_title' => $caps['title'] ?? null,
            'use_cases_title' => $useCases['title'] ?? null,
            'use_cases_description' => $useCases['description'] ?? null,
            'approach_title' => $approach['title'] ?? null,
            'approach_description' => $approach['description'] ?? null,
            'industry_title' => $industry['title'] ?? null,
            'industry_description' => $industry['description'] ?? null,
            'technologies_title' => $technologies['title'] ?? null,
            'technologies_description' => $technologies['description'] ?? null,
            'business_impact_title' => $impact['title'] ?? null,
            'business_impact_description' => $impact['description'] ?? null,
            'differentiators_title' => $differentiators['title'] ?? null,
            'closing_title' => $closing['title'] ?? null,
            'closing_subtitle' => $closing['subtitle'] ?? null,
            'published_at' => $now,
            'updated_at' => $now,
        ];

        DB::table('service_translations')->updateOrInsert(
            ['service_id' => $serviceId, 'locale' => $locale],
            array_merge($row, ['created_at' => $now])
        );
    }

    // Each child resync method follows the same pattern:
    // 1. Collect the per-locale arrays
    // 2. Determine length from the primary (en) locale
    // 3. Return early if empty
    // 4. Delete existing rows for this service
    // 5. Insert base rows one by one and translation rows for each locale

    private function resyncStats(int $serviceId, array $data, $now): void
    {
        $byLocale = [];
        foreach ($data['translations'] as $locale => $trans) {
            $byLocale[$locale] = $trans['stats'] ?? [];
        }
        $length = count($byLocale['en'] ?? []);
        if ($length === 0) {
            return;
        }

        DB::table('service_stats')->where('service_id', $serviceId)->delete();

        for ($i = 0; $i < $length; $i++) {
            $stat = $byLocale['en'][$i];
            $statId = DB::table('service_stats')->insertGetId([
                'service_id' => $serviceId,
                'icon' => $stat['icon'] ?? null,
                'order' => $i,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($byLocale as $locale => $stats) {
                $localeStat = $stats[$i] ?? $byLocale['en'][$i];
                DB::table('service_stat_translations')->insert([
                    'service_stat_id' => $statId,
                    'locale' => $locale,
                    'value' => $localeStat['value'] ?? '',
                    'label' => $localeStat['label'] ?? '',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    private function resyncPainPoints(int $serviceId, array $data, $now): void
    {
        $byLocale = [];
        foreach ($data['translations'] as $locale => $trans) {
            $byLocale[$locale] = $trans['challenge']['painPoints'] ?? [];
        }
        $length = count($byLocale['en'] ?? []);
        if ($length === 0) {
            return;
        }

        DB::table('service_pain_points')->where('service_id', $serviceId)->delete();

        for ($i = 0; $i < $length; $i++) {
            $pointId = DB::table('service_pain_points')->insertGetId([
                'service_id' => $serviceId,
                'order' => $i,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($byLocale as $locale => $points) {
                DB::table('service_pain_point_translations')->insert([
                    'service_pain_point_id' => $pointId,
                    'locale' => $locale,
                    'text' => $points[$i] ?? $byLocale['en'][$i],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    private function resyncDeliveryItems(int $serviceId, array $data, $now): void
    {
        $byLocale = [];
        foreach ($data['translations'] as $locale => $trans) {
            $byLocale[$locale] = $trans['howWeDeliver']['items'] ?? [];
        }
        $length = count($byLocale['en'] ?? []);
        if ($length === 0) {
            return;
        }

        DB::table('service_delivery_items')->where('service_id', $serviceId)->delete();

        for ($i = 0; $i < $length; $i++) {
            $item = $byLocale['en'][$i];
            $itemId = DB::table('service_delivery_items')->insertGetId([
                'service_id' => $serviceId,
                'icon' => $item['icon'] ?? null,
                'order' => $i,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($byLocale as $locale => $items) {
                $localeItem = $items[$i] ?? $byLocale['en'][$i];
                DB::table('service_delivery_item_translations')->insert([
                    'service_delivery_item_id' => $itemId,
                    'locale' => $locale,
                    'text' => $localeItem['text'] ?? '',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    private function resyncCapabilityGroups(int $serviceId, array $data, $now): void
    {
        $byLocale = [];
        foreach ($data['translations'] as $locale => $trans) {
            $byLocale[$locale] = $trans['capabilities']['groups'] ?? [];
        }
        $length = count($byLocale['en'] ?? []);
        if ($length === 0) {
            return;
        }

        // FK cascade will clean up capability items
        DB::table('service_capability_groups')->where('service_id', $serviceId)->delete();

        for ($i = 0; $i < $length; $i++) {
            $group = $byLocale['en'][$i];
            $groupId = DB::table('service_capability_groups')->insertGetId([
                'service_id' => $serviceId,
                'icon' => $group['icon'] ?? null,
                'order' => $i,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($byLocale as $locale => $groups) {
                $localeGroup = $groups[$i] ?? $byLocale['en'][$i];
                DB::table('service_capability_group_translations')->insert([
                    'service_capability_group_id' => $groupId,
                    'locale' => $locale,
                    'name' => $localeGroup['name'] ?? '',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            $itemsEn = $group['items'] ?? [];
            foreach ($itemsEn as $itemIndex => $itemEnName) {
                $itemId = DB::table('service_capability_items')->insertGetId([
                    'service_capability_group_id' => $groupId,
                    'order' => $itemIndex,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                foreach ($byLocale as $locale => $groups) {
                    $localeGroup = $groups[$i] ?? $byLocale['en'][$i];
                    $localeItems = $localeGroup['items'] ?? $byLocale['en'][$i]['items'];
                    DB::table('service_capability_item_translations')->insert([
                        'service_capability_item_id' => $itemId,
                        'locale' => $locale,
                        'name' => $localeItems[$itemIndex] ?? $itemEnName,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }
    }

    private function resyncUseCases(int $serviceId, array $data, $now): void
    {
        $byLocale = [];
        foreach ($data['translations'] as $locale => $trans) {
            $byLocale[$locale] = $trans['keyUseCases']['items'] ?? [];
        }
        $length = count($byLocale['en'] ?? []);
        if ($length === 0) {
            return;
        }

        DB::table('service_use_cases')->where('service_id', $serviceId)->delete();

        for ($i = 0; $i < $length; $i++) {
            $caseId = DB::table('service_use_cases')->insertGetId([
                'service_id' => $serviceId,
                'order' => $i,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($byLocale as $locale => $items) {
                DB::table('service_use_case_translations')->insert([
                    'service_use_case_id' => $caseId,
                    'locale' => $locale,
                    'text' => $items[$i] ?? $byLocale['en'][$i],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    private function resyncApproachSteps(int $serviceId, array $data, $now): void
    {
        $byLocale = [];
        foreach ($data['translations'] as $locale => $trans) {
            $byLocale[$locale] = $trans['ourApproach']['items'] ?? [];
        }
        $length = count($byLocale['en'] ?? []);
        if ($length === 0) {
            return;
        }

        DB::table('service_approach_steps')->where('service_id', $serviceId)->delete();

        for ($i = 0; $i < $length; $i++) {
            $step = $byLocale['en'][$i];
            $stepId = DB::table('service_approach_steps')->insertGetId([
                'service_id' => $serviceId,
                'icon' => $step['icon'] ?? null,
                'order' => $i,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($byLocale as $locale => $items) {
                $localeItem = $items[$i] ?? $byLocale['en'][$i];
                DB::table('service_approach_step_translations')->insert([
                    'service_approach_step_id' => $stepId,
                    'locale' => $locale,
                    'title' => $localeItem['title'] ?? '',
                    'description' => $localeItem['description'] ?? null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    private function resyncIndustryApplications(int $serviceId, array $data, $now): void
    {
        $byLocale = [];
        foreach ($data['translations'] as $locale => $trans) {
            $byLocale[$locale] = $trans['industryApplications']['industries'] ?? [];
        }
        $length = count($byLocale['en'] ?? []);
        if ($length === 0) {
            return;
        }

        DB::table('service_industry_applications')->where('service_id', $serviceId)->delete();

        for ($i = 0; $i < $length; $i++) {
            $ind = $byLocale['en'][$i];
            $appId = DB::table('service_industry_applications')->insertGetId([
                'service_id' => $serviceId,
                'icon' => $ind['icon'] ?? null,
                'order' => $i,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($byLocale as $locale => $industries) {
                $localeInd = $industries[$i] ?? $byLocale['en'][$i];
                DB::table('service_industry_application_translations')->insert([
                    'service_industry_application_id' => $appId,
                    'locale' => $locale,
                    'name' => $localeInd['name'] ?? '',
                    'description' => $localeInd['description'] ?? null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            $useCasesEn = $ind['useCases'] ?? [];
            foreach ($useCasesEn as $ucIndex => $ucEn) {
                $ucId = DB::table('service_industry_use_cases')->insertGetId([
                    'service_industry_application_id' => $appId,
                    'order' => $ucIndex,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                foreach ($byLocale as $locale => $industries) {
                    $localeInd = $industries[$i] ?? $byLocale['en'][$i];
                    $localeUseCases = $localeInd['useCases'] ?? $byLocale['en'][$i]['useCases'];
                    DB::table('service_industry_use_case_translations')->insert([
                        'service_industry_use_case_id' => $ucId,
                        'locale' => $locale,
                        'text' => $localeUseCases[$ucIndex] ?? $ucEn,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }
    }

    private function resyncTechnologies(int $serviceId, array $data, $now): void
    {
        $byLocale = [];
        foreach ($data['translations'] as $locale => $trans) {
            $byLocale[$locale] = $trans['technologies']['items'] ?? [];
        }
        $length = count($byLocale['en'] ?? []);
        if ($length === 0) {
            return;
        }

        DB::table('service_technologies')->where('service_id', $serviceId)->delete();

        for ($i = 0; $i < $length; $i++) {
            $tech = $byLocale['en'][$i];
            $techId = DB::table('service_technologies')->insertGetId([
                'service_id' => $serviceId,
                'icon' => $tech['icon'] ?? null,
                'order' => $i,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($byLocale as $locale => $items) {
                $localeItem = $items[$i] ?? $byLocale['en'][$i];
                DB::table('service_technology_translations')->insert([
                    'service_technology_id' => $techId,
                    'locale' => $locale,
                    'name' => $localeItem['name'] ?? '',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    private function resyncBusinessImpacts(int $serviceId, array $data, $now): void
    {
        $byLocale = [];
        foreach ($data['translations'] as $locale => $trans) {
            $byLocale[$locale] = $trans['businessImpact']['items'] ?? [];
        }
        $length = count($byLocale['en'] ?? []);
        if ($length === 0) {
            return;
        }

        DB::table('service_business_impacts')->where('service_id', $serviceId)->delete();

        for ($i = 0; $i < $length; $i++) {
            $item = $byLocale['en'][$i];
            $impactId = DB::table('service_business_impacts')->insertGetId([
                'service_id' => $serviceId,
                'icon' => $item['icon'] ?? null,
                'order' => $i,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($byLocale as $locale => $items) {
                $localeItem = $items[$i] ?? $byLocale['en'][$i];
                DB::table('service_business_impact_translations')->insert([
                    'service_business_impact_id' => $impactId,
                    'locale' => $locale,
                    'title' => $localeItem['title'] ?? '',
                    'description' => $localeItem['description'] ?? null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    private function resyncDifferentiators(int $serviceId, array $data, $now): void
    {
        $byLocale = [];
        foreach ($data['translations'] as $locale => $trans) {
            $byLocale[$locale] = $trans['differentiators']['points'] ?? [];
        }
        $length = count($byLocale['en'] ?? []);
        if ($length === 0) {
            return;
        }

        DB::table('service_differentiators')->where('service_id', $serviceId)->delete();

        for ($i = 0; $i < $length; $i++) {
            $point = $byLocale['en'][$i];
            $diffId = DB::table('service_differentiators')->insertGetId([
                'service_id' => $serviceId,
                'icon' => $point['icon'] ?? null,
                'order' => $i,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($byLocale as $locale => $points) {
                $localePoint = $points[$i] ?? $byLocale['en'][$i];
                DB::table('service_differentiator_translations')->insert([
                    'service_differentiator_id' => $diffId,
                    'locale' => $locale,
                    'title' => $localePoint['title'] ?? '',
                    'description' => $localePoint['description'] ?? null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    private function resyncFeatures(int $serviceId, array $data, $now): void
    {
        $byLocale = [];
        foreach ($data['translations'] as $locale => $trans) {
            $byLocale[$locale] = $trans['features'] ?? [];
        }
        $length = count($byLocale['en'] ?? []);
        if ($length === 0) {
            return;
        }

        DB::table('service_features')->where('service_id', $serviceId)->delete();

        for ($i = 0; $i < $length; $i++) {
            $feat = $byLocale['en'][$i];
            // service_features still has spatie JSON title/description at this point.
            // Satisfy NOT NULL on title by writing minimal JSON; content lives in translations.
            $featureId = DB::table('service_features')->insertGetId([
                'service_id' => $serviceId,
                'title' => json_encode(['en' => $feat['title'] ?? '']),
                'description' => ! empty($feat['description'])
                    ? json_encode(['en' => $feat['description']])
                    : null,
                'icon' => $feat['icon'] ?? null,
                'order' => $i,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($byLocale as $locale => $items) {
                $localeItem = $items[$i] ?? $byLocale['en'][$i];
                DB::table('service_feature_translations')->insert([
                    'service_feature_id' => $featureId,
                    'locale' => $locale,
                    'title' => $localeItem['title'] ?? '',
                    'description' => $localeItem['description'] ?? null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    private function resyncBenefits(int $serviceId, array $data, $now): void
    {
        $byLocale = [];
        foreach ($data['translations'] as $locale => $trans) {
            $byLocale[$locale] = $trans['benefits'] ?? [];
        }
        $length = count($byLocale['en'] ?? []);
        if ($length === 0) {
            return;
        }

        DB::table('service_benefits')->where('service_id', $serviceId)->delete();

        for ($i = 0; $i < $length; $i++) {
            $ben = $byLocale['en'][$i];
            $benefitId = DB::table('service_benefits')->insertGetId([
                'service_id' => $serviceId,
                'title' => json_encode(['en' => $ben['title'] ?? '']),
                'description' => ! empty($ben['description'])
                    ? json_encode(['en' => $ben['description']])
                    : null,
                'icon' => $ben['icon'] ?? null,
                'order' => $i,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($byLocale as $locale => $items) {
                $localeItem = $items[$i] ?? $byLocale['en'][$i];
                DB::table('service_benefit_translations')->insert([
                    'service_benefit_id' => $benefitId,
                    'locale' => $locale,
                    'title' => $localeItem['title'] ?? '',
                    'description' => $localeItem['description'] ?? null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
};
