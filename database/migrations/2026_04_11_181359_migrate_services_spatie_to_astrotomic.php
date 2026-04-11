<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            $this->backfillServices();
            $this->backfillServiceFeatures();
            $this->backfillServiceBenefits();
        });
    }

    public function down(): void
    {
        // Intentionally empty: we never delete data that was migrated forward.
    }

    private function backfillServices(): void
    {
        DB::table('services')->orderBy('id')->each(function ($service) {
            $this->writeTranslations(
                table: 'service_translations',
                parentKey: 'service_id',
                parentId: $service->id,
                jsonColumns: [
                    'title' => $service->title,
                    'description' => $service->description,
                    'short_description' => $service->short_description,
                ],
            );
        });
    }

    private function backfillServiceFeatures(): void
    {
        DB::table('service_features')->orderBy('id')->each(function ($feature) {
            $this->writeTranslations(
                table: 'service_feature_translations',
                parentKey: 'service_feature_id',
                parentId: $feature->id,
                jsonColumns: [
                    'title' => $feature->title,
                    'description' => $feature->description,
                ],
            );
        });
    }

    private function backfillServiceBenefits(): void
    {
        DB::table('service_benefits')->orderBy('id')->each(function ($benefit) {
            $this->writeTranslations(
                table: 'service_benefit_translations',
                parentKey: 'service_benefit_id',
                parentId: $benefit->id,
                jsonColumns: [
                    'title' => $benefit->title,
                    'description' => $benefit->description,
                ],
            );
        });
    }

    /**
     * @param  array<string, ?string>  $jsonColumns  column name → JSON string (or null)
     */
    private function writeTranslations(string $table, string $parentKey, int $parentId, array $jsonColumns): void
    {
        $decoded = [];
        $locales = [];

        foreach ($jsonColumns as $col => $json) {
            if ($json === null || $json === '') {
                $decoded[$col] = [];
                continue;
            }
            $parsed = json_decode($json, true);
            if (! is_array($parsed)) {
                $decoded[$col] = [];
                continue;
            }
            $decoded[$col] = $parsed;
            foreach (array_keys($parsed) as $loc) {
                $locales[$loc] = true;
            }
        }

        if ($locales === []) {
            return;
        }

        foreach (array_keys($locales) as $locale) {
            $values = [];
            foreach ($jsonColumns as $col => $_) {
                $values[$col] = $decoded[$col][$locale] ?? null;
            }

            if (($values['title'] ?? null) === null || $values['title'] === '') {
                continue;
            }

            $now = now();

            DB::table($table)->updateOrInsert(
                [$parentKey => $parentId, 'locale' => $locale],
                array_merge($values, [
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
            );
        }
    }
};
