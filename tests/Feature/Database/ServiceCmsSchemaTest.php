<?php

namespace Tests\Feature\Database;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ServiceCmsSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_services_table_has_published_at_column(): void
    {
        $this->assertTrue(Schema::hasColumn('services', 'published_at'));
    }

    public function test_service_translations_table_exists_with_expected_columns(): void
    {
        $this->assertTrue(Schema::hasTable('service_translations'));

        $expected = [
            'id', 'service_id', 'locale',
            'title', 'short_description', 'description',
            'meta_title', 'meta_description', 'meta_keywords',
            'hero_tagline', 'hero_headline', 'hero_subheadline',
            'hero_cta_primary_label', 'hero_cta_secondary_label',
            'challenge_title', 'challenge_description',
            'delivery_title', 'delivery_description',
            'capabilities_title',
            'use_cases_title', 'use_cases_description',
            'approach_title', 'approach_description',
            'industry_title', 'industry_description',
            'technologies_title', 'technologies_description',
            'business_impact_title', 'business_impact_description',
            'differentiators_title',
            'closing_title', 'closing_subtitle',
            'published_at', 'created_at', 'updated_at',
        ];

        foreach ($expected as $column) {
            $this->assertTrue(
                Schema::hasColumn('service_translations', $column),
                "service_translations missing column: {$column}"
            );
        }
    }

    public function test_service_feature_translations_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('service_feature_translations'));

        foreach (['id', 'service_feature_id', 'locale', 'title', 'description', 'created_at', 'updated_at'] as $column) {
            $this->assertTrue(
                Schema::hasColumn('service_feature_translations', $column),
                "service_feature_translations missing column: {$column}"
            );
        }
    }

    public function test_service_benefit_translations_table_exists(): void
    {
        $this->assertTrue(Schema::hasTable('service_benefit_translations'));

        foreach (['id', 'service_benefit_id', 'locale', 'title', 'description', 'created_at', 'updated_at'] as $column) {
            $this->assertTrue(
                Schema::hasColumn('service_benefit_translations', $column),
                "service_benefit_translations missing column: {$column}"
            );
        }
    }

    public function test_service_stats_tables_exist(): void
    {
        $this->assertTrue(Schema::hasTable('service_stats'));
        $this->assertTrue(Schema::hasTable('service_stat_translations'));

        foreach (['id', 'service_id', 'icon', 'order', 'created_at', 'updated_at'] as $column) {
            $this->assertTrue(Schema::hasColumn('service_stats', $column));
        }

        foreach (['id', 'service_stat_id', 'locale', 'value', 'label', 'created_at', 'updated_at'] as $column) {
            $this->assertTrue(Schema::hasColumn('service_stat_translations', $column));
        }
    }

    public function test_service_pain_points_tables_exist(): void
    {
        $this->assertTrue(Schema::hasTable('service_pain_points'));
        $this->assertTrue(Schema::hasTable('service_pain_point_translations'));

        foreach (['id', 'service_id', 'order', 'created_at', 'updated_at'] as $column) {
            $this->assertTrue(Schema::hasColumn('service_pain_points', $column));
        }
        foreach (['id', 'service_pain_point_id', 'locale', 'text', 'created_at', 'updated_at'] as $column) {
            $this->assertTrue(Schema::hasColumn('service_pain_point_translations', $column));
        }
    }

    public function test_service_delivery_items_tables_exist(): void
    {
        $this->assertTrue(Schema::hasTable('service_delivery_items'));
        $this->assertTrue(Schema::hasTable('service_delivery_item_translations'));

        foreach (['id', 'service_id', 'icon', 'order', 'created_at', 'updated_at'] as $column) {
            $this->assertTrue(Schema::hasColumn('service_delivery_items', $column));
        }
        foreach (['id', 'service_delivery_item_id', 'locale', 'text', 'created_at', 'updated_at'] as $column) {
            $this->assertTrue(Schema::hasColumn('service_delivery_item_translations', $column));
        }
    }

    public function test_service_capability_groups_tables_exist(): void
    {
        $this->assertTrue(Schema::hasTable('service_capability_groups'));
        $this->assertTrue(Schema::hasTable('service_capability_group_translations'));

        foreach (['id', 'service_id', 'icon', 'order', 'created_at', 'updated_at'] as $column) {
            $this->assertTrue(Schema::hasColumn('service_capability_groups', $column));
        }
        foreach (['id', 'service_capability_group_id', 'locale', 'name', 'created_at', 'updated_at'] as $column) {
            $this->assertTrue(Schema::hasColumn('service_capability_group_translations', $column));
        }
    }
}
