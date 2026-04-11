<?php

namespace Tests\Feature\Database;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ServiceCmsDataMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_spatie_to_astrotomic_migration_preserves_existing_service_content(): void
    {
        $serviceId = DB::table('services')->insertGetId([
            'title' => json_encode(['en' => 'Strategy Consulting', 'fr' => 'Conseil en stratégie']),
            'description' => json_encode(['en' => 'Helping companies strategize', 'fr' => 'Aider les entreprises']),
            'short_description' => json_encode(['en' => 'Strategy experts', 'fr' => 'Experts en stratégie']),
            'icon' => 'bi-compass',
            'icon_color' => 'primary',
            'category' => 'advisory',
            'slug' => 'strategy-consulting',
            'order' => 1,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $migrationFile = glob(database_path('migrations/*_migrate_services_spatie_to_astrotomic.php'))[0];
        $migration = require $migrationFile;
        $migration->up();

        $this->assertDatabaseHas('service_translations', [
            'service_id' => $serviceId,
            'locale' => 'en',
            'title' => 'Strategy Consulting',
            'description' => 'Helping companies strategize',
            'short_description' => 'Strategy experts',
        ]);

        $this->assertDatabaseHas('service_translations', [
            'service_id' => $serviceId,
            'locale' => 'fr',
            'title' => 'Conseil en stratégie',
            'description' => 'Aider les entreprises',
            'short_description' => 'Experts en stratégie',
        ]);
    }

    public function test_spatie_to_astrotomic_migration_handles_features_and_benefits(): void
    {
        $serviceId = DB::table('services')->insertGetId([
            'title' => json_encode(['en' => 'S']),
            'slug' => 'test-service',
            'order' => 0,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $featureId = DB::table('service_features')->insertGetId([
            'service_id' => $serviceId,
            'title' => json_encode(['en' => 'Scalable', 'fr' => 'Extensible']),
            'description' => json_encode(['en' => 'Grows with you', 'fr' => 'Grandit avec vous']),
            'icon' => 'bi-check',
            'order' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $benefitId = DB::table('service_benefits')->insertGetId([
            'service_id' => $serviceId,
            'title' => json_encode(['en' => 'Fast', 'fr' => 'Rapide']),
            'description' => json_encode(['en' => 'Quick ROI', 'fr' => 'ROI rapide']),
            'icon' => 'bi-star',
            'order' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $migrationFile = glob(database_path('migrations/*_migrate_services_spatie_to_astrotomic.php'))[0];
        $migration = require $migrationFile;
        $migration->up();

        $this->assertDatabaseHas('service_feature_translations', [
            'service_feature_id' => $featureId,
            'locale' => 'fr',
            'title' => 'Extensible',
        ]);

        $this->assertDatabaseHas('service_benefit_translations', [
            'service_benefit_id' => $benefitId,
            'locale' => 'en',
            'title' => 'Fast',
        ]);
    }

    public function test_spatie_migration_is_idempotent(): void
    {
        $serviceId = DB::table('services')->insertGetId([
            'title' => json_encode(['en' => 'Strategy']),
            'slug' => 'strategy',
            'order' => 0,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $migrationFile = glob(database_path('migrations/*_migrate_services_spatie_to_astrotomic.php'))[0];
        $migration = require $migrationFile;

        $migration->up();
        $migration->up();

        $count = DB::table('service_translations')
            ->where('service_id', $serviceId)
            ->where('locale', 'en')
            ->count();

        $this->assertSame(1, $count);
    }
}
