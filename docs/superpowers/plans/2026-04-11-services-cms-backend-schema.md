# Services CMS — Backend Schema Implementation Plan (Plan A)

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Install astrotomic/laravel-translatable alongside the existing spatie installation, create every new services CMS table (1 parent translation table + 12 child base/translation pairs + 2 translation tables for existing features/benefits + `published_at` on `services`), and create their Eloquent models and factories. End state: all new tables exist, all new models are defined, the existing spatie setup is fully intact, and nothing in production behavior has changed.

**Architecture:** Every repeating content type gets a base table + translation table pair (`service_<thing>` + `service_<thing>_translations`). The parent `services` table gets ONE fat translations table (`service_translations`) for 1-1 fields (hero copy, section titles, SEO meta). Astrotomic's `Translatable` trait + naming convention resolves base models to translation models automatically.

**Tech Stack:** Laravel 12, PHP 8.3, PostgreSQL 17, Laravel Sail, `astrotomic/laravel-translatable`, PHPUnit 11.

**Reference spec:** `docs/superpowers/specs/2026-04-11-services-cms-design.md`.

**Scope boundary:** This plan covers Phase 1 of the spec only. It does NOT touch existing data, does NOT swap model traits, does NOT drop old JSON columns, and does NOT add any GraphQL schema. Those are Plans B, C, and D.

---

## Prerequisite reading

Before starting, read these files to understand existing patterns:

- `docs/superpowers/specs/2026-04-11-services-cms-design.md` — the full design spec
- `.claude/rules/backend-laravel.md` — Laravel conventions (always use `./sail`, never `config:cache` in dev, etc.)
- `.claude/rules/database.md` — database conventions
- `.claude/rules/testing.md` — PHPUnit conventions
- `app/Models/Service.php` — the current Service model (uses spatie `HasTranslations`)
- `app/Models/ServiceFeature.php` — current child model
- `app/Models/ServiceBenefit.php` — current child model
- `database/migrations/2025_10_17_100004_create_services_table.php` — original services schema
- `database/migrations/2026_03_22_003623_convert_translatable_columns_to_json_for_i18n.php` — the migration that introduced spatie JSON columns
- `database/factories/ServiceFactory.php` — existing factory style

Astrotomic package docs: https://docs.astrotomic.info/laravel-translatable/

---

## Astrotomic model pattern (reference)

Every NEW base model created in this plan uses this shape:

```php
<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Example extends Model implements TranslatableContract
{
    use HasFactory;
    use Translatable;

    protected $fillable = ['parent_id', 'order', /* non-translatable cols */];

    protected $casts = ['order' => 'integer'];

    public array $translatedAttributes = ['title', 'description'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Parent::class);
    }
}
```

And its corresponding translation model:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExampleTranslation extends Model
{
    public $timestamps = true;

    protected $fillable = ['title', 'description'];
}
```

Astrotomic auto-resolves `Example` → `ExampleTranslation` via naming convention. FK defaults to `example_id` (snake_case singular of the base model). The `locale` column is auto-detected.

**Important:** The existing `Service`, `ServiceFeature`, `ServiceBenefit` models still use spatie's `HasTranslations` trait with `public array $translatable = [...]`. We do NOT swap them in this plan — only new models use astrotomic. Plan B swaps the existing ones.

---

## File Structure

### Created in this plan

**Configuration (1 file):**
- `config/translatable.php` (published from astrotomic vendor)

**Schema migrations (16 new files):**
- `2026_04_11_100001_add_published_at_to_services_table.php`
- `2026_04_11_100002_create_service_translations_table.php`
- `2026_04_11_100003_create_service_feature_translations_table.php`
- `2026_04_11_100004_create_service_benefit_translations_table.php`
- `2026_04_11_100005_create_service_stats_tables.php`
- `2026_04_11_100006_create_service_pain_points_tables.php`
- `2026_04_11_100007_create_service_delivery_items_tables.php`
- `2026_04_11_100008_create_service_capability_groups_tables.php`
- `2026_04_11_100009_create_service_capability_items_tables.php`
- `2026_04_11_100010_create_service_use_cases_tables.php`
- `2026_04_11_100011_create_service_approach_steps_tables.php`
- `2026_04_11_100012_create_service_industry_applications_tables.php`
- `2026_04_11_100013_create_service_industry_use_cases_tables.php`
- `2026_04_11_100014_create_service_technologies_tables.php`
- `2026_04_11_100015_create_service_business_impacts_tables.php`
- `2026_04_11_100016_create_service_differentiators_tables.php`

Note: actual timestamps will be set by `./sail artisan make:migration`. The ordering above is enforced by the natural alphabetical order of the timestamps; as long as all 16 are created sequentially on the same day, they will run in dependency order.

**New models (27 files):**
- `app/Models/ServiceTranslation.php`
- `app/Models/ServiceFeatureTranslation.php`
- `app/Models/ServiceBenefitTranslation.php`
- `app/Models/ServiceStat.php` + `app/Models/ServiceStatTranslation.php`
- `app/Models/ServicePainPoint.php` + `app/Models/ServicePainPointTranslation.php`
- `app/Models/ServiceDeliveryItem.php` + `app/Models/ServiceDeliveryItemTranslation.php`
- `app/Models/ServiceCapabilityGroup.php` + `app/Models/ServiceCapabilityGroupTranslation.php`
- `app/Models/ServiceCapabilityItem.php` + `app/Models/ServiceCapabilityItemTranslation.php`
- `app/Models/ServiceUseCase.php` + `app/Models/ServiceUseCaseTranslation.php`
- `app/Models/ServiceApproachStep.php` + `app/Models/ServiceApproachStepTranslation.php`
- `app/Models/ServiceIndustryApplication.php` + `app/Models/ServiceIndustryApplicationTranslation.php`
- `app/Models/ServiceIndustryUseCase.php` + `app/Models/ServiceIndustryUseCaseTranslation.php`
- `app/Models/ServiceTechnology.php` + `app/Models/ServiceTechnologyTranslation.php`
- `app/Models/ServiceBusinessImpact.php` + `app/Models/ServiceBusinessImpactTranslation.php`
- `app/Models/ServiceDifferentiator.php` + `app/Models/ServiceDifferentiatorTranslation.php`

**New factories (12 files — one per base child model):**
- `database/factories/ServiceStatFactory.php`
- `database/factories/ServicePainPointFactory.php`
- `database/factories/ServiceDeliveryItemFactory.php`
- `database/factories/ServiceCapabilityGroupFactory.php`
- `database/factories/ServiceCapabilityItemFactory.php`
- `database/factories/ServiceUseCaseFactory.php`
- `database/factories/ServiceApproachStepFactory.php`
- `database/factories/ServiceIndustryApplicationFactory.php`
- `database/factories/ServiceIndustryUseCaseFactory.php`
- `database/factories/ServiceTechnologyFactory.php`
- `database/factories/ServiceBusinessImpactFactory.php`
- `database/factories/ServiceDifferentiatorFactory.php`

**Tests (2 files):**
- `tests/Feature/Database/ServiceCmsSchemaTest.php` — asserts every new table exists with the expected columns
- `tests/Feature/Models/ServiceCmsModelsTest.php` — asserts each new model can create + translate rows via astrotomic

### Modified in this plan

- `composer.json` + `composer.lock` (via `composer require astrotomic/laravel-translatable`)
- `app/Models/Service.php` — add `published_at` to fillable + casts (NOT the trait swap)

### Untouched in this plan

- Existing migration files for services / features / benefits
- Existing `Service`, `ServiceFeature`, `ServiceBenefit` traits (still spatie)
- Existing `database/seeders/ServiceSeeder.php`
- Any GraphQL files
- Anything outside the services domain (solutions, industries, etc.)

---

## Tasks

### Task 1: Create feature branch

**Files:** none (git operation)

- [ ] **Step 1: Fetch latest origin and branch from main**

Run:
```bash
cd /Users/acompaore/Documents/Futurion/Development/Web/gamma/gamma-api
git fetch origin
git checkout -b feat/services-cms-schema origin/main
```

Expected: switched to new branch `feat/services-cms-schema` tracking `origin/main`.

- [ ] **Step 2: Verify clean working tree**

Run:
```bash
git status
```

Expected: `On branch feat/services-cms-schema` / `nothing to commit, working tree clean`.

---

### Task 2: Install astrotomic/laravel-translatable

**Files:**
- Modify: `composer.json`
- Modify: `composer.lock`

- [ ] **Step 1: Install the package**

Run:
```bash
./sail composer require astrotomic/laravel-translatable
```

Expected: package resolved and installed. Composer will pick the version compatible with Laravel 12 (likely `^11.x`). Both `composer.json` and `composer.lock` updated.

- [ ] **Step 2: Verify installation**

Run:
```bash
./sail composer show astrotomic/laravel-translatable
```

Expected: package info prints with the installed version.

- [ ] **Step 3: Commit**

```bash
git add composer.json composer.lock
git commit -m "$(cat <<'EOF'
Add astrotomic/laravel-translatable for services CMS

Installs astrotomic alongside the existing spatie/laravel-translatable.
Spatie stays for Solutions and Industries until they're migrated in a
later spec. This package is needed for Plans A-D of the services CMS.

Co-Authored-By: Claude Opus 4.6 (1M context) <noreply@anthropic.com>
EOF
)"
```

---

### Task 3: Publish and configure translatable config

**Files:**
- Create: `config/translatable.php`

- [ ] **Step 1: Publish the config file**

Run:
```bash
./sail artisan vendor:publish --tag=translatable
```

Expected: `config/translatable.php` created.

- [ ] **Step 2: Set supported locales and fallback**

Open `config/translatable.php` and ensure these values are set (other keys can stay at defaults):

```php
'locales' => [
    'en',
    'fr',
],

'use_fallback' => true,

'fallback_locale' => 'en',

'use_property_fallback' => true,
```

- [ ] **Step 3: Clear config cache**

Run:
```bash
./sail artisan config:clear
```

Expected: no output / success.

- [ ] **Step 4: Commit**

```bash
git add config/translatable.php
git commit -m "$(cat <<'EOF'
Configure astrotomic with en+fr supported locales and fallback to en

Co-Authored-By: Claude Opus 4.6 (1M context) <noreply@anthropic.com>
EOF
)"
```

---

### Task 4: Add `published_at` column to services table

**Files:**
- Create: `database/migrations/XXXX_XX_XX_add_published_at_to_services_table.php` (timestamp generated)
- Modify: `app/Models/Service.php`
- Create: `tests/Feature/Database/ServiceCmsSchemaTest.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/Database/ServiceCmsSchemaTest.php`:

```php
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
}
```

- [ ] **Step 2: Run test to verify it fails**

Run:
```bash
./sail artisan test --filter=ServiceCmsSchemaTest
```

Expected: FAIL. `Schema::hasColumn('services', 'published_at')` returns false.

- [ ] **Step 3: Generate the migration**

Run:
```bash
./sail artisan make:migration add_published_at_to_services_table
```

Expected: new migration file created under `database/migrations/`. Note the exact filename.

- [ ] **Step 4: Write the migration body**

Replace the generated file contents with:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->timestamp('published_at')->nullable()->after('is_active');
            $table->index('published_at');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropIndex(['published_at']);
            $table->dropColumn('published_at');
        });
    }
};
```

- [ ] **Step 5: Update the Service model**

Open `app/Models/Service.php` and modify the `$fillable` and `$casts` arrays:

```php
protected $fillable = [
    'title',
    'description',
    'short_description',
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
```

Leave everything else in `Service.php` untouched (keep spatie `HasTranslations` and `public array $translatable`).

- [ ] **Step 6: Run migration and test**

Run:
```bash
./sail artisan migrate
./sail artisan test --filter=ServiceCmsSchemaTest
```

Expected: migration runs successfully; test PASSES.

- [ ] **Step 7: Commit**

```bash
git add database/migrations/*_add_published_at_to_services_table.php \
        tests/Feature/Database/ServiceCmsSchemaTest.php \
        app/Models/Service.php
git commit -m "$(cat <<'EOF'
Add published_at column to services for draft/publish workflow

Co-Authored-By: Claude Opus 4.6 (1M context) <noreply@anthropic.com>
EOF
)"
```

---

### Task 5: Create `service_translations` table (parent translation table)

**Files:**
- Create: `database/migrations/XXXX_XX_XX_create_service_translations_table.php`
- Create: `app/Models/ServiceTranslation.php`
- Modify: `tests/Feature/Database/ServiceCmsSchemaTest.php`

- [ ] **Step 1: Add failing test assertions**

Open `tests/Feature/Database/ServiceCmsSchemaTest.php` and add this test method:

```php
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
```

- [ ] **Step 2: Run test to verify it fails**

Run:
```bash
./sail artisan test --filter=test_service_translations_table_exists_with_expected_columns
```

Expected: FAIL — table does not exist.

- [ ] **Step 3: Generate the migration**

Run:
```bash
./sail artisan make:migration create_service_translations_table
```

- [ ] **Step 4: Write the migration body**

Replace the generated file with:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);

            // Card / page fields
            $table->string('title');
            $table->string('short_description', 500)->nullable();
            $table->text('description')->nullable();

            // SEO meta
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->string('meta_keywords', 500)->nullable();

            // Hero section
            $table->string('hero_tagline')->nullable();
            $table->string('hero_headline', 500)->nullable();
            $table->text('hero_subheadline')->nullable();
            $table->string('hero_cta_primary_label', 100)->nullable();
            $table->string('hero_cta_secondary_label', 100)->nullable();

            // Challenge section
            $table->string('challenge_title')->nullable();
            $table->text('challenge_description')->nullable();

            // How-we-deliver section
            $table->string('delivery_title')->nullable();
            $table->text('delivery_description')->nullable();

            // Capabilities section (title only; groups are a separate table)
            $table->string('capabilities_title')->nullable();

            // Key use cases section
            $table->string('use_cases_title')->nullable();
            $table->text('use_cases_description')->nullable();

            // Our approach section
            $table->string('approach_title')->nullable();
            $table->text('approach_description')->nullable();

            // Industry applications section
            $table->string('industry_title')->nullable();
            $table->text('industry_description')->nullable();

            // Technologies section
            $table->string('technologies_title')->nullable();
            $table->text('technologies_description')->nullable();

            // Business impact section
            $table->string('business_impact_title')->nullable();
            $table->text('business_impact_description')->nullable();

            // Differentiators section (title only; points are a separate table)
            $table->string('differentiators_title')->nullable();

            // Closing CTA
            $table->string('closing_title')->nullable();
            $table->text('closing_subtitle')->nullable();

            // Per-locale publish
            $table->timestamp('published_at')->nullable();

            $table->timestamps();

            $table->unique(['service_id', 'locale']);
            $table->index(['service_id', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_translations');
    }
};
```

- [ ] **Step 5: Create the ServiceTranslation model**

Create `app/Models/ServiceTranslation.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceTranslation extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'title',
        'short_description',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'hero_tagline',
        'hero_headline',
        'hero_subheadline',
        'hero_cta_primary_label',
        'hero_cta_secondary_label',
        'challenge_title',
        'challenge_description',
        'delivery_title',
        'delivery_description',
        'capabilities_title',
        'use_cases_title',
        'use_cases_description',
        'approach_title',
        'approach_description',
        'industry_title',
        'industry_description',
        'technologies_title',
        'technologies_description',
        'business_impact_title',
        'business_impact_description',
        'differentiators_title',
        'closing_title',
        'closing_subtitle',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];
}
```

Note: `service_id` and `locale` are NOT in `$fillable` — astrotomic writes those automatically via the parent relation.

- [ ] **Step 6: Run migration + test**

Run:
```bash
./sail artisan migrate
./sail artisan test --filter=ServiceCmsSchemaTest
```

Expected: all assertions PASS.

- [ ] **Step 7: Commit**

```bash
git add database/migrations/*_create_service_translations_table.php \
        app/Models/ServiceTranslation.php \
        tests/Feature/Database/ServiceCmsSchemaTest.php
git commit -m "$(cat <<'EOF'
Create service_translations table and model

Single fat translations table for all 1-1 fields on a service
(card content, SEO meta, hero copy, and every section title/description).
Repeating content like stats, pain points, and capability groups get
their own base+translation table pairs in subsequent commits.

Co-Authored-By: Claude Opus 4.6 (1M context) <noreply@anthropic.com>
EOF
)"
```

---

### Task 6: Create `service_feature_translations` table

**Files:**
- Create: `database/migrations/XXXX_XX_XX_create_service_feature_translations_table.php`
- Create: `app/Models/ServiceFeatureTranslation.php`
- Modify: `tests/Feature/Database/ServiceCmsSchemaTest.php`

- [ ] **Step 1: Add failing test**

Append to `ServiceCmsSchemaTest.php`:

```php
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
```

- [ ] **Step 2: Run test, verify failure**

Run: `./sail artisan test --filter=test_service_feature_translations_table_exists` → FAIL.

- [ ] **Step 3: Generate + write migration**

Run:
```bash
./sail artisan make:migration create_service_feature_translations_table
```

Replace file contents with:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_feature_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_feature_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['service_feature_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_feature_translations');
    }
};
```

- [ ] **Step 4: Create the translation model**

Create `app/Models/ServiceFeatureTranslation.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceFeatureTranslation extends Model
{
    public $timestamps = true;

    protected $fillable = ['title', 'description'];
}
```

- [ ] **Step 5: Run migration + test, verify pass**

Run:
```bash
./sail artisan migrate
./sail artisan test --filter=ServiceCmsSchemaTest
```

Expected: all tests PASS.

- [ ] **Step 6: Commit**

```bash
git add database/migrations/*_create_service_feature_translations_table.php \
        app/Models/ServiceFeatureTranslation.php \
        tests/Feature/Database/ServiceCmsSchemaTest.php
git commit -m "$(cat <<'EOF'
Create service_feature_translations table and model

Co-Authored-By: Claude Opus 4.6 (1M context) <noreply@anthropic.com>
EOF
)"
```

---

### Task 7: Create `service_benefit_translations` table

**Files:**
- Create: `database/migrations/XXXX_XX_XX_create_service_benefit_translations_table.php`
- Create: `app/Models/ServiceBenefitTranslation.php`
- Modify: `tests/Feature/Database/ServiceCmsSchemaTest.php`

- [ ] **Step 1: Add failing test**

Append to `ServiceCmsSchemaTest.php`:

```php
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
```

- [ ] **Step 2: Run test, verify failure** — `./sail artisan test --filter=test_service_benefit_translations_table_exists` → FAIL.

- [ ] **Step 3: Generate + write migration**

Run: `./sail artisan make:migration create_service_benefit_translations_table`.

Replace file contents with:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_benefit_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_benefit_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['service_benefit_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_benefit_translations');
    }
};
```

- [ ] **Step 4: Create the translation model**

Create `app/Models/ServiceBenefitTranslation.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceBenefitTranslation extends Model
{
    public $timestamps = true;

    protected $fillable = ['title', 'description'];
}
```

- [ ] **Step 5: Run migration + test, verify pass**

Run:
```bash
./sail artisan migrate
./sail artisan test --filter=ServiceCmsSchemaTest
```

Expected: all tests PASS.

- [ ] **Step 6: Commit**

```bash
git add database/migrations/*_create_service_benefit_translations_table.php \
        app/Models/ServiceBenefitTranslation.php \
        tests/Feature/Database/ServiceCmsSchemaTest.php
git commit -m "$(cat <<'EOF'
Create service_benefit_translations table and model

Co-Authored-By: Claude Opus 4.6 (1M context) <noreply@anthropic.com>
EOF
)"
```

---

## Child entity tasks (Tasks 8–19) — reference section

Tasks 8–19 all create one child entity pair: a base table, a translation table, a base model, a translation model, a factory, and a schema test. They share the same structure; only the table name, columns, and translatable fields differ. Task 8 (`service_stats`) is written in full detail. Tasks 9–19 give the full migration + model code specific to each entity (no code is omitted — every task is self-contained), but the step-by-step structure mirrors Task 8.

**Shared step pattern** (each of Tasks 8–19 follows this):

1. Add a schema test method asserting both new tables and their columns exist
2. Run the test, verify it fails
3. Generate the migration (`./sail artisan make:migration`)
4. Write the migration body (creates BOTH the base table and its translation table)
5. Create the base model (astrotomic `Translatable`)
6. Create the translation model (plain Eloquent model)
7. Create the factory for the base model
8. Run migration + test
9. Commit

---

### Task 8: Create `service_stats` + `service_stat_translations`

**Files:**
- Create: `database/migrations/XXXX_XX_XX_create_service_stats_tables.php`
- Create: `app/Models/ServiceStat.php`
- Create: `app/Models/ServiceStatTranslation.php`
- Create: `database/factories/ServiceStatFactory.php`
- Modify: `tests/Feature/Database/ServiceCmsSchemaTest.php`
- Modify: `app/Models/Service.php` (add `stats()` relation)

- [ ] **Step 1: Add failing schema test**

Append to `ServiceCmsSchemaTest.php`:

```php
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
```

- [ ] **Step 2: Run test, verify failure** — `./sail artisan test --filter=test_service_stats_tables_exist` → FAIL.

- [ ] **Step 3: Generate the migration**

Run: `./sail artisan make:migration create_service_stats_tables`.

- [ ] **Step 4: Write the migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('icon')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index(['service_id', 'order']);
        });

        Schema::create('service_stat_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_stat_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('value', 100);
            $table->string('label', 255);
            $table->timestamps();

            $table->unique(['service_stat_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_stat_translations');
        Schema::dropIfExists('service_stats');
    }
};
```

- [ ] **Step 5: Create `ServiceStat` base model**

Create `app/Models/ServiceStat.php`:

```php
<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceStat extends Model implements TranslatableContract
{
    use HasFactory;
    use Translatable;

    protected $fillable = ['service_id', 'icon', 'order'];

    protected $casts = ['order' => 'integer'];

    public array $translatedAttributes = ['value', 'label'];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
```

- [ ] **Step 6: Create `ServiceStatTranslation` model**

Create `app/Models/ServiceStatTranslation.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceStatTranslation extends Model
{
    public $timestamps = true;

    protected $fillable = ['value', 'label'];
}
```

- [ ] **Step 7: Create `ServiceStatFactory`**

Create `database/factories/ServiceStatFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServiceStat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceStat>
 */
class ServiceStatFactory extends Factory
{
    protected $model = ServiceStat::class;

    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'icon' => fake()->randomElement(['bi-people', 'bi-graph-up', 'bi-star', 'bi-trophy']),
            'order' => fake()->numberBetween(0, 10),
        ];
    }
}
```

- [ ] **Step 8: Add `stats()` relation on Service**

Open `app/Models/Service.php` and add this method alongside the existing `features()` and `benefits()`:

```php
public function stats(): HasMany
{
    return $this->hasMany(ServiceStat::class)->orderBy('order');
}
```

The `HasMany` import should already exist from the existing `features()` relation.

- [ ] **Step 9: Run migration + test**

Run:
```bash
./sail artisan migrate
./sail artisan test --filter=ServiceCmsSchemaTest
```

Expected: all tests PASS.

- [ ] **Step 10: Commit**

```bash
git add database/migrations/*_create_service_stats_tables.php \
        app/Models/ServiceStat.php \
        app/Models/ServiceStatTranslation.php \
        database/factories/ServiceStatFactory.php \
        app/Models/Service.php \
        tests/Feature/Database/ServiceCmsSchemaTest.php
git commit -m "$(cat <<'EOF'
Create service_stats + translations tables, model, factory

Hero stats section — each service has ordered stats with icon,
value, and label. Value and label are translatable.

Co-Authored-By: Claude Opus 4.6 (1M context) <noreply@anthropic.com>
EOF
)"
```

---

### Task 9: Create `service_pain_points` + `service_pain_point_translations`

**Files:**
- Create: migration `XXXX_XX_XX_create_service_pain_points_tables.php`
- Create: `app/Models/ServicePainPoint.php`
- Create: `app/Models/ServicePainPointTranslation.php`
- Create: `database/factories/ServicePainPointFactory.php`
- Modify: `tests/Feature/Database/ServiceCmsSchemaTest.php`
- Modify: `app/Models/Service.php` (add `painPoints()` relation)

Follow the 10-step structure from Task 8. Concrete content for each step:

- [ ] **Step 1: Add schema test**

```php
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
```

- [ ] **Step 2: Run test → FAIL**

- [ ] **Step 3: Generate migration** — `./sail artisan make:migration create_service_pain_points_tables`

- [ ] **Step 4: Migration body**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_pain_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index(['service_id', 'order']);
        });

        Schema::create('service_pain_point_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_pain_point_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->text('text');
            $table->timestamps();

            $table->unique(['service_pain_point_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_pain_point_translations');
        Schema::dropIfExists('service_pain_points');
    }
};
```

- [ ] **Step 5: `app/Models/ServicePainPoint.php`**

```php
<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicePainPoint extends Model implements TranslatableContract
{
    use HasFactory;
    use Translatable;

    protected $fillable = ['service_id', 'order'];

    protected $casts = ['order' => 'integer'];

    public array $translatedAttributes = ['text'];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
```

- [ ] **Step 6: `app/Models/ServicePainPointTranslation.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicePainPointTranslation extends Model
{
    public $timestamps = true;

    protected $fillable = ['text'];
}
```

- [ ] **Step 7: `database/factories/ServicePainPointFactory.php`**

```php
<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServicePainPoint;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServicePainPointFactory extends Factory
{
    protected $model = ServicePainPoint::class;

    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'order' => fake()->numberBetween(0, 10),
        ];
    }
}
```

- [ ] **Step 8: Add `painPoints()` relation on Service**

Add to `app/Models/Service.php`:

```php
public function painPoints(): HasMany
{
    return $this->hasMany(ServicePainPoint::class)->orderBy('order');
}
```

- [ ] **Step 9: Run migration + test → PASS**

```bash
./sail artisan migrate
./sail artisan test --filter=ServiceCmsSchemaTest
```

- [ ] **Step 10: Commit**

```bash
git add database/migrations/*_create_service_pain_points_tables.php \
        app/Models/ServicePainPoint.php \
        app/Models/ServicePainPointTranslation.php \
        database/factories/ServicePainPointFactory.php \
        app/Models/Service.php \
        tests/Feature/Database/ServiceCmsSchemaTest.php
git commit -m "$(cat <<'EOF'
Create service_pain_points + translations tables, model, factory

Challenge section — each service has ordered pain points (translatable text).

Co-Authored-By: Claude Opus 4.6 (1M context) <noreply@anthropic.com>
EOF
)"
```

---

### Task 10: Create `service_delivery_items` + translations

- [ ] **Step 1: Schema test**

```php
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
```

- [ ] **Step 2: Run test → FAIL**

- [ ] **Step 3: Generate migration** — `./sail artisan make:migration create_service_delivery_items_tables`

- [ ] **Step 4: Migration body**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_delivery_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('icon')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index(['service_id', 'order']);
        });

        Schema::create('service_delivery_item_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_delivery_item_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->text('text');
            $table->timestamps();

            $table->unique(['service_delivery_item_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_delivery_item_translations');
        Schema::dropIfExists('service_delivery_items');
    }
};
```

- [ ] **Step 5: `app/Models/ServiceDeliveryItem.php`**

```php
<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceDeliveryItem extends Model implements TranslatableContract
{
    use HasFactory;
    use Translatable;

    protected $fillable = ['service_id', 'icon', 'order'];

    protected $casts = ['order' => 'integer'];

    public array $translatedAttributes = ['text'];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
```

- [ ] **Step 6: `app/Models/ServiceDeliveryItemTranslation.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceDeliveryItemTranslation extends Model
{
    public $timestamps = true;

    protected $fillable = ['text'];
}
```

- [ ] **Step 7: `database/factories/ServiceDeliveryItemFactory.php`**

```php
<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServiceDeliveryItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceDeliveryItemFactory extends Factory
{
    protected $model = ServiceDeliveryItem::class;

    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'icon' => fake()->randomElement(['bi-check-circle', 'bi-lightning', 'bi-gear']),
            'order' => fake()->numberBetween(0, 10),
        ];
    }
}
```

- [ ] **Step 8: Add `deliveryItems()` relation on Service**

```php
public function deliveryItems(): HasMany
{
    return $this->hasMany(ServiceDeliveryItem::class)->orderBy('order');
}
```

- [ ] **Step 9: Run migration + test → PASS**

- [ ] **Step 10: Commit**

Commit message: `Create service_delivery_items + translations tables, model, factory`

---

### Task 11: Create `service_capability_groups` + translations

- [ ] **Step 1: Schema test**

```php
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
```

- [ ] **Step 2–4: Generate + write migration**

Run: `./sail artisan make:migration create_service_capability_groups_tables`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_capability_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('icon')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index(['service_id', 'order']);
        });

        Schema::create('service_capability_group_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_capability_group_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('name', 255);
            $table->timestamps();

            $table->unique(['service_capability_group_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_capability_group_translations');
        Schema::dropIfExists('service_capability_groups');
    }
};
```

- [ ] **Step 5: `app/Models/ServiceCapabilityGroup.php`**

```php
<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceCapabilityGroup extends Model implements TranslatableContract
{
    use HasFactory;
    use Translatable;

    protected $fillable = ['service_id', 'icon', 'order'];

    protected $casts = ['order' => 'integer'];

    public array $translatedAttributes = ['name'];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ServiceCapabilityItem::class)->orderBy('order');
    }
}
```

Note the `items()` relation — this is the nested relationship defined in the spec (capability groups → capability items). `ServiceCapabilityItem` is created in Task 12; leaving this relation here is fine because PHP only resolves it at call time.

- [ ] **Step 6: `app/Models/ServiceCapabilityGroupTranslation.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceCapabilityGroupTranslation extends Model
{
    public $timestamps = true;

    protected $fillable = ['name'];
}
```

- [ ] **Step 7: `database/factories/ServiceCapabilityGroupFactory.php`**

```php
<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServiceCapabilityGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceCapabilityGroupFactory extends Factory
{
    protected $model = ServiceCapabilityGroup::class;

    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'icon' => fake()->randomElement(['bi-gear', 'bi-lightbulb', 'bi-shield']),
            'order' => fake()->numberBetween(0, 10),
        ];
    }
}
```

- [ ] **Step 8: Add `capabilityGroups()` relation on Service**

```php
public function capabilityGroups(): HasMany
{
    return $this->hasMany(ServiceCapabilityGroup::class)->orderBy('order');
}
```

- [ ] **Step 9: Run migration + test → PASS**

- [ ] **Step 10: Commit**

Commit message: `Create service_capability_groups + translations tables, model, factory`

---

### Task 12: Create `service_capability_items` + translations

**Note:** This child hangs off `service_capability_groups`, NOT directly off `services`. FK is `capability_group_id`.

- [ ] **Step 1: Schema test**

```php
public function test_service_capability_items_tables_exist(): void
{
    $this->assertTrue(Schema::hasTable('service_capability_items'));
    $this->assertTrue(Schema::hasTable('service_capability_item_translations'));

    foreach (['id', 'service_capability_group_id', 'order', 'created_at', 'updated_at'] as $column) {
        $this->assertTrue(Schema::hasColumn('service_capability_items', $column));
    }
    foreach (['id', 'service_capability_item_id', 'locale', 'name', 'created_at', 'updated_at'] as $column) {
        $this->assertTrue(Schema::hasColumn('service_capability_item_translations', $column));
    }
}
```

- [ ] **Step 2: Run test → FAIL**

- [ ] **Step 3: Generate migration** — `./sail artisan make:migration create_service_capability_items_tables`

- [ ] **Step 4: Migration body**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_capability_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_capability_group_id')
                ->constrained('service_capability_groups')
                ->cascadeOnDelete();
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index(['service_capability_group_id', 'order']);
        });

        Schema::create('service_capability_item_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_capability_item_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('name', 255);
            $table->timestamps();

            $table->unique(['service_capability_item_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_capability_item_translations');
        Schema::dropIfExists('service_capability_items');
    }
};
```

- [ ] **Step 5: `app/Models/ServiceCapabilityItem.php`**

```php
<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceCapabilityItem extends Model implements TranslatableContract
{
    use HasFactory;
    use Translatable;

    protected $fillable = ['service_capability_group_id', 'order'];

    protected $casts = ['order' => 'integer'];

    public array $translatedAttributes = ['name'];

    public function group(): BelongsTo
    {
        return $this->belongsTo(ServiceCapabilityGroup::class, 'service_capability_group_id');
    }
}
```

- [ ] **Step 6: `app/Models/ServiceCapabilityItemTranslation.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceCapabilityItemTranslation extends Model
{
    public $timestamps = true;

    protected $fillable = ['name'];
}
```

- [ ] **Step 7: `database/factories/ServiceCapabilityItemFactory.php`**

```php
<?php

namespace Database\Factories;

use App\Models\ServiceCapabilityGroup;
use App\Models\ServiceCapabilityItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceCapabilityItemFactory extends Factory
{
    protected $model = ServiceCapabilityItem::class;

    public function definition(): array
    {
        return [
            'service_capability_group_id' => ServiceCapabilityGroup::factory(),
            'order' => fake()->numberBetween(0, 10),
        ];
    }
}
```

- [ ] **Step 8: No Service model change** (items belong to groups, not directly to services)

- [ ] **Step 9: Run migration + test → PASS**

- [ ] **Step 10: Commit**

Commit message: `Create service_capability_items + translations tables, model, factory`

---

### Task 13: Create `service_use_cases` + translations

- [ ] **Step 1: Schema test**

```php
public function test_service_use_cases_tables_exist(): void
{
    $this->assertTrue(Schema::hasTable('service_use_cases'));
    $this->assertTrue(Schema::hasTable('service_use_case_translations'));

    foreach (['id', 'service_id', 'order', 'created_at', 'updated_at'] as $column) {
        $this->assertTrue(Schema::hasColumn('service_use_cases', $column));
    }
    foreach (['id', 'service_use_case_id', 'locale', 'text', 'created_at', 'updated_at'] as $column) {
        $this->assertTrue(Schema::hasColumn('service_use_case_translations', $column));
    }
}
```

- [ ] **Step 2: Run test → FAIL**

- [ ] **Step 3: Generate migration** — `./sail artisan make:migration create_service_use_cases_tables`

- [ ] **Step 4: Migration body**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_use_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index(['service_id', 'order']);
        });

        Schema::create('service_use_case_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_use_case_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->text('text');
            $table->timestamps();

            $table->unique(['service_use_case_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_use_case_translations');
        Schema::dropIfExists('service_use_cases');
    }
};
```

- [ ] **Step 5: `app/Models/ServiceUseCase.php`**

```php
<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceUseCase extends Model implements TranslatableContract
{
    use HasFactory;
    use Translatable;

    protected $fillable = ['service_id', 'order'];

    protected $casts = ['order' => 'integer'];

    public array $translatedAttributes = ['text'];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
```

- [ ] **Step 6: `app/Models/ServiceUseCaseTranslation.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceUseCaseTranslation extends Model
{
    public $timestamps = true;

    protected $fillable = ['text'];
}
```

- [ ] **Step 7: `database/factories/ServiceUseCaseFactory.php`**

```php
<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServiceUseCase;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceUseCaseFactory extends Factory
{
    protected $model = ServiceUseCase::class;

    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'order' => fake()->numberBetween(0, 10),
        ];
    }
}
```

- [ ] **Step 8: Add `useCases()` relation on Service**

```php
public function useCases(): HasMany
{
    return $this->hasMany(ServiceUseCase::class)->orderBy('order');
}
```

- [ ] **Step 9: Run migration + test → PASS**

- [ ] **Step 10: Commit**

Commit message: `Create service_use_cases + translations tables, model, factory`

---

### Task 14: Create `service_approach_steps` + translations

- [ ] **Step 1: Schema test**

```php
public function test_service_approach_steps_tables_exist(): void
{
    $this->assertTrue(Schema::hasTable('service_approach_steps'));
    $this->assertTrue(Schema::hasTable('service_approach_step_translations'));

    foreach (['id', 'service_id', 'icon', 'order', 'created_at', 'updated_at'] as $column) {
        $this->assertTrue(Schema::hasColumn('service_approach_steps', $column));
    }
    foreach (['id', 'service_approach_step_id', 'locale', 'title', 'description', 'created_at', 'updated_at'] as $column) {
        $this->assertTrue(Schema::hasColumn('service_approach_step_translations', $column));
    }
}
```

- [ ] **Step 2: Run test → FAIL**

- [ ] **Step 3: Generate migration** — `./sail artisan make:migration create_service_approach_steps_tables`

- [ ] **Step 4: Migration body**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_approach_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('icon')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index(['service_id', 'order']);
        });

        Schema::create('service_approach_step_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_approach_step_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['service_approach_step_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_approach_step_translations');
        Schema::dropIfExists('service_approach_steps');
    }
};
```

- [ ] **Step 5: `app/Models/ServiceApproachStep.php`**

```php
<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceApproachStep extends Model implements TranslatableContract
{
    use HasFactory;
    use Translatable;

    protected $fillable = ['service_id', 'icon', 'order'];

    protected $casts = ['order' => 'integer'];

    public array $translatedAttributes = ['title', 'description'];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
```

- [ ] **Step 6: `app/Models/ServiceApproachStepTranslation.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceApproachStepTranslation extends Model
{
    public $timestamps = true;

    protected $fillable = ['title', 'description'];
}
```

- [ ] **Step 7: `database/factories/ServiceApproachStepFactory.php`**

```php
<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServiceApproachStep;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceApproachStepFactory extends Factory
{
    protected $model = ServiceApproachStep::class;

    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'icon' => fake()->randomElement(['bi-1-circle', 'bi-2-circle', 'bi-3-circle', 'bi-4-circle']),
            'order' => fake()->numberBetween(0, 10),
        ];
    }
}
```

- [ ] **Step 8: Add `approachSteps()` relation on Service**

```php
public function approachSteps(): HasMany
{
    return $this->hasMany(ServiceApproachStep::class)->orderBy('order');
}
```

- [ ] **Step 9: Run migration + test → PASS**

- [ ] **Step 10: Commit**

Commit message: `Create service_approach_steps + translations tables, model, factory`

---

### Task 15: Create `service_industry_applications` + translations

- [ ] **Step 1: Schema test**

```php
public function test_service_industry_applications_tables_exist(): void
{
    $this->assertTrue(Schema::hasTable('service_industry_applications'));
    $this->assertTrue(Schema::hasTable('service_industry_application_translations'));

    foreach (['id', 'service_id', 'icon', 'order', 'created_at', 'updated_at'] as $column) {
        $this->assertTrue(Schema::hasColumn('service_industry_applications', $column));
    }
    foreach (['id', 'service_industry_application_id', 'locale', 'name', 'description', 'created_at', 'updated_at'] as $column) {
        $this->assertTrue(Schema::hasColumn('service_industry_application_translations', $column));
    }
}
```

- [ ] **Step 2: Run test → FAIL**

- [ ] **Step 3: Generate migration** — `./sail artisan make:migration create_service_industry_applications_tables`

- [ ] **Step 4: Migration body**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_industry_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('icon')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index(['service_id', 'order']);
        });

        Schema::create('service_industry_application_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_industry_application_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['service_industry_application_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_industry_application_translations');
        Schema::dropIfExists('service_industry_applications');
    }
};
```

- [ ] **Step 5: `app/Models/ServiceIndustryApplication.php`**

```php
<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceIndustryApplication extends Model implements TranslatableContract
{
    use HasFactory;
    use Translatable;

    protected $fillable = ['service_id', 'icon', 'order'];

    protected $casts = ['order' => 'integer'];

    public array $translatedAttributes = ['name', 'description'];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function useCases(): HasMany
    {
        return $this->hasMany(ServiceIndustryUseCase::class)->orderBy('order');
    }
}
```

(`ServiceIndustryUseCase` is created in Task 16 — the relation resolves at call time, so order of model file creation doesn't matter.)

- [ ] **Step 6: `app/Models/ServiceIndustryApplicationTranslation.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceIndustryApplicationTranslation extends Model
{
    public $timestamps = true;

    protected $fillable = ['name', 'description'];
}
```

- [ ] **Step 7: `database/factories/ServiceIndustryApplicationFactory.php`**

```php
<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServiceIndustryApplication;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceIndustryApplicationFactory extends Factory
{
    protected $model = ServiceIndustryApplication::class;

    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'icon' => fake()->randomElement(['bi-building', 'bi-hospital', 'bi-bank', 'bi-cart']),
            'order' => fake()->numberBetween(0, 10),
        ];
    }
}
```

- [ ] **Step 8: Add `industryApplications()` relation on Service**

```php
public function industryApplications(): HasMany
{
    return $this->hasMany(ServiceIndustryApplication::class)->orderBy('order');
}
```

- [ ] **Step 9: Run migration + test → PASS**

- [ ] **Step 10: Commit**

Commit message: `Create service_industry_applications + translations tables, model, factory`

---

### Task 16: Create `service_industry_use_cases` + translations

**Note:** This child hangs off `service_industry_applications`, not directly off `services`. FK is `service_industry_application_id`.

- [ ] **Step 1: Schema test**

```php
public function test_service_industry_use_cases_tables_exist(): void
{
    $this->assertTrue(Schema::hasTable('service_industry_use_cases'));
    $this->assertTrue(Schema::hasTable('service_industry_use_case_translations'));

    foreach (['id', 'service_industry_application_id', 'order', 'created_at', 'updated_at'] as $column) {
        $this->assertTrue(Schema::hasColumn('service_industry_use_cases', $column));
    }
    foreach (['id', 'service_industry_use_case_id', 'locale', 'text', 'created_at', 'updated_at'] as $column) {
        $this->assertTrue(Schema::hasColumn('service_industry_use_case_translations', $column));
    }
}
```

- [ ] **Step 2: Run test → FAIL**

- [ ] **Step 3: Generate migration** — `./sail artisan make:migration create_service_industry_use_cases_tables`

- [ ] **Step 4: Migration body**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_industry_use_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_industry_application_id')
                ->constrained('service_industry_applications')
                ->cascadeOnDelete();
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index(['service_industry_application_id', 'order']);
        });

        Schema::create('service_industry_use_case_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_industry_use_case_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->text('text');
            $table->timestamps();

            $table->unique(['service_industry_use_case_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_industry_use_case_translations');
        Schema::dropIfExists('service_industry_use_cases');
    }
};
```

- [ ] **Step 5: `app/Models/ServiceIndustryUseCase.php`**

```php
<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceIndustryUseCase extends Model implements TranslatableContract
{
    use HasFactory;
    use Translatable;

    protected $fillable = ['service_industry_application_id', 'order'];

    protected $casts = ['order' => 'integer'];

    public array $translatedAttributes = ['text'];

    public function industryApplication(): BelongsTo
    {
        return $this->belongsTo(ServiceIndustryApplication::class, 'service_industry_application_id');
    }
}
```

- [ ] **Step 6: `app/Models/ServiceIndustryUseCaseTranslation.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceIndustryUseCaseTranslation extends Model
{
    public $timestamps = true;

    protected $fillable = ['text'];
}
```

- [ ] **Step 7: `database/factories/ServiceIndustryUseCaseFactory.php`**

```php
<?php

namespace Database\Factories;

use App\Models\ServiceIndustryApplication;
use App\Models\ServiceIndustryUseCase;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceIndustryUseCaseFactory extends Factory
{
    protected $model = ServiceIndustryUseCase::class;

    public function definition(): array
    {
        return [
            'service_industry_application_id' => ServiceIndustryApplication::factory(),
            'order' => fake()->numberBetween(0, 10),
        ];
    }
}
```

- [ ] **Step 8: No Service model change** (hangs off industry application)

- [ ] **Step 9: Run migration + test → PASS**

- [ ] **Step 10: Commit**

Commit message: `Create service_industry_use_cases + translations tables, model, factory`

---

### Task 17: Create `service_technologies` + translations

- [ ] **Step 1: Schema test**

```php
public function test_service_technologies_tables_exist(): void
{
    $this->assertTrue(Schema::hasTable('service_technologies'));
    $this->assertTrue(Schema::hasTable('service_technology_translations'));

    foreach (['id', 'service_id', 'icon', 'order', 'created_at', 'updated_at'] as $column) {
        $this->assertTrue(Schema::hasColumn('service_technologies', $column));
    }
    foreach (['id', 'service_technology_id', 'locale', 'name', 'created_at', 'updated_at'] as $column) {
        $this->assertTrue(Schema::hasColumn('service_technology_translations', $column));
    }
}
```

- [ ] **Step 2: Run test → FAIL**

- [ ] **Step 3: Generate migration** — `./sail artisan make:migration create_service_technologies_tables`

- [ ] **Step 4: Migration body**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_technologies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('icon')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index(['service_id', 'order']);
        });

        Schema::create('service_technology_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_technology_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('name');
            $table->timestamps();

            $table->unique(['service_technology_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_technology_translations');
        Schema::dropIfExists('service_technologies');
    }
};
```

- [ ] **Step 5: `app/Models/ServiceTechnology.php`**

```php
<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceTechnology extends Model implements TranslatableContract
{
    use HasFactory;
    use Translatable;

    protected $fillable = ['service_id', 'icon', 'order'];

    protected $casts = ['order' => 'integer'];

    public array $translatedAttributes = ['name'];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
```

- [ ] **Step 6: `app/Models/ServiceTechnologyTranslation.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceTechnologyTranslation extends Model
{
    public $timestamps = true;

    protected $fillable = ['name'];
}
```

- [ ] **Step 7: `database/factories/ServiceTechnologyFactory.php`**

```php
<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServiceTechnology;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceTechnologyFactory extends Factory
{
    protected $model = ServiceTechnology::class;

    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'icon' => fake()->randomElement(['bi-code', 'bi-cloud', 'bi-database']),
            'order' => fake()->numberBetween(0, 10),
        ];
    }
}
```

- [ ] **Step 8: Add `technologies()` relation on Service**

```php
public function technologies(): HasMany
{
    return $this->hasMany(ServiceTechnology::class)->orderBy('order');
}
```

- [ ] **Step 9: Run migration + test → PASS**

- [ ] **Step 10: Commit**

Commit message: `Create service_technologies + translations tables, model, factory`

---

### Task 18: Create `service_business_impacts` + translations

- [ ] **Step 1: Schema test**

```php
public function test_service_business_impacts_tables_exist(): void
{
    $this->assertTrue(Schema::hasTable('service_business_impacts'));
    $this->assertTrue(Schema::hasTable('service_business_impact_translations'));

    foreach (['id', 'service_id', 'icon', 'order', 'created_at', 'updated_at'] as $column) {
        $this->assertTrue(Schema::hasColumn('service_business_impacts', $column));
    }
    foreach (['id', 'service_business_impact_id', 'locale', 'title', 'description', 'created_at', 'updated_at'] as $column) {
        $this->assertTrue(Schema::hasColumn('service_business_impact_translations', $column));
    }
}
```

- [ ] **Step 2: Run test → FAIL**

- [ ] **Step 3: Generate migration** — `./sail artisan make:migration create_service_business_impacts_tables`

- [ ] **Step 4: Migration body**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_business_impacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('icon')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index(['service_id', 'order']);
        });

        Schema::create('service_business_impact_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_business_impact_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['service_business_impact_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_business_impact_translations');
        Schema::dropIfExists('service_business_impacts');
    }
};
```

- [ ] **Step 5: `app/Models/ServiceBusinessImpact.php`**

```php
<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceBusinessImpact extends Model implements TranslatableContract
{
    use HasFactory;
    use Translatable;

    protected $fillable = ['service_id', 'icon', 'order'];

    protected $casts = ['order' => 'integer'];

    public array $translatedAttributes = ['title', 'description'];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
```

- [ ] **Step 6: `app/Models/ServiceBusinessImpactTranslation.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceBusinessImpactTranslation extends Model
{
    public $timestamps = true;

    protected $fillable = ['title', 'description'];
}
```

- [ ] **Step 7: `database/factories/ServiceBusinessImpactFactory.php`**

```php
<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServiceBusinessImpact;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceBusinessImpactFactory extends Factory
{
    protected $model = ServiceBusinessImpact::class;

    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'icon' => fake()->randomElement(['bi-graph-up', 'bi-currency-dollar', 'bi-speedometer']),
            'order' => fake()->numberBetween(0, 10),
        ];
    }
}
```

- [ ] **Step 8: Add `businessImpacts()` relation on Service**

```php
public function businessImpacts(): HasMany
{
    return $this->hasMany(ServiceBusinessImpact::class)->orderBy('order');
}
```

- [ ] **Step 9: Run migration + test → PASS**

- [ ] **Step 10: Commit**

Commit message: `Create service_business_impacts + translations tables, model, factory`

---

### Task 19: Create `service_differentiators` + translations

- [ ] **Step 1: Schema test**

```php
public function test_service_differentiators_tables_exist(): void
{
    $this->assertTrue(Schema::hasTable('service_differentiators'));
    $this->assertTrue(Schema::hasTable('service_differentiator_translations'));

    foreach (['id', 'service_id', 'icon', 'order', 'created_at', 'updated_at'] as $column) {
        $this->assertTrue(Schema::hasColumn('service_differentiators', $column));
    }
    foreach (['id', 'service_differentiator_id', 'locale', 'title', 'description', 'created_at', 'updated_at'] as $column) {
        $this->assertTrue(Schema::hasColumn('service_differentiator_translations', $column));
    }
}
```

- [ ] **Step 2: Run test → FAIL**

- [ ] **Step 3: Generate migration** — `./sail artisan make:migration create_service_differentiators_tables`

- [ ] **Step 4: Migration body**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_differentiators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('icon')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index(['service_id', 'order']);
        });

        Schema::create('service_differentiator_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_differentiator_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['service_differentiator_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_differentiator_translations');
        Schema::dropIfExists('service_differentiators');
    }
};
```

- [ ] **Step 5: `app/Models/ServiceDifferentiator.php`**

```php
<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceDifferentiator extends Model implements TranslatableContract
{
    use HasFactory;
    use Translatable;

    protected $fillable = ['service_id', 'icon', 'order'];

    protected $casts = ['order' => 'integer'];

    public array $translatedAttributes = ['title', 'description'];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
```

- [ ] **Step 6: `app/Models/ServiceDifferentiatorTranslation.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceDifferentiatorTranslation extends Model
{
    public $timestamps = true;

    protected $fillable = ['title', 'description'];
}
```

- [ ] **Step 7: `database/factories/ServiceDifferentiatorFactory.php`**

```php
<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\ServiceDifferentiator;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceDifferentiatorFactory extends Factory
{
    protected $model = ServiceDifferentiator::class;

    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'icon' => fake()->randomElement(['bi-star', 'bi-award', 'bi-trophy']),
            'order' => fake()->numberBetween(0, 10),
        ];
    }
}
```

- [ ] **Step 8: Add `differentiators()` relation on Service**

```php
public function differentiators(): HasMany
{
    return $this->hasMany(ServiceDifferentiator::class)->orderBy('order');
}
```

- [ ] **Step 9: Run migration + test → PASS**

- [ ] **Step 10: Commit**

Commit message: `Create service_differentiators + translations tables, model, factory`

---

### Task 20: End-to-end model smoke test

Validate that every new astrotomic model can create, translate, and read rows end-to-end.

**Files:**
- Create: `tests/Feature/Models/ServiceCmsModelsTest.php`

- [ ] **Step 1: Write the test**

Create `tests/Feature/Models/ServiceCmsModelsTest.php`:

```php
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
```

- [ ] **Step 2: Run the test**

```bash
./sail artisan test --filter=ServiceCmsModelsTest
```

Expected: all 5 tests PASS.

- [ ] **Step 3: Run the full test suite to catch regressions**

```bash
./sail artisan test
```

Expected: every previous test still passes. No regressions from the new tables / models.

- [ ] **Step 4: Commit**

```bash
git add tests/Feature/Models/ServiceCmsModelsTest.php
git commit -m "$(cat <<'EOF'
Add end-to-end smoke test for services CMS models

Covers: astrotomic translate+read roundtrip, nested relationships
(capability item → group, industry use case → industry application),
and that Service has all 10 new relations.

Co-Authored-By: Claude Opus 4.6 (1M context) <noreply@anthropic.com>
EOF
)"
```

---

### Task 21: Push branch and open draft PR

**Files:** none

- [ ] **Step 1: Confirm everything is committed**

Run:
```bash
git status
```

Expected: `nothing to commit, working tree clean`.

- [ ] **Step 2: Review the commit log**

Run:
```bash
git log origin/main..HEAD --oneline
```

Expected: ~20 commits, one per task, in logical order.

- [ ] **Step 3: Push the branch**

Run:
```bash
git push -u origin feat/services-cms-schema
```

Expected: branch published to origin.

- [ ] **Step 4: Open a draft PR against main**

Run:
```bash
gh pr create --draft --base main --title "Services CMS — backend schema (Plan A)" --body "$(cat <<'EOF'
## Summary

- Installs `astrotomic/laravel-translatable` alongside the existing spatie package.
- Creates 16 schema migrations: `published_at` on services, `service_translations` (parent fat translation table), `service_feature_translations`, `service_benefit_translations`, and 12 child base+translation table pairs (stats, pain points, delivery items, capability groups, capability items, use cases, approach steps, industry applications, industry use cases, technologies, business impacts, differentiators).
- Creates 27 new Eloquent models — astrotomic base models + plain translation models — matching the design spec.
- Creates 12 factories for the new child entities.
- Adds 10 new relations to `Service` (stats, painPoints, deliveryItems, capabilityGroups, useCases, approachSteps, industryApplications, technologies, businessImpacts, differentiators). Features/benefits relations are unchanged.
- Adds PHPUnit schema assertions for every new table + end-to-end smoke test exercising astrotomic translate+read on every new model.

**Scope boundary:** This is Plan A of the Services CMS rollout. It is purely additive — existing spatie translations on `Service`, `ServiceFeature`, `ServiceBenefit` are untouched. No GraphQL schema changes. No data migration. No trait swap. Those are Plans B, C, and D.

## Test plan

- [ ] `./sail artisan migrate:fresh` completes without error
- [ ] `./sail artisan test --filter=ServiceCmsSchemaTest` — all assertions pass
- [ ] `./sail artisan test --filter=ServiceCmsModelsTest` — smoke test passes
- [ ] `./sail artisan test` — full suite passes, no regressions
- [ ] Staging deploy: migrations run cleanly; existing services queries continue to return data

## Reference

- Spec: `docs/superpowers/specs/2026-04-11-services-cms-design.md`
- Plan: `docs/superpowers/plans/2026-04-11-services-cms-backend-schema.md`

🤖 Generated with [Claude Code](https://claude.com/claude-code)
EOF
)"
```

Expected: PR URL printed.

---

## Self-review checklist

After Task 21 completes, verify:

1. **Schema coverage** — every table listed in the spec's "Full child table list" exists in the DB. Spot-check via `\dt service_*` in psql.
2. **Model coverage** — every model listed in "File Structure → Created" has a file under `app/Models/`.
3. **Factory coverage** — every base child model has a factory.
4. **Relations** — `Service` model has all 10 new relation methods (plus the existing `features()` and `benefits()`).
5. **Existing behavior preserved** — spatie `HasTranslations` trait is still on `Service`, `ServiceFeature`, `ServiceBenefit`. The old JSON columns (`title`, `description`, `short_description`) still exist on `services`. Existing GraphQL queries still work.
6. **No production data touched** — no `DB::table(...)` calls that would modify existing rows in any of this plan's migrations.
7. **Test suite green** — `./sail artisan test` passes end-to-end.

---

## Next plans (for context, not for this PR)

- **Plan B — Data migration + trait swap:** spatie → astrotomic data migration for existing title/description/short_description; commit `database/data/services-content-backfill.json` fixture; data migration that backfills rich content from the fixture; swap `Service`/`ServiceFeature`/`ServiceBenefit` traits from spatie to astrotomic; drop old JSON columns; update `ServiceSeeder.php`.
- **Plan C — GraphQL API:** implement grouped `Service` + `ServiceForAdmin` types, `service(slug:)` resolver with projection, admin CRUD mutations, PHPUnit feature tests, mcp-graphql verification.
- **Plan D — Frontend switch + deploy:** `gamma-web` `useServiceDetail` composable, replace `tm(...)` lookups with GraphQL query, keep i18n fallback until stable, production deploy, cleanup.
