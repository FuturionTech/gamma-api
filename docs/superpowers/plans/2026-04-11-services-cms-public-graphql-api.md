# Services CMS — Public GraphQL API Implementation Plan (Plan C)

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Implement the public GraphQL read API for the services CMS: update `graphql/entities/service.graphql` with grouped section types (hero, challenge, capabilities, industries, etc.), wire up a dedicated PHP resolver that projects the fat `service_translations` columns and the 12 child collections into the tree shape the frontend expects, and add PHPUnit feature tests + mcp-graphql verification.

**Architecture:** One dedicated resolver class (`App\GraphQL\Queries\ServiceDetail`) loads a service by slug with every relation eager-loaded, then projects the flat translation columns into grouped nested arrays. A second resolver (`App\GraphQL\Queries\ServicesList`) handles the simpler listing query. Locale comes from the existing header-based middleware (installed in commit `03e52c1`) — no `locale` argument on public queries. The GraphQL schema layer stays framework-agnostic via Lighthouse; resolvers return PHP arrays that Lighthouse auto-maps to GraphQL types.

**Tech Stack:** Laravel 12, PHP 8.3, PostgreSQL 17, Laravel Sail, nuwave/lighthouse ^6.63, astrotomic/laravel-translatable ^11.17, PHPUnit 11, mcp-graphql MCP server for schema verification.

**Reference spec:** `docs/superpowers/specs/2026-04-11-services-cms-design.md` — phases 5 and 6 (the "Public queries" and "mcp-graphql verification" portions).

**Prior plans:** Plans A and B must be deployed. `Service`, `ServiceFeature`, `ServiceBenefit` are on astrotomic; 6 fixture services + all child collections are populated in the DB.

**Scope boundary:** Plan C covers the PUBLIC read side only. Admin types, admin queries, admin mutations, per-locale publish mutations — all out of scope, deferred to Plan E. The frontend `gamma-web` switch is Plan D.

---

## Prerequisite reading

- `docs/superpowers/specs/2026-04-11-services-cms-design.md` — the full design spec, especially "GraphQL Schema → Public types" and "Public queries"
- Previous plans A and B for context
- `graphql/entities/service.graphql` — the current public service schema (will be rewritten)
- `graphql/schema.graphql` — the root GraphQL schema that imports entity files
- `app/Models/Service.php` — has the 10 new astrotomic-child relations
- `app/GraphQL/Queries/` — existing query resolver classes (if any) — check the layout
- Lighthouse docs: https://lighthouse-php.com/

### How to check the existing schema structure

Run:
```bash
grep -r "extend type Query" graphql/
cat graphql/schema.graphql
ls -la graphql/entities/
```

And inspect `app/GraphQL/` directory layout to see where existing resolver classes live:
```bash
find app/GraphQL -type f -name "*.php"
```

The new resolvers will be placed consistently with that convention.

---

## Architecture reference — the projection pattern

The new public `service(slug: String!)` query returns a grouped tree where each section (hero, challenge, capabilities, etc.) is its own nested object type. But the underlying `service_translations` table has flat columns (`hero_tagline`, `hero_headline`, `challenge_title`, etc.). The resolver's job is to project flat → grouped.

```php
// In the resolver, after loading $service with eager-loaded relations
return [
    'id' => $service->id,
    'slug' => $service->slug,
    'icon' => $service->icon,
    'icon_color' => $service->icon_color,
    'category' => $service->category,
    'is_active' => $service->is_active,
    'title' => $service->title,                   // astrotomic → current locale
    'short_description' => $service->short_description,
    'description' => $service->description,
    'meta_title' => $service->meta_title,
    'meta_description' => $service->meta_description,

    'hero' => [
        'tagline' => $service->hero_tagline,
        'headline' => $service->hero_headline,
        'subheadline' => $service->hero_subheadline,
        'ctaPrimaryLabel' => $service->hero_cta_primary_label,
        'ctaSecondaryLabel' => $service->hero_cta_secondary_label,
        'stats' => $service->stats->map(fn ($stat) => [
            'icon' => $stat->icon,
            'value' => $stat->value,
            'label' => $stat->label,
        ])->all(),
    ],

    'challenge' => [
        'title' => $service->challenge_title,
        'description' => $service->challenge_description,
        'painPoints' => $service->painPoints->map(fn ($p) => ['text' => $p->text])->all(),
    ],

    // ... (every section projected the same way)
];
```

Lighthouse auto-maps each key in the returned array to the matching field on the GraphQL type, recursively. Arrays-of-arrays become lists of objects. For child entities (stats, painPoints, etc.), we call `->map(...)->all()` to convert Eloquent Collections into plain arrays.

### Naming convention

- **Existing Service fields** — `id`, `slug`, `icon`, `icon_color`, `category`, `is_active`, `title`, `description`, `short_description` — stay snake_case to match the existing `graphql/entities/service.graphql` conventions.
- **New nested types** — `ServiceHero`, `ServiceChallenge`, etc. — use camelCase for their fields (`heroTagline` → `tagline`, `heroCtaPrimaryLabel` → `ctaPrimaryLabel`, `painPoints`, `howWeDeliver`, etc.) because:
  1. The spec explicitly uses camelCase for the new types
  2. The frontend already consumes camelCase from the locale JSON files
  3. Keeps the fat-translations-table column names private to the backend
- The mapping from snake_case DB column → camelCase GraphQL field happens in the resolver (the array key IS the GraphQL field name).

### Locale resolution

The existing `SetLocaleFromHeader` middleware (from commit `03e52c1`) reads `X-Locale` or `Accept-Language` and sets `App::setLocale($locale)`. Astrotomic's `use_property_fallback => true` (from `config/translatable.php`, set in Plan A Task 3) means that if a service has no translation for the requested locale, reading `$service->title` transparently returns the fallback locale's value. Verify this middleware is attached to the GraphQL route before starting Task 6.

---

## File Structure

### Created in this plan

**Resolvers:**
- `app/GraphQL/Queries/ServiceDetail.php` — the projection resolver
- `app/GraphQL/Queries/ServicesList.php` — the listing resolver

**Tests:**
- `tests/Feature/GraphQL/ServiceDetailQueryTest.php` — covers the grouped query + locale behavior
- `tests/Feature/GraphQL/ServicesListQueryTest.php` — covers the listing query (may extend or replace the existing `ServiceQueryTest.php`, decide during Task 2)

### Modified in this plan

- `graphql/entities/service.graphql` — REWRITE: add all new nested type definitions; keep the existing `ServiceFeature`, `ServiceBenefit`, `CreateServiceInput`, `UpdateServiceInput`, and mutation definitions UNCHANGED for now (admin side is out of scope)
- `tests/Feature/GraphQL/ServiceQueryTest.php` — if the existing tests conflict with the new `service(slug: ...)` query, update or replace. The existing tests query `services { id title ... }` and `service(id: ID!) { ... }`. We're keeping `services` (listing) and changing `service(id:)` to `service(slug: String!)`. Existing tests that pass an ID will need to switch to a slug OR we keep both signatures (prefer renaming — one public query per purpose).

### Untouched in this plan

- Any admin types, admin queries, admin mutations
- Anything outside `graphql/entities/service.graphql` and `app/GraphQL/`
- gamma-web, gamma-admin

---

## Tasks

### Task 1: Rewrite the public `service.graphql` schema

**Files:**
- Modify: `graphql/entities/service.graphql`

This task rewrites the public Service GraphQL type and adds all 20+ new nested type definitions. It does NOT implement any resolvers yet — the schema file can reference unwritten resolver classes as long as nothing executes those queries.

- [ ] **Step 1: Read the current schema**

Run:
```bash
cat graphql/entities/service.graphql
```

Note the existing:
- `type Service { ... }` definition
- `type ServiceFeature { ... }`, `type ServiceBenefit { ... }`
- `input CreateServiceInput`, `input UpdateServiceInput`
- Any mutations
- Any queries (e.g., `services` and `service(id: ID!)`)

You are going to REWRITE the `type Service` and ADD new nested types. Keep everything else (existing inputs, existing mutations) UNCHANGED in this file — we'll revisit mutations in Plan E.

- [ ] **Step 2: Decide on the query signature change**

The existing query is:
```graphql
extend type Query {
    services(is_active: Boolean, limit: Int): [Service!]! @all
    service(id: ID!): Service @find
}
```

The new design requires `service(slug: String!)` for the detail page (slugs are stable; IDs depend on DB state). Options:

- **Option A**: replace `service(id: ID!)` with `service(slug: String!)`
- **Option B**: keep both, add a second query `serviceBySlug(slug: String!)`

Pick **Option A** — simpler, and the existing tests that use `service(id: 1)` will be updated to use the slug of a seeded service. Reasons:
1. Slugs are the canonical public identifier for services
2. The spec's query examples all use `service(slug: ...)`
3. One query per purpose > two parallel ones

Also rename the existing `@all` + `@find` directives so they use our custom resolver classes instead (we need a custom resolver for the projection logic). The directive change is in Step 3.

- [ ] **Step 3: Write the new schema**

Replace `graphql/entities/service.graphql` with:

```graphql
"A consulting service entity (e.g., AI & Intelligent Systems, Cybersecurity)."
type Service {
    id: ID!
    slug: String!
    icon: String
    icon_color: String
    category: String
    is_active: Boolean!
    order: Int!

    "Translated via header-based locale."
    title: String!
    short_description: String
    description: String
    meta_title: String
    meta_description: String
    meta_keywords: String

    hero: ServiceHero!
    challenge: ServiceChallenge
    howWeDeliver: ServiceHowWeDeliver
    capabilities: ServiceCapabilities
    keyUseCases: ServiceUseCasesSection
    ourApproach: ServiceApproachSection
    industryApplications: ServiceIndustryApplicationsSection
    technologies: ServiceTechnologiesSection
    businessImpact: ServiceBusinessImpactSection
    differentiators: ServiceDifferentiatorsSection
    closing: ServiceClosing!

    features: [ServiceFeature!]!
    benefits: [ServiceBenefit!]!

    created_at: DateTime!
    updated_at: DateTime!
}

type ServiceHero {
    tagline: String
    headline: String
    subheadline: String
    ctaPrimaryLabel: String
    ctaSecondaryLabel: String
    stats: [ServiceStat!]!
}

type ServiceStat {
    icon: String
    value: String!
    label: String!
}

type ServiceChallenge {
    title: String
    description: String
    painPoints: [ServicePainPoint!]!
}

type ServicePainPoint {
    text: String!
}

type ServiceHowWeDeliver {
    title: String
    description: String
    items: [ServiceDeliveryItem!]!
}

type ServiceDeliveryItem {
    icon: String
    text: String!
}

type ServiceCapabilities {
    title: String
    groups: [ServiceCapabilityGroup!]!
}

type ServiceCapabilityGroup {
    icon: String
    name: String!
    items: [ServiceCapabilityItem!]!
}

type ServiceCapabilityItem {
    name: String!
}

type ServiceUseCasesSection {
    title: String
    description: String
    items: [ServiceUseCase!]!
}

type ServiceUseCase {
    text: String!
}

type ServiceApproachSection {
    title: String
    description: String
    items: [ServiceApproachStep!]!
}

type ServiceApproachStep {
    icon: String
    title: String!
    description: String
}

type ServiceIndustryApplicationsSection {
    title: String
    description: String
    industries: [ServiceIndustryApplication!]!
}

type ServiceIndustryApplication {
    icon: String
    name: String!
    description: String
    useCases: [ServiceIndustryUseCase!]!
}

type ServiceIndustryUseCase {
    text: String!
}

type ServiceTechnologiesSection {
    title: String
    description: String
    items: [ServiceTechnology!]!
}

type ServiceTechnology {
    icon: String
    name: String!
}

type ServiceBusinessImpactSection {
    title: String
    description: String
    items: [ServiceBusinessImpactItem!]!
}

type ServiceBusinessImpactItem {
    icon: String
    title: String!
    description: String
}

type ServiceDifferentiatorsSection {
    title: String
    points: [ServiceDifferentiator!]!
}

type ServiceDifferentiator {
    icon: String
    title: String!
    description: String
}

type ServiceClosing {
    title: String
    subtitle: String
}

type ServiceFeature {
    id: ID!
    service_id: ID!
    title: String!
    description: String
    icon: String
    order: Int!
    created_at: DateTime!
    updated_at: DateTime!
    service: Service @belongsTo
}

type ServiceBenefit {
    id: ID!
    service_id: ID!
    title: String!
    description: String
    icon: String
    order: Int!
    created_at: DateTime!
    updated_at: DateTime!
    service: Service @belongsTo
}

extend type Query {
    "Return the list of active, published services (card-level content)."
    services(is_active: Boolean, limit: Int): [Service!]!
        @field(resolver: "App\\GraphQL\\Queries\\ServicesList")

    "Return a single service by slug with the full grouped content tree."
    service(slug: String!): Service
        @field(resolver: "App\\GraphQL\\Queries\\ServiceDetail")
}

input CreateServiceInput {
    title: String! @rules(apply: ["required", "max:255"])
    description: String
    short_description: String
    icon: String
    icon_color: String
    category: String
    slug: String
    order: Int
    is_active: Boolean
}

input UpdateServiceInput {
    title: String @rules(apply: ["max:255"])
    description: String
    short_description: String
    icon: String
    icon_color: String
    category: String
    slug: String
    order: Int
    is_active: Boolean
}
```

Keep any existing mutations (`createService`, `updateService`, `deleteService`, `createServiceFeature`, etc.) as they were — don't touch them in this plan. If they were in this same file, preserve them verbatim below the queries.

NOTES on the schema:

1. **Section wrapper types are named `*Section`** for the sections that have a `title + description + items` pattern (e.g., `ServiceUseCasesSection`, `ServiceApproachSection`). This avoids name collisions with the existing `ServiceUseCase` / `ServiceApproachStep` types that represent the individual items.

2. **`capabilities` and `differentiators` are named `ServiceCapabilities` and `ServiceDifferentiatorsSection`** — not `*Section` on the first one because it only has `title + groups` (no description). The naming is pragmatic: matches the frontend shape.

3. **`hero` and `closing` are NOT nullable** — every service must have them rendered, even if the translation content is empty strings. Nullability reflects the semantic requirement, not whether data exists.

4. **`@field(resolver: "...")`** uses the fully-qualified class name with double-escaped namespace separators (`\\`). Lighthouse parses this correctly.

5. **The top-level `Service.title` field is non-nullable** (`String!`) because every service MUST have a title in at least the fallback locale — astrotomic's `use_property_fallback => true` guarantees this.

6. **The camelCase field names inside nested types** are valid GraphQL — GraphQL spec allows both snake and camel; we're just staying consistent with the frontend's existing data shape.

- [ ] **Step 4: Validate the schema parses**

Run:
```bash
./sail artisan lighthouse:validate-schema
```

Expected: no errors. If Lighthouse complains about a missing type or field, check for typos in the schema.

If `lighthouse:validate-schema` is not available, try:
```bash
./sail artisan lighthouse:print-schema 2>&1 | head -20
```

This will render the schema; any parse error will show up at the top.

Lighthouse MAY error out because the resolver class `App\GraphQL\Queries\ServiceDetail` doesn't exist yet. That's OK — the error will be at field resolution time, not parse time. The parse should still succeed.

- [ ] **Step 5: Commit**

```bash
git add graphql/entities/service.graphql
git commit -m "$(cat <<'EOF'
Update Service GraphQL schema with grouped section types

Rewrites the public Service type to include nested section types
(ServiceHero, ServiceChallenge, ServiceCapabilities, ..., ServiceClosing)
that mirror the frontend rendering tree.

Changes `service(id: ID!)` to `service(slug: String!)` and switches
both queries to custom PHP resolvers (ServicesList, ServiceDetail).
Resolver classes are implemented in the next tasks.

Existing mutations and inputs remain unchanged — admin-side work
is deferred to Plan E.

Part of Plan C of the services CMS rollout.

Co-Authored-By: Claude Opus 4.6 (1M context) <noreply@anthropic.com>
EOF
)"
```

---

### Task 2: Implement the `ServiceDetail` resolver

**Files:**
- Create: `app/GraphQL/Queries/ServiceDetail.php`

- [ ] **Step 1: Inspect the existing resolver directory**

Run:
```bash
ls -la app/GraphQL/Queries/ 2>/dev/null || echo "does not exist yet"
find app/GraphQL -name "*.php" | head
```

If `app/GraphQL/Queries/` doesn't exist, Laravel's autoloader via composer's PSR-4 will still find new files there as long as the namespace matches. Create the directory with `mkdir -p app/GraphQL/Queries` before writing the class.

- [ ] **Step 2: Write the resolver**

Create `app/GraphQL/Queries/ServiceDetail.php`:

```php
<?php

namespace App\GraphQL\Queries;

use App\Models\Service;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

final class ServiceDetail
{
    /**
     * Resolve the `service(slug: String!)` query.
     *
     * Loads a single service by slug with every relation eager-loaded,
     * then projects the flat service_translations columns and child
     * collections into the grouped tree shape the frontend expects.
     *
     * Locale is resolved from the SetLocaleFromHeader middleware,
     * so calling $service->title implicitly returns the active locale
     * via astrotomic's property fallback.
     */
    public function __invoke(mixed $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): ?array
    {
        /** @var Service|null $service */
        $service = Service::query()
            ->with([
                'stats',
                'painPoints',
                'deliveryItems',
                'capabilityGroups.items',
                'useCases',
                'approachSteps',
                'industryApplications.useCases',
                'technologies',
                'businessImpacts',
                'differentiators',
                'features',
                'benefits',
            ])
            ->where('slug', $args['slug'])
            ->where('is_active', true)
            ->first();

        if ($service === null) {
            return null;
        }

        return $this->project($service);
    }

    private function project(Service $service): array
    {
        return [
            'id' => $service->id,
            'slug' => $service->slug,
            'icon' => $service->icon,
            'icon_color' => $service->icon_color,
            'category' => $service->category,
            'is_active' => $service->is_active,
            'order' => $service->order,

            'title' => $service->title,
            'short_description' => $service->short_description,
            'description' => $service->description,
            'meta_title' => $service->meta_title,
            'meta_description' => $service->meta_description,
            'meta_keywords' => $service->meta_keywords,

            'hero' => $this->projectHero($service),
            'challenge' => $this->projectChallenge($service),
            'howWeDeliver' => $this->projectHowWeDeliver($service),
            'capabilities' => $this->projectCapabilities($service),
            'keyUseCases' => $this->projectKeyUseCases($service),
            'ourApproach' => $this->projectOurApproach($service),
            'industryApplications' => $this->projectIndustryApplications($service),
            'technologies' => $this->projectTechnologies($service),
            'businessImpact' => $this->projectBusinessImpact($service),
            'differentiators' => $this->projectDifferentiators($service),
            'closing' => $this->projectClosing($service),

            'features' => $service->features->all(),
            'benefits' => $service->benefits->all(),

            'created_at' => $service->created_at,
            'updated_at' => $service->updated_at,
        ];
    }

    private function projectHero(Service $service): array
    {
        return [
            'tagline' => $service->hero_tagline,
            'headline' => $service->hero_headline,
            'subheadline' => $service->hero_subheadline,
            'ctaPrimaryLabel' => $service->hero_cta_primary_label,
            'ctaSecondaryLabel' => $service->hero_cta_secondary_label,
            'stats' => $service->stats->map(fn ($stat) => [
                'icon' => $stat->icon,
                'value' => $stat->value,
                'label' => $stat->label,
            ])->all(),
        ];
    }

    private function projectChallenge(Service $service): array
    {
        return [
            'title' => $service->challenge_title,
            'description' => $service->challenge_description,
            'painPoints' => $service->painPoints->map(fn ($p) => ['text' => $p->text])->all(),
        ];
    }

    private function projectHowWeDeliver(Service $service): array
    {
        return [
            'title' => $service->delivery_title,
            'description' => $service->delivery_description,
            'items' => $service->deliveryItems->map(fn ($i) => [
                'icon' => $i->icon,
                'text' => $i->text,
            ])->all(),
        ];
    }

    private function projectCapabilities(Service $service): array
    {
        return [
            'title' => $service->capabilities_title,
            'groups' => $service->capabilityGroups->map(fn ($group) => [
                'icon' => $group->icon,
                'name' => $group->name,
                'items' => $group->items->map(fn ($item) => ['name' => $item->name])->all(),
            ])->all(),
        ];
    }

    private function projectKeyUseCases(Service $service): array
    {
        return [
            'title' => $service->use_cases_title,
            'description' => $service->use_cases_description,
            'items' => $service->useCases->map(fn ($uc) => ['text' => $uc->text])->all(),
        ];
    }

    private function projectOurApproach(Service $service): array
    {
        return [
            'title' => $service->approach_title,
            'description' => $service->approach_description,
            'items' => $service->approachSteps->map(fn ($step) => [
                'icon' => $step->icon,
                'title' => $step->title,
                'description' => $step->description,
            ])->all(),
        ];
    }

    private function projectIndustryApplications(Service $service): array
    {
        return [
            'title' => $service->industry_title,
            'description' => $service->industry_description,
            'industries' => $service->industryApplications->map(fn ($ind) => [
                'icon' => $ind->icon,
                'name' => $ind->name,
                'description' => $ind->description,
                'useCases' => $ind->useCases->map(fn ($uc) => ['text' => $uc->text])->all(),
            ])->all(),
        ];
    }

    private function projectTechnologies(Service $service): array
    {
        return [
            'title' => $service->technologies_title,
            'description' => $service->technologies_description,
            'items' => $service->technologies->map(fn ($t) => [
                'icon' => $t->icon,
                'name' => $t->name,
            ])->all(),
        ];
    }

    private function projectBusinessImpact(Service $service): array
    {
        return [
            'title' => $service->business_impact_title,
            'description' => $service->business_impact_description,
            'items' => $service->businessImpacts->map(fn ($b) => [
                'icon' => $b->icon,
                'title' => $b->title,
                'description' => $b->description,
            ])->all(),
        ];
    }

    private function projectDifferentiators(Service $service): array
    {
        return [
            'title' => $service->differentiators_title,
            'points' => $service->differentiators->map(fn ($d) => [
                'icon' => $d->icon,
                'title' => $d->title,
                'description' => $d->description,
            ])->all(),
        ];
    }

    private function projectClosing(Service $service): array
    {
        return [
            'title' => $service->closing_title,
            'subtitle' => $service->closing_subtitle,
        ];
    }
}
```

Key points:
- Eager-loads all 12 relations + features/benefits with one query (no N+1)
- Uses `->with(['capabilityGroups.items', 'industryApplications.useCases'])` for the 2 nested relations
- Every reader (`$service->title`, `$service->hero_tagline`, `$stat->value`, etc.) goes through astrotomic which resolves to the current `App::getLocale()`
- Returns `null` if the service is not found or not active, so Lighthouse returns `{ "service": null }` instead of erroring
- Features and benefits are passed as model arrays — Lighthouse reads their properties directly (since those are ALSO astrotomic-translated, they'll return the current locale)

- [ ] **Step 3: Make Sail refresh its cached namespace**

Run:
```bash
./sail composer dump-autoload
```

Otherwise Laravel may not pick up the new class.

- [ ] **Step 4: Manual smoke test**

Start by ensuring Sail is running and the DB has the fixture-backfilled services. Then:

```bash
curl -s -X POST http://localhost:8880/graphql \
  -H "Content-Type: application/json" \
  -d '{"query":"{ service(slug: \"ai-intelligent-systems\") { id slug title hero { headline stats { value label } } challenge { title painPoints { text } } } }"}' | python3 -m json.tool
```

Expected: a JSON response with the AI service's details, including hero + stats, challenge + pain points.

If the query errors, read the error message — common issues:
- `Unknown field` → schema wasn't saved or Lighthouse cache is stale; run `./sail artisan lighthouse:clear-cache`
- `Null returned for non-nullable field hero.stats` → eager loading didn't work, or the stats relation is empty — check the seed data
- `Class not found` → composer autoload didn't pick up the new class; run `./sail composer dump-autoload`

Iterate until the smoke test returns valid data.

- [ ] **Step 5: Commit**

```bash
git add app/GraphQL/Queries/ServiceDetail.php
git commit -m "$(cat <<'EOF'
Implement ServiceDetail GraphQL resolver

Loads a service by slug with every child relation eager-loaded,
then projects the flat service_translations columns into the
grouped section tree (hero, challenge, capabilities, industries,
etc.) that matches the frontend rendering shape.

Locale comes from the SetLocaleFromHeader middleware — readers
implicitly resolve to App::getLocale() via astrotomic.

Part of Plan C of the services CMS rollout.

Co-Authored-By: Claude Opus 4.6 (1M context) <noreply@anthropic.com>
EOF
)"
```

---

### Task 3: PHPUnit feature tests for `ServiceDetail` query

**Files:**
- Create: `tests/Feature/GraphQL/ServiceDetailQueryTest.php`

- [ ] **Step 1: Write the test class**

Create `tests/Feature/GraphQL/ServiceDetailQueryTest.php`:

```php
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

        app()->setLocale('en');

        $response = $this->postJson('/graphql', [
            'query' => '{
                service(slug: "' . $service->slug . '") {
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

        app()->setLocale('fr');

        $response = $this->postJson('/graphql', [
            'query' => '{
                service(slug: "' . $service->slug . '") {
                    title
                    hero { headline }
                    challenge { title painPoints { text } }
                }
            }',
        ]);

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

        app()->setLocale('fr');

        $response = $this->postJson('/graphql', [
            'query' => '{
                service(slug: "fallback-test") {
                    title
                    hero { headline }
                }
            }',
        ]);

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
        $service = Service::factory()->create([
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
            'slug' => 'test-service-full-' . uniqid(),
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
```

- [ ] **Step 2: Run the test**

```bash
./sail artisan test --filter=ServiceDetailQueryTest
```

Expected: all 5 tests pass. If any fail, the most likely cause is either:
- The schema/resolver has a typo or mismatch
- Astrotomic's fallback isn't returning what the test expects — check that `config/translatable.php` has `use_property_fallback => true`
- A child model is missing or has a naming mismatch

Iterate until all 5 pass.

- [ ] **Step 3: Run the full suite**

```bash
./sail artisan test
```

Expected: at least 39 tests passing (34 prior + 5 new), plus the 3 Plan B skips. Zero new failures.

If `tests/Feature/GraphQL/ServiceQueryTest.php` fails because it uses the old `service(id: ID!)` signature, update it to use `service(slug: ...)`. Example change:

```php
// BEFORE
$response = $this->postJson('/graphql', [
    'query' => '{ service(id: 1) { id title } }',
]);

// AFTER
$service = Service::factory()->create();
$response = $this->postJson('/graphql', [
    'query' => '{ service(slug: "' . $service->slug . '") { id title } }',
]);
```

- [ ] **Step 4: Commit**

```bash
git add tests/Feature/GraphQL/ServiceDetailQueryTest.php tests/Feature/GraphQL/ServiceQueryTest.php
git commit -m "$(cat <<'EOF'
Add ServiceDetailQueryTest + migrate ServiceQueryTest to slug signature

5 new feature tests for the grouped public service query:
- Full tree in English
- Full tree in French (via App::setLocale)
- EN fallback when FR translation is missing
- Null for unknown slug
- Null for inactive service

Also updates the existing ServiceQueryTest to use service(slug:)
instead of service(id:) since Task 1 changed the public signature.

Part of Plan C of the services CMS rollout.

Co-Authored-By: Claude Opus 4.6 (1M context) <noreply@anthropic.com>
EOF
)"
```

(Only add `ServiceQueryTest.php` to the commit if you actually modified it.)

---

### Task 4: Implement the `ServicesList` resolver

**Files:**
- Create: `app/GraphQL/Queries/ServicesList.php`

- [ ] **Step 1: Write the resolver**

Create `app/GraphQL/Queries/ServicesList.php`:

```php
<?php

namespace App\GraphQL\Queries;

use App\Models\Service;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

final class ServicesList
{
    /**
     * Resolve the `services(is_active: Boolean, limit: Int): [Service!]!` query.
     *
     * Returns the card-level listing — does NOT eager-load the rich content
     * relations (hero stats, pain points, capability groups, etc.) because
     * the listing only renders title, short_description, icon, features.
     *
     * If a caller requests a nested section field on a list result, Lighthouse
     * will fall through to the default resolver which reads the model property
     * directly. That works for scalar fields (`title`, `short_description`)
     * but NOT for the nested section types (`hero`, `challenge`, etc.) —
     * those require the ServiceDetail resolver's projection. Callers should
     * use ServiceDetail for the detail page and ServicesList for the listing.
     */
    public function __invoke(mixed $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): array
    {
        $query = Service::query()
            ->with(['features', 'benefits'])
            ->orderBy('order');

        if (array_key_exists('is_active', $args) && $args['is_active'] !== null) {
            $query->where('is_active', (bool) $args['is_active']);
        } else {
            // Default: only active services
            $query->where('is_active', true);
        }

        if (array_key_exists('limit', $args) && $args['limit'] !== null) {
            $query->limit((int) $args['limit']);
        }

        $services = $query->get();

        return $services->map(fn (Service $s) => $this->projectCardLevel($s))->all();
    }

    private function projectCardLevel(Service $service): array
    {
        return [
            'id' => $service->id,
            'slug' => $service->slug,
            'icon' => $service->icon,
            'icon_color' => $service->icon_color,
            'category' => $service->category,
            'is_active' => $service->is_active,
            'order' => $service->order,

            'title' => $service->title,
            'short_description' => $service->short_description,
            'description' => $service->description,
            'meta_title' => $service->meta_title,
            'meta_description' => $service->meta_description,
            'meta_keywords' => $service->meta_keywords,

            // Listing doesn't project the rich sections, but hero + closing are
            // marked non-nullable in the schema. Return empty structures so
            // GraphQL validation passes, with the scalar title fields filled in
            // in case the UI renders them on a card.
            'hero' => [
                'tagline' => $service->hero_tagline,
                'headline' => $service->hero_headline,
                'subheadline' => $service->hero_subheadline,
                'ctaPrimaryLabel' => $service->hero_cta_primary_label,
                'ctaSecondaryLabel' => $service->hero_cta_secondary_label,
                'stats' => [],
            ],
            'challenge' => null,
            'howWeDeliver' => null,
            'capabilities' => null,
            'keyUseCases' => null,
            'ourApproach' => null,
            'industryApplications' => null,
            'technologies' => null,
            'businessImpact' => null,
            'differentiators' => null,
            'closing' => [
                'title' => $service->closing_title,
                'subtitle' => $service->closing_subtitle,
            ],

            'features' => $service->features->all(),
            'benefits' => $service->benefits->all(),

            'created_at' => $service->created_at,
            'updated_at' => $service->updated_at,
        ];
    }
}
```

Key points:
- Only loads `features` and `benefits` relations — avoids loading all 12 child collections for every service in a list
- Returns empty/null placeholders for the rich sections so GraphQL doesn't error if a caller queries them on a list item (nullable fields are OK to return null; the non-nullable `hero` and `closing` get minimal shapes)
- If a caller needs the full detail, they use `service(slug:)` which is what `ServiceDetail` handles

- [ ] **Step 2: Dump autoload**

```bash
./sail composer dump-autoload
```

- [ ] **Step 3: Manual smoke test**

```bash
curl -s -X POST http://localhost:8880/graphql \
  -H "Content-Type: application/json" \
  -d '{"query":"{ services(limit: 3) { id slug title short_description features { id title } } }"}' | python3 -m json.tool
```

Expected: list of 3 services with scalar fields populated. `features` arrays resolved.

- [ ] **Step 4: Commit**

```bash
git add app/GraphQL/Queries/ServicesList.php
git commit -m "$(cat <<'EOF'
Implement ServicesList GraphQL resolver

Listing resolver for the `services` query — returns card-level content
(title, short_description, icon, features, benefits) without eager-loading
the rich content relations. Callers use `service(slug:)` for detail pages.

Part of Plan C of the services CMS rollout.

Co-Authored-By: Claude Opus 4.6 (1M context) <noreply@anthropic.com>
EOF
)"
```

---

### Task 5: PHPUnit feature tests for `ServicesList` query

**Files:**
- Create: `tests/Feature/GraphQL/ServicesListQueryTest.php`

- [ ] **Step 1: Write the test class**

Create `tests/Feature/GraphQL/ServicesListQueryTest.php`:

```php
<?php

namespace Tests\Feature\GraphQL;

use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServicesListQueryTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_active_services_ordered(): void
    {
        $s1 = Service::factory()->create(['order' => 2, 'is_active' => true, 'slug' => 'service-b']);
        $s2 = Service::factory()->create(['order' => 1, 'is_active' => true, 'slug' => 'service-a']);

        $response = $this->postJson('/graphql', [
            'query' => '{ services { id slug order } }',
        ]);

        $response->assertOk();
        $response->assertJsonCount(2, 'data.services');
        $response->assertJsonPath('data.services.0.slug', 'service-a');
        $response->assertJsonPath('data.services.1.slug', 'service-b');
    }

    public function test_filters_out_inactive_services_by_default(): void
    {
        Service::factory()->create(['is_active' => true, 'slug' => 'active-service']);
        Service::factory()->create(['is_active' => false, 'slug' => 'inactive-service']);

        $response = $this->postJson('/graphql', [
            'query' => '{ services { id slug is_active } }',
        ]);

        $response->assertOk();
        $response->assertJsonCount(1, 'data.services');
        $response->assertJsonPath('data.services.0.slug', 'active-service');
    }

    public function test_can_explicitly_query_inactive_services(): void
    {
        Service::factory()->create(['is_active' => true, 'slug' => 'active-service']);
        Service::factory()->create(['is_active' => false, 'slug' => 'inactive-service']);

        $response = $this->postJson('/graphql', [
            'query' => '{ services(is_active: false) { id slug is_active } }',
        ]);

        $response->assertOk();
        $response->assertJsonCount(1, 'data.services');
        $response->assertJsonPath('data.services.0.slug', 'inactive-service');
    }

    public function test_can_limit_results(): void
    {
        Service::factory()->count(5)->create(['is_active' => true]);

        $response = $this->postJson('/graphql', [
            'query' => '{ services(limit: 3) { id } }',
        ]);

        $response->assertOk();
        $response->assertJsonCount(3, 'data.services');
    }

    public function test_returns_empty_array_when_no_services(): void
    {
        $response = $this->postJson('/graphql', [
            'query' => '{ services { id } }',
        ]);

        $response->assertOk();
        $response->assertJsonPath('data.services', []);
    }
}
```

- [ ] **Step 2: Run the test**

```bash
./sail artisan test --filter=ServicesListQueryTest
```

Expected: 5 tests pass.

- [ ] **Step 3: Run the full suite**

```bash
./sail artisan test
```

Expected: at least 44 tests passing (39 + 5 new).

- [ ] **Step 4: Commit**

```bash
git add tests/Feature/GraphQL/ServicesListQueryTest.php
git commit -m "$(cat <<'EOF'
Add ServicesListQueryTest

5 feature tests for the public services listing query:
- Returns active services ordered by `order`
- Filters inactive by default
- Can explicitly query inactive via is_active arg
- Respects limit arg
- Returns empty array when no services exist

Part of Plan C of the services CMS rollout.

Co-Authored-By: Claude Opus 4.6 (1M context) <noreply@anthropic.com>
EOF
)"
```

---

### Task 6: Verify header-based locale middleware works end-to-end

**Files:** none (verification only), possibly `app/Http/Kernel.php` or a middleware file if fix needed

- [ ] **Step 1: Find the existing locale middleware**

The commit `03e52c1 Implement header-based locale resolution` introduced header-based locale. Locate it:

```bash
grep -r "Accept-Language\|X-Locale\|App::setLocale\|setLocale" app/Http/Middleware/ app/Providers/ 2>&1
```

Report what you find:
- Is there a `SetLocaleFromHeader` middleware or similar?
- Where is it registered? (Global in `app/Http/Kernel.php`, route-group specific, or service provider?)
- Is it attached to the GraphQL route group? (Lighthouse routes are usually at `/graphql`.)

- [ ] **Step 2: Check if the middleware is running for GraphQL requests**

```bash
curl -s -X POST http://localhost:8880/graphql \
  -H "Content-Type: application/json" \
  -H "Accept-Language: fr" \
  -d '{"query":"{ service(slug: \"ai-intelligent-systems\") { title hero { headline } } }"}' | python3 -m json.tool
```

If the response has `title: "AI & Intelligent Systems"` (English) instead of `"IA et systèmes intelligents"`, the middleware is not triggering. Investigate.

```bash
curl -s -X POST http://localhost:8880/graphql \
  -H "Content-Type: application/json" \
  -H "Accept-Language: en" \
  -d '{"query":"{ service(slug: \"ai-intelligent-systems\") { title } }"}' | python3 -m json.tool
```

Both should return the appropriate localized titles.

- [ ] **Step 3: Fix if broken**

If the middleware isn't wired up for GraphQL routes, investigate:

Option A — it exists but isn't on the GraphQL route group:
- Find `config/lighthouse.php`
- Locate the `route.middleware` array (typically `'web'` or `'api'` with Lighthouse's own)
- Add the locale middleware class name to that array, OR add it to the global `api` middleware group in `app/Http/Kernel.php`

Option B — it doesn't exist at all (commit might have been only partial):
- Create `app/Http/Middleware/SetLocaleFromHeader.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromHeader
{
    public function handle(Request $request, Closure $next): Response
    {
        $supported = config('translatable.locales', ['en']);
        $fallback = config('translatable.fallback_locale', 'en');

        $locale = $request->header('X-Locale')
            ?? $this->parseAcceptLanguage($request->header('Accept-Language'))
            ?? $fallback;

        if (! in_array($locale, $supported, true)) {
            $locale = $fallback;
        }

        app()->setLocale($locale);

        return $next($request);
    }

    private function parseAcceptLanguage(?string $header): ?string
    {
        if (! $header) {
            return null;
        }

        // Simple parse: first language tag, first 2 chars
        $parts = explode(',', $header);
        $first = trim(explode(';', $parts[0])[0]);
        if (str_contains($first, '-')) {
            $first = explode('-', $first)[0];
        }
        return strtolower($first) ?: null;
    }
}
```

Register it — preferred approach is to add to the Lighthouse route group middleware in `config/lighthouse.php`:
```php
'route' => [
    'middleware' => [
        \App\Http\Middleware\SetLocaleFromHeader::class,
        // existing middleware
    ],
],
```

- [ ] **Step 4: Re-test**

After ensuring the middleware is in place:

```bash
curl -s -X POST http://localhost:8880/graphql \
  -H "Content-Type: application/json" \
  -H "Accept-Language: fr" \
  -d '{"query":"{ service(slug: \"ai-intelligent-systems\") { title } }"}'

curl -s -X POST http://localhost:8880/graphql \
  -H "Content-Type: application/json" \
  -H "Accept-Language: en" \
  -d '{"query":"{ service(slug: \"ai-intelligent-systems\") { title } }"}'
```

Expected: the two responses return FR and EN titles respectively.

- [ ] **Step 5: Write a middleware integration test**

Create `tests/Feature/GraphQL/LocaleHeaderTest.php`:

```php
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
}
```

- [ ] **Step 6: Run the locale test**

```bash
./sail artisan test --filter=LocaleHeaderTest
```

Expected: both tests pass.

- [ ] **Step 7: Run full suite**

```bash
./sail artisan test
```

Expected: at least 46 tests passing.

- [ ] **Step 8: Commit**

If you modified any files (middleware, config, kernel):

```bash
git add <files>
git commit -m "$(cat <<'EOF'
Verify and test header-based locale resolution for GraphQL

Adds LocaleHeaderTest covering:
- Accept-Language header switches the response locale
- X-Locale header takes precedence over Accept-Language

If the SetLocaleFromHeader middleware needed to be created or fixed,
that change is included here.

Part of Plan C of the services CMS rollout.

Co-Authored-By: Claude Opus 4.6 (1M context) <noreply@anthropic.com>
EOF
)"
```

If nothing needed fixing (the middleware already works from commit 03e52c1), the commit is just the new test file.

---

### Task 7: mcp-graphql verification

**Files:** none (verification only)

This task uses the `mcp-graphql` MCP server (available in the workspace) to introspect the running API schema and confirm every new type is exposed.

- [ ] **Step 1: Check that mcp-graphql is available**

The mcp-graphql server is configured for the gamma workspace. From within this task, the controlling Claude should invoke `mcp-graphql`'s introspection tool against `http://localhost:8880/graphql`.

If the MCP server is unavailable, fall back to:

```bash
curl -s -X POST http://localhost:8880/graphql \
  -H "Content-Type: application/json" \
  -d '{"query":"{ __schema { types { name } } }"}' | python3 -c "
import json, sys
data = json.load(sys.stdin)
types = [t['name'] for t in data['data']['__schema']['types']]
expected = [
    'Service', 'ServiceHero', 'ServiceStat', 'ServiceChallenge', 'ServicePainPoint',
    'ServiceHowWeDeliver', 'ServiceDeliveryItem', 'ServiceCapabilities',
    'ServiceCapabilityGroup', 'ServiceCapabilityItem', 'ServiceUseCasesSection',
    'ServiceUseCase', 'ServiceApproachSection', 'ServiceApproachStep',
    'ServiceIndustryApplicationsSection', 'ServiceIndustryApplication', 'ServiceIndustryUseCase',
    'ServiceTechnologiesSection', 'ServiceTechnology', 'ServiceBusinessImpactSection',
    'ServiceBusinessImpactItem', 'ServiceDifferentiatorsSection', 'ServiceDifferentiator',
    'ServiceClosing', 'ServiceFeature', 'ServiceBenefit',
]
missing = [t for t in expected if t not in types]
present = [t for t in expected if t in types]
print(f'Present: {len(present)}/{len(expected)}')
if missing:
    print(f'Missing: {missing}')
else:
    print('All expected types present.')
"
```

Expected: "All expected types present."

- [ ] **Step 2: Verify each seeded production service resolves correctly**

Run the detail query against each of the 6 seeded slugs:

```bash
for slug in ai-intelligent-systems data-engineering-platforms cloud-strategy-engineering cybersecurity business-intelligence big-data; do
  echo "=== $slug ==="
  curl -s -X POST http://localhost:8880/graphql \
    -H "Content-Type: application/json" \
    -d "{\"query\":\"{ service(slug: \\\"$slug\\\") { id slug title hero { headline } challenge { title } closing { title } } }\"}" \
    | python3 -c "import json, sys; d = json.load(sys.stdin); s = d['data'].get('service'); print('  title:', s['title'] if s else 'NULL'); print('  hero.headline:', s['hero']['headline'] if s else 'NULL')"
done
```

Expected: all 6 services return populated titles + hero headlines.

- [ ] **Step 3: Commit (if anything was written)**

This task has no code changes — it's verification only. Skip commit if nothing changed.

If you wrote a verification script, commit it to `docs/verification/plan-c-verification.sh` or similar.

---

### Task 8: Final verification + push

**Files:** none (git operation)

- [ ] **Step 1: Run the full test suite**

```bash
./sail artisan test
```

Expected: all tests pass (including the existing Plan A + Plan B tests + the 11 new Plan C tests). Total should be 45+.

- [ ] **Step 2: Review commit log**

```bash
git log origin/main..HEAD --oneline
```

Expected: 5–7 new commits on main covering the 7 tasks (some tasks may not have produced a commit, like Task 7).

- [ ] **Step 3: Push**

```bash
git push origin main
```

Expected: all commits pushed.

---

## Self-review checklist

After Task 8:

1. **Schema state** — `graphql/entities/service.graphql` contains all 26+ types; `service` query takes `slug: String!` with the custom resolver directive.
2. **Resolver state** — both `ServiceDetail.php` and `ServicesList.php` exist in `app/GraphQL/Queries/`, eager-load their relations, and project into the expected shapes.
3. **Locale middleware** — `Accept-Language` and `X-Locale` both switch the response locale. Tested via `LocaleHeaderTest`.
4. **Test coverage** — `ServiceDetailQueryTest` (5), `ServicesListQueryTest` (5), `LocaleHeaderTest` (2) all passing. Existing `ServiceQueryTest` updated to use slug signature.
5. **Live verification** — all 6 production service slugs return populated grouped trees in both EN and FR.

## Rollback considerations

If something in Plan C breaks the public API:
- **Resolver errors**: roll back the resolver class files (`git revert` their commits). The schema still validates because it references the class names, but Lighthouse will error at request time. Fix forward rather than roll back.
- **Schema changes**: roll back `graphql/entities/service.graphql` to restore the old `@all` / `@find` directives. The resolvers become dead code but the old queries resume working.
- **Locale middleware**: if the middleware change breaks other routes, revert that commit first.

## Next plans (context)

- **Plan D — Frontend switch**: gamma-web `useServiceDetail` composable, replace `tm()` lookups with the new GraphQL query, keep i18n fallback until stable.
- **Plan E — Admin GraphQL API**: admin types (`ServiceForAdmin`, `ServiceTranslation`, `ServiceStatForAdmin`, etc.), admin queries with Sanctum guard, CRUD + reorder mutations for every entity type.
- **Plan F — Cleanup**: remove i18n fallback from gamma-web, delete dead `services.details.items` from locale JSON, remove obsolete `services.{cardKey}` blocks.
