# Services CMS — Backend Data Migration Implementation Plan (Plan B)

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Migrate existing spatie JSON translatable data into the astrotomic translation tables created in Plan A, backfill the rich content sections (hero, challenge, capabilities, industries, etc.) from a JSON fixture committed to the repo, swap `Service` / `ServiceFeature` / `ServiceBenefit` model traits from spatie to astrotomic, drop the now-unused JSON columns, and update `ServiceSeeder`. End state: services backend fully on astrotomic, all content (card + rich sections) populated in the database, zero reliance on spatie's JSON columns.

**Architecture:** Data migrations use `DB::table()` raw queries (never Eloquent models) so they remain resilient across future model refactors. The order is strict: backfill existing spatie content FIRST, then merge in fixture content, THEN swap traits, THEN drop columns. This keeps the app in a readable state at every step — if any deploy step fails mid-way the previous state is still intact.

**Tech Stack:** Laravel 12, PHP 8.3, PostgreSQL 17, Laravel Sail, astrotomic/laravel-translatable ^11.17, PHPUnit 11.

**Reference spec:** `docs/superpowers/specs/2026-04-11-services-cms-design.md` — phases 2, 3, 4.

**Prior plan:** `docs/superpowers/plans/2026-04-11-services-cms-backend-schema.md` — must be merged and deployed before starting this plan.

**Scope boundary:** Plan B covers phases 2–4 of the spec. NO GraphQL schema changes (that's Plan C). NO frontend changes (that's Plan D). The trait swap will change how `$service->title` resolves (from spatie JSON → astrotomic translation row), but the GraphQL output shape stays identical because the existing schema exposes the same scalar fields.

---

## Prerequisite reading

- `docs/superpowers/specs/2026-04-11-services-cms-design.md` — full design
- `docs/superpowers/plans/2026-04-11-services-cms-backend-schema.md` — Plan A (must already be deployed)
- `.claude/rules/backend-laravel.md` + `.claude/rules/database.md`
- `app/Models/Service.php` — currently using spatie `HasTranslations`, has 10 new astrotomic-child relations from Plan A
- `app/Models/ServiceFeature.php` and `app/Models/ServiceBenefit.php` — spatie
- `app/Models/ServiceTranslation.php` (and the other translation models) — plain Eloquent, ready to be written to
- `database/seeders/ServiceSeeder.php` — currently passes spatie-style arrays `['en' => '...', 'fr' => '...']`
- `gamma-web/locales/en.json` and `gamma-web/locales/fr.json` — source of truth for fixture content under `services.{slug}` (card-level) and `services.details.items.{slug}` (rich content)

Astrotomic astrotomic docs: https://docs.astrotomic.info/laravel-translatable/

---

## Architecture reference — trait swap

### Before Plan B (current state after Plan A)

```php
// app/Models/Service.php
use Spatie\Translatable\HasTranslations;

class Service extends Model
{
    use HasFactory;
    use HasTranslations;

    public array $translatable = ['title', 'description', 'short_description'];

    protected $fillable = ['title', 'description', 'short_description', /* ... */ 'published_at'];
    // ...
}
```

Reading: `$service->title` — spatie returns the current locale value from the JSON column.

### After Plan B

```php
// app/Models/Service.php
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Service extends Model implements TranslatableContract
{
    use HasFactory;
    use Translatable;

    public array $translatedAttributes = ['title', 'description', 'short_description'];

    protected $fillable = ['icon', 'icon_color', 'category', 'slug', 'order', 'is_active', 'published_at'];
    // Note: title/description/short_description REMOVED from $fillable — astrotomic writes them via translations
    // ...
}
```

Reading: `$service->title` — astrotomic looks up `service_translations` for the active locale (header-based middleware), falls back to `en` if missing.

### Data migration approach

ALL data migrations use `DB::table()` — never Eloquent models. This is because:

1. Migrations run across code versions. The model state at migration time may differ from the state at write time.
2. Raw `DB::table()` queries don't depend on trait configuration.
3. It's safer when coexisting with mixed trait state (spatie on one model, astrotomic on another).

---

## File Structure

### Created in this plan

**Data migrations (2 files):**
- `database/migrations/2026_04_11_200001_migrate_services_spatie_to_astrotomic.php` — reads spatie JSON columns, writes to translation tables
- `database/migrations/2026_04_11_200002_backfill_services_cms_content.php` — reads the fixture, populates rich content sections

**Content fixture (1 file):**
- `database/data/services-content-backfill.json` — a committed JSON file holding the full service content in EN + FR, derived from `gamma-web/locales/*.json`

**Final schema migration (1 file):**
- `database/migrations/2026_04_11_200003_drop_spatie_json_columns_from_services_and_children.php` — drops `title`, `description`, `short_description` JSON columns from `services`, `service_features`, `service_benefits`

**Tests (1 file):**
- `tests/Feature/Database/ServiceCmsDataMigrationTest.php` — asserts the backfill migrations preserve content correctly

### Modified in this plan

- `app/Models/Service.php` — swap trait from spatie to astrotomic; remove JSON-column attribute references from `$fillable`; add `HasMany` import if needed (already there)
- `app/Models/ServiceFeature.php` — same trait swap pattern
- `app/Models/ServiceBenefit.php` — same trait swap pattern
- `database/seeders/ServiceSeeder.php` — update to use astrotomic's nested-array write syntax
- `tests/Feature/Models/ServiceCmsModelsTest.php` — add tests proving the swapped `Service` / `ServiceFeature` / `ServiceBenefit` models still work (read+write roundtrip)

### Untouched in this plan

- Any GraphQL schema file
- Any resolver
- Any new model created in Plan A (they're already on astrotomic)
- `Solution`, `Industry`, `FAQ`, `Stat`, `BlogPost` and other models still using spatie — out of scope

---

## Safety notes

- **The user has authorized direct work on `main` branch.** No feature branch needed.
- **No seeders run in production.** All data changes happen through migrations, which are tracked in the `migrations` table and run once.
- **Zero-downtime concern**: see the sequencing below. The trait swap AND column drop must be in a single deploy with the migrations, because old code would fault between the two. For local dev this is fine; for real production we'd either use a maintenance window or ship dual-read code in a prior deploy (out of scope for this plan — the user's production deployment is TBD).
- **Idempotent backfill**: both data migrations use `updateOrInsert` so re-running (e.g., if the migration is manually re-run against a DB that partially succeeded) is safe.

---

## Tasks

### Task 1: Spatie → astrotomic data migration (existing service title/description/short_description)

**Files:**
- Create: `database/migrations/XXXX_XX_XX_migrate_services_spatie_to_astrotomic.php`
- Create: `tests/Feature/Database/ServiceCmsDataMigrationTest.php`

This task backfills existing spatie-formatted content from the 3 parent tables into their new astrotomic translation tables. It reads the JSON columns and writes one row per (parent, locale) pair into the translation tables.

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/Database/ServiceCmsDataMigrationTest.php`:

```php
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
        // Seed a service with spatie-style JSON content BEFORE the trait swap
        // (simulate the production state as of end of Plan A)
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

        // Run the migration manually (since RefreshDatabase already applied everything)
        // Re-run the specific data migration by calling its up() method
        $migrationFile = glob(database_path('migrations/*_migrate_services_spatie_to_astrotomic.php'))[0];
        $migration = require $migrationFile;
        $migration->up();

        // Assert EN and FR translation rows exist for this service
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

        // Run twice
        $migration->up();
        $migration->up();

        // Only ONE translation row should exist, not two
        $count = DB::table('service_translations')
            ->where('service_id', $serviceId)
            ->where('locale', 'en')
            ->count();

        $this->assertSame(1, $count);
    }
}
```

- [ ] **Step 2: Run the test to verify it fails**

Run:
```bash
./sail artisan test --filter=ServiceCmsDataMigrationTest
```

Expected: FAIL — the migration file doesn't exist yet, `glob()` returns empty array.

- [ ] **Step 3: Generate the migration file**

Run:
```bash
./sail artisan make:migration migrate_services_spatie_to_astrotomic
```

- [ ] **Step 4: Write the migration body**

Replace the generated file with:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Data migration: moves existing spatie/laravel-translatable JSON column
     * content into astrotomic translation tables. Uses DB::table() (raw queries)
     * intentionally so it's resilient to future model refactors.
     *
     * Idempotent — re-running produces the same state via updateOrInsert.
     */
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
        // If you need to roll back, drop the translation tables via the Plan A
        // migrations instead (via `./sail artisan migrate:rollback --step=N`).
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
     * For a single parent row, decode each JSON column and write one translation
     * row per locale found across all columns. Uses updateOrInsert for idempotency.
     *
     * @param  array<string, ?string>  $jsonColumns  column name → JSON string (or null)
     */
    private function writeTranslations(string $table, string $parentKey, int $parentId, array $jsonColumns): void
    {
        // Decode each JSON column and collect the union of locales
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

            // Skip rows where the required title is empty
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
```

- [ ] **Step 5: Run the test again**

```bash
./sail artisan test --filter=ServiceCmsDataMigrationTest
```

Expected: all 3 tests PASS.

- [ ] **Step 6: Run the full suite to catch regressions**

```bash
./sail artisan test
```

Expected: still 29 + 3 new = 32 tests passing, zero failures.

- [ ] **Step 7: Commit**

```bash
git add database/migrations/*_migrate_services_spatie_to_astrotomic.php \
        tests/Feature/Database/ServiceCmsDataMigrationTest.php
git commit -m "$(cat <<'EOF'
Data migration: spatie JSON → astrotomic translation tables

Backfills existing services, service_features, and service_benefits
spatie-translatable JSON content into their astrotomic translation tables.
Uses DB::table() raw queries for resilience to future model refactors.
Idempotent via updateOrInsert. Wrapped in a DB transaction.

Part of Plan B of the services CMS rollout.

Co-Authored-By: Claude Opus 4.6 (1M context) <noreply@anthropic.com>
EOF
)"
```

---

### Task 2: Create services content fixture JSON

**Files:**
- Create: `database/data/services-content-backfill.json`

This task extracts the full service content (card-level + rich detail sections) from `gamma-web/locales/en.json` and `gamma-web/locales/fr.json`, transforms it into a fixture format, and commits it to the repo.

- [ ] **Step 1: Read the source locale files**

Read the following two files from the sibling gamma-web project (note: this is the source of truth, you read but do NOT modify these files):
- `/Users/acompaore/Documents/Futurion/Development/Web/gamma/gamma-web/locales/en.json`
- `/Users/acompaore/Documents/Futurion/Development/Web/gamma/gamma-web/locales/fr.json`

Both files have this shape:
```json
{
  "services": {
    "ai": { "title": "...", "slug": "...", "description": "...", "features": [...] },
    "dataEngineering": { ... },
    "cloud": { ... },
    "cybersecurity": { ... },
    "bi": { ... },
    "bigData": { ... },
    "details": {
      "items": {
        "ai-intelligent-systems": {
          "hero": { "tagline": "...", "headline": "...", "subheadline": "...", "ctaPrimary": "...", "ctaSecondary": "...", "stats": [{"value":"...","label":"..."}] },
          "challenge": { "title": "...", "description": "...", "painPoints": ["...", "..."] },
          "howWeDeliver": { "title": "...", "description": "...", "items": [{"icon":"...","text":"..."}] },
          "capabilities": { "title": "...", "groups": [{"icon":"...","name":"...","items":["..."]}] },
          "keyUseCases": { "title": "...", "description": "...", "items": ["..."] },
          "ourApproach": { "title": "...", "description": "...", "items": [{"icon":"...","title":"...","description":"..."}] },
          "industryApplications": { "title": "...", "description": "...", "industries": [{"icon":"...","name":"...","description":"...","useCases":["..."]}] },
          "technologies": { "title": "...", "description": "...", "items": [{"icon":"...","name":"..."}] },
          "businessImpact": { "title": "...", "description": "...", "items": [{"icon":"...","title":"...","description":"..."}] },
          "differentiators": { "title": "...", "points": [{"icon":"...","title":"...","description":"..."}] },
          "closing": { "title": "...", "subtitle": "..." }
        },
        "data-engineering-platforms": { ... },
        "cloud-strategy-engineering": { ... },
        "cybersecurity": { ... },
        "business-intelligence": { ... },
        "big-data": { ... }
      }
    }
  }
}
```

Card-level keys use short names (`ai`, `dataEngineering`, etc.) while detail slugs use the hyphenated form (`ai-intelligent-systems`, etc.). You need to map card → detail. The mapping is typically:
- `ai` → `ai-intelligent-systems`
- `dataEngineering` → `data-engineering-platforms`
- `cloud` → `cloud-strategy-engineering`
- `cybersecurity` → `cybersecurity`
- `bi` → `business-intelligence`
- `bigData` → `big-data`

Verify the mapping by reading the `slug` field on each card-level entry (each entry has a `slug` field that matches the detail key).

- [ ] **Step 2: Write the fixture file**

Create `database/data/services-content-backfill.json` with this top-level structure:

```json
{
  "locales": ["en", "fr"],
  "services": [
    {
      "slug": "ai-intelligent-systems",
      "icon": "bi-cpu",
      "iconColor": "primary",
      "category": "ai",
      "order": 1,
      "translations": {
        "en": {
          "title": "AI & Intelligent Systems",
          "shortDescription": "<from card-level services.ai.description or shortDescription>",
          "description": "<from card-level or detail>",
          "metaTitle": null,
          "metaDescription": null,
          "hero": {
            "tagline": "<from services.details.items.ai-intelligent-systems.hero.tagline>",
            "headline": "<...>",
            "subheadline": "<...>",
            "ctaPrimaryLabel": "<...>",
            "ctaSecondaryLabel": "<...>"
          },
          "stats": [
            {"icon": "bi-people", "value": "<from hero.stats[0].value>", "label": "<from hero.stats[0].label>"}
          ],
          "challenge": {
            "title": "<...>",
            "description": "<...>",
            "painPoints": ["<...>", "<...>"]
          },
          "howWeDeliver": {
            "title": "<...>",
            "description": "<...>",
            "items": [{"icon": "<...>", "text": "<...>"}]
          },
          "capabilities": {
            "title": "<...>",
            "groups": [
              {"icon": "<...>", "name": "<...>", "items": ["<...>", "<...>"]}
            ]
          },
          "keyUseCases": {
            "title": "<...>",
            "description": "<...>",
            "items": ["<...>", "<...>"]
          },
          "ourApproach": {
            "title": "<...>",
            "description": "<...>",
            "items": [{"icon": "<...>", "title": "<...>", "description": "<...>"}]
          },
          "industryApplications": {
            "title": "<...>",
            "description": "<...>",
            "industries": [
              {
                "icon": "<...>",
                "name": "<...>",
                "description": "<...>",
                "useCases": ["<...>", "<...>"]
              }
            ]
          },
          "technologies": {
            "title": "<...>",
            "description": "<...>",
            "items": [{"icon": "<...>", "name": "<...>"}]
          },
          "businessImpact": {
            "title": "<...>",
            "description": "<...>",
            "items": [{"icon": "<...>", "title": "<...>", "description": "<...>"}]
          },
          "differentiators": {
            "title": "<...>",
            "points": [{"icon": "<...>", "title": "<...>", "description": "<...>"}]
          },
          "closing": {
            "title": "<...>",
            "subtitle": "<...>"
          },
          "features": [
            {"icon": "bi-check", "title": "<from services.ai.features[0].title or raw string>", "description": "<...>"}
          ],
          "benefits": []
        },
        "fr": {
          "title": "IA & Systèmes Intelligents",
          "...": "<same structure as en, populated from locales/fr.json>"
        }
      }
    },
    {
      "slug": "data-engineering-platforms",
      "icon": "bi-diagram-3",
      "iconColor": "primary",
      "category": "data",
      "order": 2,
      "translations": { "en": { ... }, "fr": { ... } }
    },
    {
      "slug": "cloud-strategy-engineering",
      "...": "..."
    },
    {
      "slug": "cybersecurity",
      "...": "..."
    },
    {
      "slug": "business-intelligence",
      "...": "..."
    },
    {
      "slug": "big-data",
      "...": "..."
    }
  ]
}
```

**Implementation tips**:
- You don't need to replicate the `<from ...>` annotations in the final file — those are just guiding comments. The final file should have concrete values from the locale files.
- If a locale is missing a field (e.g., the French file doesn't have `closing.subtitle`), copy the English value as a fallback so the fixture isn't lossy.
- If the detail section lists are shorter than their siblings, that's OK — use the actual length from the source file.
- For `icon` fields in the fixture (ONE field per service at top level + on each section item), copy the Bootstrap icon class string exactly as it appears in the frontend if the frontend specifies one; otherwise use a sensible default (e.g., `bi-gear`, `bi-check-circle`).
- The `category` field is what you pick to group services — check `gamma-web/domains/services/pages/services.vue` or the listing UI for the canonical category name for each service.

**Scope discipline**: don't invent new services, new sections, or new fields. The fixture should hold ONLY content that already exists in the gamma-web locale files. If a section is empty in the source, it's empty in the fixture too.

- [ ] **Step 3: Validate the JSON**

Run:
```bash
./sail php -r 'json_decode(file_get_contents("database/data/services-content-backfill.json"), true) ?: die("invalid JSON: " . json_last_error_msg());echo "valid\n";'
```

Expected: `valid`.

- [ ] **Step 4: Commit**

```bash
git add database/data/services-content-backfill.json
git commit -m "$(cat <<'EOF'
Add services content backfill fixture (EN + FR)

Extracts the full services content (6 services + 11 sections each, both locales)
from gamma-web/locales/*.json into a single fixture file. This file is the
one-time source of truth for initial rich content population; after deploy,
admins own the content via the gamma-admin UI.

Part of Plan B of the services CMS rollout.

Co-Authored-By: Claude Opus 4.6 (1M context) <noreply@anthropic.com>
EOF
)"
```

---

### Task 3: Fixture backfill data migration

**Files:**
- Create: `database/migrations/XXXX_XX_XX_backfill_services_cms_content.php`
- Modify: `tests/Feature/Database/ServiceCmsDataMigrationTest.php`

This task writes a data migration that reads the Task 2 fixture and populates all the service content (base row, translations, all child sections + their translations). It uses `DB::table()` raw queries, idempotent via `updateOrInsert` and a delete-then-insert re-sync for child collections.

- [ ] **Step 1: Write failing test**

Append this test method to `tests/Feature/Database/ServiceCmsDataMigrationTest.php`:

```php
public function test_fixture_backfill_populates_all_services(): void
{
    $migrationFile = glob(database_path('migrations/*_backfill_services_cms_content.php'))[0];
    $migration = require $migrationFile;
    $migration->up();

    // The fixture ships 6 services
    $this->assertSame(6, DB::table('services')->count());

    // Each service has 2 translations (en, fr)
    $this->assertSame(12, DB::table('service_translations')->count());

    // Spot-check one service's full tree
    $aiServiceId = DB::table('services')->where('slug', 'ai-intelligent-systems')->value('id');
    $this->assertNotNull($aiServiceId);

    $enTranslation = DB::table('service_translations')
        ->where('service_id', $aiServiceId)
        ->where('locale', 'en')
        ->first();

    $this->assertNotNull($enTranslation->title);
    $this->assertNotNull($enTranslation->hero_headline);
    $this->assertNotNull($enTranslation->challenge_title);
    $this->assertNotNull($enTranslation->closing_title);

    // Spot-check that at least one child collection was populated
    $this->assertGreaterThan(0, DB::table('service_stats')->where('service_id', $aiServiceId)->count());
}

public function test_fixture_backfill_is_idempotent(): void
{
    $migrationFile = glob(database_path('migrations/*_backfill_services_cms_content.php'))[0];
    $migration = require $migrationFile;

    $migration->up();
    $firstCount = DB::table('services')->count();

    $migration->up();
    $secondCount = DB::table('services')->count();

    $this->assertSame($firstCount, $secondCount);
}
```

- [ ] **Step 2: Run to verify failure**

```bash
./sail artisan test --filter=test_fixture_backfill_populates_all_services
```

Expected: FAIL — migration file not found.

- [ ] **Step 3: Generate migration**

```bash
./sail artisan make:migration backfill_services_cms_content
```

- [ ] **Step 4: Write the migration body**

Replace the generated file with:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Data migration: populates the services CMS from
     * database/data/services-content-backfill.json.
     *
     * Uses DB::table() (never Eloquent) for resilience. Wrapped in a single
     * transaction. Idempotent via updateOrInsert on parent/translation tables
     * and delete-then-reinsert on child collections (fixture is authoritative
     * for initial rich content).
     */
    public function up(): void
    {
        $fixturePath = database_path('data/services-content-backfill.json');
        if (! file_exists($fixturePath)) {
            throw new \RuntimeException("Fixture not found at {$fixturePath}");
        }

        $fixture = json_decode(file_get_contents($fixturePath), true);
        if (! is_array($fixture) || ! isset($fixture['services']) || ! is_array($fixture['services'])) {
            throw new \RuntimeException("Invalid fixture structure");
        }

        DB::transaction(function () use ($fixture) {
            foreach ($fixture['services'] as $service) {
                $this->backfillService($service);
            }
        });
    }

    public function down(): void
    {
        // Intentionally empty. Rolling this back would be "delete fixture data",
        // which requires knowing which rows were fixture vs admin-authored.
        // If you need a clean slate, drop the tables via the Plan A migrations.
    }

    private function backfillService(array $serviceData): void
    {
        $now = now();

        // ── Parent row (services) ──
        $baseRow = [
            'slug' => $serviceData['slug'],
            'icon' => $serviceData['icon'] ?? null,
            'icon_color' => $serviceData['iconColor'] ?? null,
            'category' => $serviceData['category'] ?? null,
            'order' => $serviceData['order'] ?? 0,
            'is_active' => true,
            'updated_at' => $now,
        ];

        // Check if service exists
        $existing = DB::table('services')->where('slug', $serviceData['slug'])->first();

        if ($existing) {
            DB::table('services')->where('id', $existing->id)->update($baseRow);
            $serviceId = $existing->id;
        } else {
            // NOTE: spatie JSON columns `title`, `description`, `short_description` still exist
            // on the services table at this point. Insert empty JSON objects so they satisfy
            // NOT NULL if the column is NOT NULL (check actual schema — title is TEXT, might be NULL-acceptable).
            $insertRow = array_merge($baseRow, [
                'title' => json_encode(['en' => $serviceData['translations']['en']['title'] ?? '']),
                'created_at' => $now,
            ]);
            $serviceId = DB::table('services')->insertGetId($insertRow);
        }

        // ── Translations ──
        foreach ($serviceData['translations'] as $locale => $trans) {
            $this->upsertServiceTranslation($serviceId, $locale, $trans, $now);
        }

        // ── Re-sync child collections ──
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
        $howWeDeliver = $trans['howWeDeliver'] ?? [];
        $capabilities = $trans['capabilities'] ?? [];
        $useCases = $trans['keyUseCases'] ?? [];
        $approach = $trans['ourApproach'] ?? [];
        $industry = $trans['industryApplications'] ?? [];
        $technologies = $trans['technologies'] ?? [];
        $businessImpact = $trans['businessImpact'] ?? [];
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
            'delivery_title' => $howWeDeliver['title'] ?? null,
            'delivery_description' => $howWeDeliver['description'] ?? null,
            'capabilities_title' => $capabilities['title'] ?? null,
            'use_cases_title' => $useCases['title'] ?? null,
            'use_cases_description' => $useCases['description'] ?? null,
            'approach_title' => $approach['title'] ?? null,
            'approach_description' => $approach['description'] ?? null,
            'industry_title' => $industry['title'] ?? null,
            'industry_description' => $industry['description'] ?? null,
            'technologies_title' => $technologies['title'] ?? null,
            'technologies_description' => $technologies['description'] ?? null,
            'business_impact_title' => $businessImpact['title'] ?? null,
            'business_impact_description' => $businessImpact['description'] ?? null,
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

    private function resyncStats(int $serviceId, array $data, $now): void
    {
        $statsByLocale = [];
        foreach ($data['translations'] as $locale => $trans) {
            $statsByLocale[$locale] = $trans['stats'] ?? ($trans['hero']['stats'] ?? []);
        }

        // Determine the length from the primary locale (en)
        $length = count($statsByLocale['en'] ?? []);
        if ($length === 0) {
            return;
        }

        // Delete existing rows for this service
        DB::table('service_stats')->where('service_id', $serviceId)->delete();

        for ($i = 0; $i < $length; $i++) {
            $stat = $statsByLocale['en'][$i] ?? [];
            $statId = DB::table('service_stats')->insertGetId([
                'service_id' => $serviceId,
                'icon' => $stat['icon'] ?? null,
                'order' => $i,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($statsByLocale as $locale => $stats) {
                $localeStat = $stats[$i] ?? $statsByLocale['en'][$i];
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

        // Cascade delete will also clean up capability items via FK
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

            // Nested capability items
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

            // Nested industry use cases
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
            // NOTE: service_features table still has spatie JSON title/description columns at this point.
            // We write empty JSON there to satisfy NOT NULL constraints, then fill real content via translations.
            $featureId = DB::table('service_features')->insertGetId([
                'service_id' => $serviceId,
                'title' => json_encode(['en' => $feat['title'] ?? '']),
                'description' => $feat['description'] ?? null ? json_encode(['en' => $feat['description']]) : null,
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
                'description' => $ben['description'] ?? null ? json_encode(['en' => $ben['description']]) : null,
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
```

- [ ] **Step 5: Run the test**

```bash
./sail artisan test --filter=test_fixture_backfill_populates_all_services
./sail artisan test --filter=test_fixture_backfill_is_idempotent
```

Expected: both PASS.

- [ ] **Step 6: Run full suite**

```bash
./sail artisan test
```

Expected: 34+ tests passing (29 + 3 data migration tests + 2 new from this task). Zero failures.

- [ ] **Step 7: Commit**

```bash
git add database/migrations/*_backfill_services_cms_content.php \
        tests/Feature/Database/ServiceCmsDataMigrationTest.php
git commit -m "$(cat <<'EOF'
Data migration: backfill services CMS content from fixture

Reads database/data/services-content-backfill.json and populates every
service's translations + all 12 child collections (stats, pain points,
delivery items, capability groups + nested items, use cases, approach
steps, industry applications + nested use cases, technologies, business
impacts, differentiators, features, benefits) for both EN and FR locales.

Idempotent: re-running via updateOrInsert on parent/translations and
delete-then-insert on child collections. Uses DB::table() raw queries.

Part of Plan B of the services CMS rollout.

Co-Authored-By: Claude Opus 4.6 (1M context) <noreply@anthropic.com>
EOF
)"
```

---

### Task 4: Swap model traits (Service, ServiceFeature, ServiceBenefit) + update seeder

**Files:**
- Modify: `app/Models/Service.php`
- Modify: `app/Models/ServiceFeature.php`
- Modify: `app/Models/ServiceBenefit.php`
- Modify: `database/seeders/ServiceSeeder.php`
- Modify: `tests/Feature/Models/ServiceCmsModelsTest.php` (add roundtrip tests for the swapped models)

This task swaps the 3 models off spatie and onto astrotomic. The JSON columns are NOT dropped yet — they just become unused. The drop happens in Task 5.

- [ ] **Step 1: Add failing tests for the swapped models**

Append to `tests/Feature/Models/ServiceCmsModelsTest.php`:

```php
public function test_service_model_uses_astrotomic_translatable(): void
{
    $service = Service::create([
        'slug' => 'astro-test',
        'icon' => 'bi-test',
        'category' => 'test',
        'order' => 1,
        'is_active' => true,
    ]);

    $service->translateOrNew('en')->fill([
        'title' => 'Astro Test',
        'description' => 'Testing astrotomic',
        'short_description' => 'Short',
    ])->save();

    $service->translateOrNew('fr')->fill([
        'title' => 'Test Astro',
        'description' => 'Test astrotomic',
        'short_description' => 'Court',
    ])->save();

    app()->setLocale('en');
    $this->assertSame('Astro Test', $service->fresh()->title);

    app()->setLocale('fr');
    $this->assertSame('Test Astro', $service->fresh()->title);
}

public function test_service_feature_model_uses_astrotomic_translatable(): void
{
    $service = Service::factory()->create();
    $feature = \App\Models\ServiceFeature::create([
        'service_id' => $service->id,
        'icon' => 'bi-star',
        'order' => 0,
    ]);

    $feature->translateOrNew('en')->fill(['title' => 'Fast', 'description' => 'Very quick'])->save();
    $feature->translateOrNew('fr')->fill(['title' => 'Rapide', 'description' => 'Très rapide'])->save();

    app()->setLocale('fr');
    $this->assertSame('Rapide', $feature->fresh()->title);
}

public function test_service_benefit_model_uses_astrotomic_translatable(): void
{
    $service = Service::factory()->create();
    $benefit = \App\Models\ServiceBenefit::create([
        'service_id' => $service->id,
        'icon' => 'bi-star',
        'order' => 0,
    ]);

    $benefit->translateOrNew('en')->fill(['title' => 'ROI', 'description' => 'Strong return'])->save();
    app()->setLocale('en');
    $this->assertSame('ROI', $benefit->fresh()->title);
}
```

Also, the existing `test_service_has_all_new_relations` test creates a `Service::factory()` which currently uses spatie JSON via `title` in its definition. After the trait swap, the factory needs to write via translations. Look at `database/factories/ServiceFactory.php` and keep it compatible (see Step 4 below).

- [ ] **Step 2: Run the new tests to verify they fail**

```bash
./sail artisan test --filter=test_service_model_uses_astrotomic_translatable
```

Expected: FAIL — Service still uses spatie, `$service->translateOrNew()` method doesn't exist (that's an astrotomic method).

- [ ] **Step 3: Swap the Service model trait**

Open `app/Models/Service.php` and replace contents with:

```php
<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Service extends Model implements TranslatableContract
{
    use HasFactory;
    use Translatable;

    public array $translatedAttributes = ['title', 'description', 'short_description'];

    protected $fillable = [
        'icon',
        'icon_color',
        'category',
        'slug',
        'order',
        'is_active',
        'published_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
        'published_at' => 'datetime',
    ];

    // Relationships
    public function features(): HasMany
    {
        return $this->hasMany(ServiceFeature::class)->orderBy('order');
    }

    public function benefits(): HasMany
    {
        return $this->hasMany(ServiceBenefit::class)->orderBy('order');
    }

    public function stats(): HasMany
    {
        return $this->hasMany(ServiceStat::class)->orderBy('order');
    }

    public function painPoints(): HasMany
    {
        return $this->hasMany(ServicePainPoint::class)->orderBy('order');
    }

    public function deliveryItems(): HasMany
    {
        return $this->hasMany(ServiceDeliveryItem::class)->orderBy('order');
    }

    public function capabilityGroups(): HasMany
    {
        return $this->hasMany(ServiceCapabilityGroup::class)->orderBy('order');
    }

    public function useCases(): HasMany
    {
        return $this->hasMany(ServiceUseCase::class)->orderBy('order');
    }

    public function approachSteps(): HasMany
    {
        return $this->hasMany(ServiceApproachStep::class)->orderBy('order');
    }

    public function industryApplications(): HasMany
    {
        return $this->hasMany(ServiceIndustryApplication::class)->orderBy('order');
    }

    public function technologies(): HasMany
    {
        return $this->hasMany(ServiceTechnology::class)->orderBy('order');
    }

    public function businessImpacts(): HasMany
    {
        return $this->hasMany(ServiceBusinessImpact::class)->orderBy('order');
    }

    public function differentiators(): HasMany
    {
        return $this->hasMany(ServiceDifferentiator::class)->orderBy('order');
    }

    // Scopes
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('order');
    }

    // Events
    protected static function booted(): void
    {
        static::creating(function (Service $service) {
            if (empty($service->slug)) {
                $service->slug = Str::slug($service->getTranslation('title', 'en')->title ?? 'service');
            }
        });
    }
}
```

**Note on `booted()` slug hook**: in spatie, `$service->getTranslation('title', 'en')` returned a string. In astrotomic, it returns a translation object; `->title` reads the attribute. Adjust the hook accordingly. If the adjustment is tricky, alternative: read directly from `$service->translations` or `$service->getTranslationOrNew('en')->title`. Use whichever cleanly returns the EN title string.

Actually, review astrotomic's API: the method name `getTranslation` may differ. Look at how the trait exposes locale reading during model events. A safer approach: require the caller to set the slug explicitly when creating via the admin UI later, and relax the auto-slug hook to just do `$service->slug ?: Str::slug($service->getTranslation('en', false)?->title ?? 'service')` — the `getTranslation('en', false)` signature in astrotomic returns the translation model instance (or null if not found).

If unsure about the exact API, **test it in tinker before committing** and adjust as needed. The test in Step 1 will fail if the slug hook is broken, so iterate until green.

- [ ] **Step 4: Update `database/factories/ServiceFactory.php`**

The current factory uses spatie syntax (`'title' => fake()->words(3, true)`). After the trait swap, this won't work because `title` isn't in the base row. Replace the factory with:

```php
<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        $slug = Str::slug(fake()->words(3, true));

        return [
            'slug' => $slug,
            'icon' => fake()->randomElement(['chart', 'database', 'shield', 'cloud', 'brain']),
            'category' => fake()->randomElement(['Technology', 'Security', 'Analytics']),
            'order' => fake()->numberBetween(1, 10),
            'is_active' => true,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Service $service) {
            if ($service->translations()->count() === 0) {
                $service->translateOrNew('en')->fill([
                    'title' => Str::headline($service->slug),
                    'description' => fake()->paragraph(),
                    'short_description' => fake()->sentence(),
                ])->save();
            }
        });
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
```

This creates the base row and then adds an EN translation via `afterCreating`. The `translations()` method is astrotomic's default relation accessor on translatable models.

- [ ] **Step 5: Swap the ServiceFeature model**

Open `app/Models/ServiceFeature.php` and replace with:

```php
<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceFeature extends Model implements TranslatableContract
{
    use HasFactory;
    use Translatable;

    public array $translatedAttributes = ['title', 'description'];

    protected $fillable = [
        'service_id',
        'icon',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
```

- [ ] **Step 6: Swap the ServiceBenefit model**

Open `app/Models/ServiceBenefit.php` and replace with:

```php
<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceBenefit extends Model implements TranslatableContract
{
    use HasFactory;
    use Translatable;

    public array $translatedAttributes = ['title', 'description'];

    protected $fillable = [
        'service_id',
        'icon',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
```

- [ ] **Step 7: Update `database/seeders/ServiceSeeder.php`**

Read the current seeder first (`cat database/seeders/ServiceSeeder.php`) to understand its current shape. It currently uses spatie's array syntax like `['en' => '...', 'fr' => '...']` to write translated values. Update it to use astrotomic's `fill(['en' => [...], 'fr' => [...]])` pattern.

Example transformation:

```php
// BEFORE (spatie)
Service::create([
    'title' => ['en' => 'Strategy', 'fr' => 'Stratégie'],
    'description' => ['en' => '...', 'fr' => '...'],
    'slug' => 'strategy',
    'icon' => 'bi-compass',
    'order' => 1,
    'is_active' => true,
]);

// AFTER (astrotomic)
$service = Service::create([
    'slug' => 'strategy',
    'icon' => 'bi-compass',
    'order' => 1,
    'is_active' => true,
]);
$service->fill([
    'en' => ['title' => 'Strategy', 'description' => '...'],
    'fr' => ['title' => 'Stratégie', 'description' => '...'],
])->save();
```

Do the same for any features/benefits seeded via the seeder.

**Important**: the seeder is only run on `migrate:fresh --seed` in local dev, never in production. It's not on the hot path, but it should still produce working data so developers can seed their local DB.

- [ ] **Step 8: Run migrations from scratch and tests**

Run:
```bash
./sail artisan migrate:fresh
./sail artisan test
```

Expected: full suite passes — including the new roundtrip tests and the existing ServiceQueryTest (which may hit the refactored trait on Service).

If `ServiceQueryTest` fails because it was previously writing via spatie JSON, fix the test to use astrotomic's write pattern instead. The test file is `tests/Feature/GraphQL/ServiceQueryTest.php` — do NOT change what it asserts about the query response shape, only change how it sets up the test fixture.

- [ ] **Step 9: Live GraphQL smoke test**

```bash
./sail artisan tinker --execute='
use App\Models\Service;
$s = Service::create(["slug" => "smoke", "icon" => "bi-test", "order" => 99, "is_active" => true]);
$s->translateOrNew("en")->fill(["title" => "Smoke Test", "description" => "check"])->save();
$s->translateOrNew("fr")->fill(["title" => "Test de fumée", "description" => "vérifier"])->save();
echo "created id=".$s->id.PHP_EOL;
'

curl -s -X POST http://localhost:8880/graphql \
  -H "Content-Type: application/json" \
  -d '{"query":"{ services(limit: 5) { id title description slug } }"}'

curl -s -X POST http://localhost:8880/graphql \
  -H "Content-Type: application/json" \
  -H "Accept-Language: fr" \
  -d '{"query":"{ services(limit: 5) { id title description slug } }"}'
```

Expected: first query returns EN title ("Smoke Test"); second query returns FR title ("Test de fumée") if header-based locale middleware is wired up. If FR doesn't switch, that's fine — Plan C wires up the locale middleware for public queries; for now, the default locale resolution via `App::getLocale()` takes over and returns whatever `config('app.locale')` is set to.

Clean up:
```bash
./sail artisan tinker --execute='App\Models\Service::where("slug", "smoke")->delete();'
```

- [ ] **Step 10: Commit**

```bash
git add app/Models/Service.php \
        app/Models/ServiceFeature.php \
        app/Models/ServiceBenefit.php \
        database/factories/ServiceFactory.php \
        database/seeders/ServiceSeeder.php \
        tests/Feature/Models/ServiceCmsModelsTest.php
git commit -m "$(cat <<'EOF'
Swap Service/Feature/Benefit traits from spatie to astrotomic

Service, ServiceFeature, and ServiceBenefit now use astrotomic
Translatable (via service_translations, service_feature_translations,
and service_benefit_translations tables populated in previous tasks).

Also updates ServiceFactory to use afterCreating for EN translation,
and ServiceSeeder to use astrotomic's nested-array fill syntax.

JSON columns on the parent tables still exist but are no longer read
by the models — Task 5 drops them.

Co-Authored-By: Claude Opus 4.6 (1M context) <noreply@anthropic.com>
EOF
)"
```

---

### Task 5: Drop old JSON columns from services, service_features, service_benefits

**Files:**
- Create: `database/migrations/XXXX_XX_XX_drop_spatie_json_columns_from_services_and_children.php`

- [ ] **Step 1: Generate migration**

```bash
./sail artisan make:migration drop_spatie_json_columns_from_services_and_children
```

- [ ] **Step 2: Write the migration body**

Replace the generated file with:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Final cleanup: drops the JSON columns used by spatie/laravel-translatable
     * on services, service_features, and service_benefits. All content has
     * already been migrated to the astrotomic translation tables by the
     * preceding data migrations, and the model traits have been swapped
     * so nothing reads from these columns anymore.
     */
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['title', 'description', 'short_description']);
        });

        Schema::table('service_features', function (Blueprint $table) {
            $table->dropColumn(['title', 'description']);
        });

        Schema::table('service_benefits', function (Blueprint $table) {
            $table->dropColumn(['title', 'description']);
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->text('title')->nullable();
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
        });

        Schema::table('service_features', function (Blueprint $table) {
            $table->text('title')->nullable();
            $table->text('description')->nullable();
        });

        Schema::table('service_benefits', function (Blueprint $table) {
            $table->text('title')->nullable();
            $table->text('description')->nullable();
        });
    }
};
```

- [ ] **Step 3: Run migration and full test suite**

```bash
./sail artisan migrate
./sail artisan test
```

Expected: all tests pass. The trait-swapped models read from translation tables; the old columns are gone but no code references them anymore.

- [ ] **Step 4: Live GraphQL smoke test**

```bash
curl -s -X POST http://localhost:8880/graphql \
  -H "Content-Type: application/json" \
  -d '{"query":"{ services(limit: 5) { id title description slug features { id title } benefits { id title } } }"}'
```

Expected: valid JSON response with resolved titles from translation tables.

- [ ] **Step 5: Commit**

```bash
git add database/migrations/*_drop_spatie_json_columns_from_services_and_children.php
git commit -m "$(cat <<'EOF'
Drop spatie JSON columns from services, service_features, service_benefits

Now that all models are on astrotomic and content is in the translation tables,
the old JSON columns are unused and can be removed. down() is reversible.

Part of Plan B of the services CMS rollout.

Co-Authored-By: Claude Opus 4.6 (1M context) <noreply@anthropic.com>
EOF
)"
```

---

### Task 6: Final verification + push

**Files:** none (git operations)

- [ ] **Step 1: Run the full test suite one more time**

```bash
./sail artisan test
```

Expected: every test passes. Report total count.

- [ ] **Step 2: Live GraphQL verification — EN**

```bash
curl -s -X POST http://localhost:8880/graphql \
  -H "Content-Type: application/json" \
  -d '{"query":"{ services(limit: 10) { id title description slug icon category is_active } }"}' | head -50
```

Expected: all 6 fixture services returned with EN titles.

- [ ] **Step 3: Live GraphQL verification — single service with children**

```bash
curl -s -X POST http://localhost:8880/graphql \
  -H "Content-Type: application/json" \
  -d '{"query":"{ service(id: 1) { id title description slug features { id title description } benefits { id title description } } }"}' | head -50
```

Expected: one service with populated features/benefits (both with translated titles).

- [ ] **Step 4: Schema + row count sanity check**

```bash
./sail artisan tinker --execute='
echo "services: ".App\Models\Service::count().PHP_EOL;
echo "service_translations: ".DB::table("service_translations")->count().PHP_EOL;
echo "service_features: ".App\Models\ServiceFeature::count().PHP_EOL;
echo "service_feature_translations: ".DB::table("service_feature_translations")->count().PHP_EOL;
echo "service_benefits: ".App\Models\ServiceBenefit::count().PHP_EOL;
echo "service_benefit_translations: ".DB::table("service_benefit_translations")->count().PHP_EOL;
echo "service_stats: ".DB::table("service_stats")->count().PHP_EOL;
echo "service_pain_points: ".DB::table("service_pain_points")->count().PHP_EOL;
echo "service_capability_groups: ".DB::table("service_capability_groups")->count().PHP_EOL;
echo "service_capability_items: ".DB::table("service_capability_items")->count().PHP_EOL;
echo "service_industry_applications: ".DB::table("service_industry_applications")->count().PHP_EOL;
echo "service_industry_use_cases: ".DB::table("service_industry_use_cases")->count().PHP_EOL;
'
```

Expected: all counts > 0 where the fixture had content. Specifically ~6 services, 12 translations (6 × 2 locales), >0 stats/pain_points/etc.

- [ ] **Step 5: Confirm git state**

```bash
git log origin/main..HEAD --oneline
git status
```

Expected: 5 new commits on main (1 per task + any fix commits), working tree clean.

- [ ] **Step 6: Push to origin/main**

```bash
git push origin main
```

Expected: all commits pushed successfully.

---

## Self-review checklist

After Task 6 completes, verify:

1. **Schema state** — `services`, `service_features`, `service_benefits` NO longer have JSON `title`/`description`/`short_description` columns
2. **Model state** — all 3 swapped models use `Astrotomic\Translatable\Translatable` trait, implement `TranslatableContract`, have `$translatedAttributes` arrays
3. **Data state** — `service_translations` has rows for every existing service in both EN and FR
4. **Fixture loaded** — the 6 fixture services from `gamma-web/locales/*.json` are in the DB with all 12 rich content child collections populated
5. **Tests green** — full PHPUnit suite passes
6. **GraphQL works** — live queries against `localhost:8880/graphql` return resolved scalar fields (not JSON blobs) and relation lists
7. **No spatie references on Service/Feature/Benefit** — grep for `HasTranslations` in those 3 files; expect zero matches

## Rollback considerations

If something goes wrong mid-plan:

- **Between Tasks 1 and 2**: rerun Task 1's migration (idempotent). Tests should still pass against spatie-driven models.
- **Between Tasks 2 and 3**: just delete the fixture file; no data has been loaded yet.
- **Between Tasks 3 and 4**: the DB has been backfilled. Models still use spatie. Tests pass against spatie. Safe state. You can rollback Task 3 by deleting the rows it inserted (painful — safer is to `migrate:fresh`).
- **Between Tasks 4 and 5**: models use astrotomic, DB has both sets of data. Old spatie JSON columns are unused but still present. To roll back, revert the model file changes.
- **After Task 5**: JSON columns are gone. To roll back, run `migrate:rollback --step=1` to recreate the columns, then revert the data migrations, then revert the model changes. Nontrivial — use a staging environment first.

## Next plans (context)

- **Plan C — GraphQL API**: implement grouped Service + ServiceForAdmin types, `service(slug: ...)` resolver that projects the flat translation columns into sections (hero, challenge, etc.), admin CRUD mutations, PHPUnit feature tests for the new query shapes, mcp-graphql verification.
- **Plan D — Frontend switch + cleanup**: gamma-web `useServiceDetail` composable, replace i18n `tm()` lookups with GraphQL query, keep i18n fallback until stable, remove fallback after cutover, delete dead `services.details.items` from locale JSON files.
