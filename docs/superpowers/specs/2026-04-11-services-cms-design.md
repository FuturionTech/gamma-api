---
date: 2026-04-11
status: approved
topic: Services CMS — admin-managed, multilingual, API-driven
author: Abdoul Compaore (with Claude)
---

# Services CMS Design

## Context

`gamma-web` renders each service detail page from eleven content sections — hero, challenge, how-we-deliver, capabilities, key use cases, our approach, industry applications, technologies, business impact, differentiators, closing CTA — entirely from hardcoded i18n JSON under `services.details.items` in `locales/en.json` and `locales/fr.json`.

`gamma-api` only models five translatable fields on a service (`title`, `description`, `short_description`) plus two child tables (`service_features`, `service_benefits`), all using `spatie/laravel-translatable`'s JSON-column approach. The gap between what the frontend renders and what the backend manages is large.

Every content change today requires a hand edit to JSON files plus a `gamma-web` redeploy. That is slow, unsafe for non-technical admins, and cannot scale past two languages.

## Goals

- Admins (via `gamma-admin`) can create, edit, reorder, and delete every piece of service detail content via GraphQL mutations.
- Content is translatable without schema changes when adding new languages (es, de, pt, …).
- Public GraphQL queries return the exact tree shape `gamma-web` already expects, so the Nuxt rendering logic only swaps its data source (i18n → GraphQL) and nothing visible changes on the client.
- Rollout is production-safe: no seeders run on production, the existing i18n fallback stays in place until the API is verified stable.
- Draft / publish workflow, per-locale publish supported.

## Non-goals

- Solutions, Industries, Blog, Team, FAQ. Same pattern may apply later; see Future Work.
- Rich-text editor or page-builder UX. Fields are plain text + Bootstrap icon classes + ordered lists.
- Media library (hero images, galleries, uploads). Services use Bootstrap icons; image fields can be added later.
- Content versioning / revision history.
- Approval workflows (reviewer → publisher).
- Full-text search across translated content.

## Constraints

1. **Frontend rendering must stay identical.** No visual regression on `gamma-web` at any phase of rollout.
2. **No seeders in production.** Content backfill runs as a one-shot data migration tracked in the `migrations` table.
3. **Public API is header-based for locale.** Client sets `X-Locale` (or `Accept-Language`); middleware reads it. No `locale` argument on public queries.
4. **Admin API exposes all locales at once.** Admin queries return a `translations` array for each entity, ignoring the locale header.
5. **Sanctum guard on every admin mutation.**
6. Existing `ServiceSeeder.php` stays usable for local `migrate:fresh --seed`.

## Architecture Decisions

### AD-1 — Services-only scope

Build a services-specific schema. Do NOT generalize into a shared polymorphic content system yet.

**Rationale:** We don't yet know whether Solutions, Industries, and Blog will share the same section structure. Designing a polymorphic system now bakes in untested assumptions. After Services ships, if Solutions turns out to have the same shape, we extract a shared pattern. Rule of thumb: don't generalize until the third entity.

**Reversibility:** Medium. Extracting a shared pattern later is a refactor, not a rewrite.

### AD-2 — Full CMS (every section and every item is editable)

Model every section and every nested item as first-class database rows, not JSON blobs.

**Rationale:** User explicitly chose this over Partial CMS and Blocks-Based CMS. Full control for admins, strongly-typed GraphQL, clean admin forms, easy validation.

**Reversibility:** Low. This is the shape of the system.

### AD-3 — `astrotomic/laravel-translatable` (translation tables), migrating off spatie JSON

Add `astrotomic/laravel-translatable` and migrate `Service`, `ServiceFeature`, `ServiceBenefit` from `spatie/laravel-translatable` to `astrotomic`.

**Rationale:**

- Long-form content (challenge narratives, capability descriptions) does not bloat parent rows.
- Per-locale validation is native via FormRequest rules (`translations.fr.title => nullable`).
- Per-locale publishing is a natural fit (publish EN, add FR later, admins never blocked).
- "Missing translation" queries are a normal `LEFT JOIN`, indexable.
- Adding new languages = inserting rows. No JSON bloat, no functional indexes.

**Reversibility:** Medium. Switching back would require another data migration but is possible.

**Caveat:** Solutions and Industries also use `spatie/laravel-translatable` today. They stay on spatie until they are migrated in a later spec. The `spatie/laravel-translatable` package is NOT removed from `composer.json` in this spec.

### AD-4 — Fat `service_translations` table for 1-1 fields, per-thing child tables for repeats

Fields that are 1-1 with a service (hero copy, section titles, SEO meta) live as columns on `service_translations`. Fields that repeat (stats, pain points, capability groups, approach steps, etc.) get their own base + translation table pair.

**Rationale:** Eleven separate `service_<section>_translations` tables would require eleven joins for zero gain, because every field in them is 1-1 with the service. The fat translations table is simpler to read, simpler to update, and simpler to query.

### AD-5 — Dedicated PHP resolver for `service(slug:)`

Implement the public detail query via `App\GraphQL\Queries\ServiceDetail`, not pure Lighthouse directives.

**Rationale:** The flat columns on `service_translations` need to be projected into grouped sections (`hero { tagline ... }`, `challenge { title ... }`) before returning. That projection is real PHP logic, not something directives express cleanly. Child relations (stats, painPoints, capabilityGroups…) still use Lighthouse `@hasMany` with type-level resolvers that call `->translate($locale)`.

## Database Schema

### Modified tables

**`services`** (existing — modified)

- Keep: `id`, `application_id`, `slug`, `icon`, `icon_color`, `category`, `order`, `is_active`, `created_at`, `updated_at`
- **Drop** (in a later phase, after data is backfilled): JSON columns `title`, `description`, `short_description`
- **Add**: `published_at` timestamp nullable — service-level draft/publish gate

### New parent translation table

**`service_translations`** (new)

| Column | Type | Notes |
|---|---|---|
| `id` | bigint PK | |
| `service_id` | bigint FK (cascade) | |
| `locale` | varchar(5) | |
| `title` | varchar(255) | card / page title |
| `short_description` | varchar(500) nullable | card subtitle |
| `description` | text nullable | listing description |
| `meta_title` | varchar(255) nullable | SEO |
| `meta_description` | varchar(500) nullable | SEO |
| `meta_keywords` | varchar(500) nullable | SEO |
| `hero_tagline` | varchar(255) nullable | |
| `hero_headline` | varchar(500) nullable | |
| `hero_subheadline` | text nullable | |
| `hero_cta_primary_label` | varchar(100) nullable | |
| `hero_cta_secondary_label` | varchar(100) nullable | |
| `challenge_title` | varchar(255) nullable | |
| `challenge_description` | text nullable | |
| `delivery_title` | varchar(255) nullable | how we deliver |
| `delivery_description` | text nullable | |
| `capabilities_title` | varchar(255) nullable | |
| `use_cases_title` | varchar(255) nullable | |
| `use_cases_description` | text nullable | |
| `approach_title` | varchar(255) nullable | |
| `approach_description` | text nullable | |
| `industry_title` | varchar(255) nullable | industry applications |
| `industry_description` | text nullable | |
| `technologies_title` | varchar(255) nullable | |
| `technologies_description` | text nullable | |
| `business_impact_title` | varchar(255) nullable | |
| `business_impact_description` | text nullable | |
| `differentiators_title` | varchar(255) nullable | |
| `closing_title` | varchar(255) nullable | |
| `closing_subtitle` | text nullable | |
| `published_at` | timestamp nullable | per-locale publish |
| `created_at`, `updated_at` | timestamp | |

Unique index on `(service_id, locale)`.

### Child table pattern

All repeating content follows the same shape:

- **Base table** `service_<thing>` — `id`, `service_id` (or parent child id) FK cascade, `order` int, optional `icon` / `color`, timestamps.
- **Translation table** `service_<thing>_translations` — `id`, `<thing>_id` FK cascade, `locale`, translatable text columns, timestamps. Unique on `(<thing>_id, locale)`.

### Full child table list

| # | Base table | Base columns (beyond id, timestamps) | Translation table | Translatable fields |
|---|---|---|---|---|
| 1 | `service_stats` | `service_id, order, icon` | `service_stat_translations` | `value, label` |
| 2 | `service_pain_points` | `service_id, order` | `service_pain_point_translations` | `text` |
| 3 | `service_delivery_items` | `service_id, order, icon` | `service_delivery_item_translations` | `text` |
| 4 | `service_capability_groups` | `service_id, order, icon` | `service_capability_group_translations` | `name` |
| 5 | `service_capability_items` | `capability_group_id, order` | `service_capability_item_translations` | `name` |
| 6 | `service_use_cases` | `service_id, order` | `service_use_case_translations` | `text` |
| 7 | `service_approach_steps` | `service_id, order, icon` | `service_approach_step_translations` | `title, description` |
| 8 | `service_industry_applications` | `service_id, order, icon` | `service_industry_application_translations` | `name, description` |
| 9 | `service_industry_use_cases` | `industry_application_id, order` | `service_industry_use_case_translations` | `text` |
| 10 | `service_technologies` | `service_id, order, icon` | `service_technology_translations` | `name` |
| 11 | `service_business_impacts` | `service_id, order, icon` | `service_business_impact_translations` | `title, description` |
| 12 | `service_differentiators` | `service_id, order, icon` | `service_differentiator_translations` | `title, description` |

**Two nested relationships:**

- `service_capability_items.capability_group_id` → `service_capability_groups.id`
- `service_industry_use_cases.industry_application_id` → `service_industry_applications.id`

**Plus migrate existing children off spatie JSON:**

- `service_features` (existing base: `service_id, icon, order`) → new `service_feature_translations` (`feature_id, locale, title, description`)
- `service_benefits` (existing base: `service_id, icon, order`) → new `service_benefit_translations` (`benefit_id, locale, title, description`)

### Total migration count

- 1 modification to `services` (add `published_at`)
- 1 new parent translation table (`service_translations`)
- 24 new child tables (12 base + 12 translation)
- 2 new translation tables for existing features / benefits
- Later phase: drop JSON columns on `services`, `service_features`, `service_benefits` (1 migration)

Estimate: ~28–30 migration files. The child pattern is highly repetitive; a shared migration helper or generator is worth it.

## GraphQL Schema

### Public types

```graphql
type Service {
  id: ID!
  slug: String!
  icon: String
  iconColor: String
  category: String

  title: String!
  shortDescription: String
  description: String
  metaTitle: String
  metaDescription: String
  metaKeywords: String

  hero: ServiceHero!
  challenge: ServiceChallenge
  howWeDeliver: ServiceHowWeDeliver
  capabilities: ServiceCapabilities
  keyUseCases: ServiceUseCases
  ourApproach: ServiceApproach
  industryApplications: ServiceIndustryApplications
  technologies: ServiceTechnologies
  businessImpact: ServiceBusinessImpact
  differentiators: ServiceDifferentiators
  closing: ServiceClosing!

  features: [ServiceFeature!]!
  benefits: [ServiceBenefit!]!
}

type ServiceHero {
  tagline: String
  headline: String!
  subheadline: String
  ctaPrimaryLabel: String
  ctaSecondaryLabel: String
  stats: [ServiceStat!]!
}

type ServiceStat { icon: String, value: String!, label: String! }

type ServiceChallenge {
  title: String!
  description: String
  painPoints: [ServicePainPoint!]!
}
type ServicePainPoint { text: String! }

type ServiceHowWeDeliver {
  title: String!
  description: String
  items: [ServiceDeliveryItem!]!
}
type ServiceDeliveryItem { icon: String, text: String! }

type ServiceCapabilities {
  title: String!
  groups: [ServiceCapabilityGroup!]!
}
type ServiceCapabilityGroup {
  icon: String
  name: String!
  items: [ServiceCapabilityItem!]!
}
type ServiceCapabilityItem { name: String! }

type ServiceUseCases {
  title: String!
  description: String
  items: [ServiceUseCase!]!
}
type ServiceUseCase { text: String! }

type ServiceApproach {
  title: String!
  description: String
  items: [ServiceApproachStep!]!
}
type ServiceApproachStep { icon: String, title: String!, description: String }

type ServiceIndustryApplications {
  title: String!
  description: String
  industries: [ServiceIndustryApplication!]!
}
type ServiceIndustryApplication {
  icon: String
  name: String!
  description: String
  useCases: [ServiceIndustryUseCase!]!
}
type ServiceIndustryUseCase { text: String! }

type ServiceTechnologies {
  title: String!
  description: String
  items: [ServiceTechnology!]!
}
type ServiceTechnology { icon: String, name: String! }

type ServiceBusinessImpact {
  title: String!
  description: String
  items: [ServiceBusinessImpactItem!]!
}
type ServiceBusinessImpactItem { icon: String, title: String!, description: String }

type ServiceDifferentiators {
  title: String!
  points: [ServiceDifferentiator!]!
}
type ServiceDifferentiator { icon: String, title: String!, description: String }

type ServiceClosing { title: String!, subtitle: String }

type ServiceFeature { icon: String, title: String!, description: String }
type ServiceBenefit { icon: String, title: String!, description: String }
```

**Locale:** every scalar field on a translated entity is resolved via `->translate($locale, fallback: true)`. Locale comes from middleware — not from query arguments.

### Public queries

```graphql
extend type Query {
  services(isActive: Boolean, limit: Int): [Service!]!
    @field(resolver: "App\\GraphQL\\Queries\\ServicesList")
  service(slug: String!): Service
    @field(resolver: "App\\GraphQL\\Queries\\ServiceDetail")
}
```

`ServicesList` eager-loads active-locale translations + features + benefits. `ServiceDetail` eager-loads every relation in one query, then projects the fat translations table into grouped sections.

Both filter to `is_active = true` and `published_at <= now()` on both the service and the active-locale translation.

### Admin types

Separate types that expose every translation instead of the active locale.

```graphql
type ServiceForAdmin {
  id: ID!
  slug: String!
  icon: String
  iconColor: String
  category: String
  order: Int!
  isActive: Boolean!
  publishedAt: DateTime
  translations: [ServiceTranslation!]!

  stats: [ServiceStatForAdmin!]!
  painPoints: [ServicePainPointForAdmin!]!
  deliveryItems: [ServiceDeliveryItemForAdmin!]!
  capabilityGroups: [ServiceCapabilityGroupForAdmin!]!
  useCases: [ServiceUseCaseForAdmin!]!
  approachSteps: [ServiceApproachStepForAdmin!]!
  industryApplications: [ServiceIndustryApplicationForAdmin!]!
  technologies: [ServiceTechnologyForAdmin!]!
  businessImpacts: [ServiceBusinessImpactForAdmin!]!
  differentiators: [ServiceDifferentiatorForAdmin!]!

  features: [ServiceFeatureForAdmin!]!
  benefits: [ServiceBenefitForAdmin!]!
}

type ServiceTranslation {
  locale: String!
  title: String
  shortDescription: String
  description: String
  metaTitle: String
  metaDescription: String
  metaKeywords: String
  heroTagline: String
  heroHeadline: String
  heroSubheadline: String
  heroCtaPrimaryLabel: String
  heroCtaSecondaryLabel: String
  challengeTitle: String
  challengeDescription: String
  deliveryTitle: String
  deliveryDescription: String
  capabilitiesTitle: String
  useCasesTitle: String
  useCasesDescription: String
  approachTitle: String
  approachDescription: String
  industryTitle: String
  industryDescription: String
  technologiesTitle: String
  technologiesDescription: String
  businessImpactTitle: String
  businessImpactDescription: String
  differentiatorsTitle: String
  closingTitle: String
  closingSubtitle: String
  publishedAt: DateTime
}

type ServiceStatForAdmin {
  id: ID!
  order: Int!
  icon: String
  translations: [ServiceStatTranslation!]!
}
type ServiceStatTranslation { locale: String!, value: String!, label: String! }

# ... same shape (<Type>ForAdmin + <Type>Translation) for every child ...
```

### Admin queries

```graphql
extend type Query {
  serviceForAdmin(id: ID!): ServiceForAdmin @guard(with: ["sanctum"])
  servicesForAdmin(isActive: Boolean): [ServiceForAdmin!]! @guard(with: ["sanctum"])
}
```

### Admin mutations

Service root:

```graphql
extend type Mutation {
  createService(input: CreateServiceInput!): ServiceForAdmin @guard(with: ["sanctum"])
  updateService(id: ID!, input: UpdateServiceInput!): ServiceForAdmin @guard(with: ["sanctum"])
  deleteService(id: ID!): Boolean @guard(with: ["sanctum"])
  publishService(id: ID!): ServiceForAdmin @guard(with: ["sanctum"])
  unpublishService(id: ID!): ServiceForAdmin @guard(with: ["sanctum"])
  publishServiceTranslation(serviceId: ID!, locale: String!): ServiceTranslation @guard(with: ["sanctum"])
  unpublishServiceTranslation(serviceId: ID!, locale: String!): ServiceTranslation @guard(with: ["sanctum"])
}
```

Child pattern (example for stats — repeated for every child type):

```graphql
extend type Mutation {
  createServiceStat(serviceId: ID!, input: CreateServiceStatInput!): ServiceStatForAdmin
    @guard(with: ["sanctum"])
  updateServiceStat(id: ID!, input: UpdateServiceStatInput!): ServiceStatForAdmin
    @guard(with: ["sanctum"])
  deleteServiceStat(id: ID!): Boolean @guard(with: ["sanctum"])
  reorderServiceStats(serviceId: ID!, orderedIds: [ID!]!): [ServiceStatForAdmin!]!
    @guard(with: ["sanctum"])
}
```

Inputs take translations as a nested array:

```graphql
input UpdateServiceStatInput {
  order: Int
  icon: String
  translations: [ServiceStatTranslationInput!]
}
input ServiceStatTranslationInput {
  locale: String!
  value: String!
  label: String!
}
```

Every admin mutation routes through a dedicated resolver class (`App\GraphQL\Mutations\Admin\*`), validates the nested `translations` array per locale via a FormRequest or in-resolver validation, and wraps the write in a transaction.

## Locale Resolution

### Public middleware

New file: `app/Http/Middleware/SetLocaleFromHeader.php`.

- Reads `X-Locale` first, falls back to `Accept-Language`.
- Validates against the supported locale list (`config('translatable.locales')` → `['en', 'fr', …]`).
- Falls back to the default locale (`en`) on missing / unsupported.
- Calls `App::setLocale($locale)`.
- Registered on the GraphQL route group so it runs before Lighthouse resolvers.

### Public resolvers

Read `App::getLocale()` implicitly via astrotomic. Scalar fields on translated entities resolve through `->translate($locale, fallback: true)`.

### Admin resolvers

Ignore the locale header entirely. Return every `translations` row as a nested array. The admin frontend decides which locale to display.

## Migration Strategy (Phased Rollout)

The hard requirement: **`gamma-web` must keep rendering identical content at every step.** No black hole during rollout.

### Phase 0 — branch

Create `feat/services-cms` from `origin/main` in `gamma-api`. Matching branch name in `gamma-web` when Phase 6 starts.

### Phase 1 — install package + schema migrations (non-breaking)

1. `./sail composer require astrotomic/laravel-translatable`.
2. Publish config: `./sail artisan vendor:publish --tag=translatable`.
3. Add the supported-locales list to the config (`['en', 'fr']` for now).
4. Write migrations:
   - Add `published_at` to `services`.
   - Create `service_translations`.
   - Create all 24 new child tables (12 base + 12 translation).
   - Create `service_feature_translations` and `service_benefit_translations` for the existing features / benefits.
5. Run `./sail artisan migrate` locally. Verify schema.

**At end of Phase 1:** DB has new tables, nothing reads from them, spatie JSON columns still intact. `gamma-web` still reads from i18n. Everything keeps working.

### Phase 2 — spatie → astrotomic backfill for existing data

Data migration: `2026_XX_XX_migrate_services_spatie_to_astrotomic.php`.

- Reads existing spatie JSON from `services.title / description / short_description`, `service_features.title / description`, `service_benefits.title / description`.
- For each row and each locale present in the JSON, inserts a row into the matching `*_translations` table.
- Uses `updateOrCreate` so re-running is safe.
- Wrapped in a DB transaction.

**At end of Phase 2:** existing production content exists in both spatie JSON columns AND astrotomic translation tables (dual state). Still nothing reads from astrotomic yet.

### Phase 3 — fixture backfill for new rich content

3a. Create `database/data/services-content-backfill.json` — committed to the repo. Shape:

```json
{
  "locales": ["en", "fr"],
  "services": [
    {
      "slug": "strategy-consulting",
      "icon": "bi-compass",
      "iconColor": "primary",
      "category": "advisory",
      "order": 1,
      "translations": {
        "en": {
          "title": "Strategy Consulting",
          "shortDescription": "...",
          "description": "...",
          "metaTitle": "...",
          "metaDescription": "...",
          "hero": { "tagline": "...", "headline": "...", "subheadline": "...", "ctaPrimaryLabel": "...", "ctaSecondaryLabel": "..." },
          "challenge": { "title": "...", "description": "...", "painPoints": ["...", "..."] },
          "howWeDeliver": { "title": "...", "description": "...", "items": [{ "icon": "bi-check", "text": "..." }] },
          "capabilities": { "title": "...", "groups": [{ "icon": "bi-gear", "name": "...", "items": ["...", "..."] }] },
          "keyUseCases": { "title": "...", "description": "...", "items": ["..."] },
          "ourApproach": { "title": "...", "description": "...", "items": [{ "icon": "bi-1-circle", "title": "...", "description": "..." }] },
          "industryApplications": { "title": "...", "description": "...", "industries": [{ "icon": "bi-building", "name": "...", "description": "...", "useCases": ["..."] }] },
          "technologies": { "title": "...", "description": "...", "items": [{ "icon": "bi-code", "name": "..." }] },
          "businessImpact": { "title": "...", "description": "...", "items": [{ "icon": "bi-graph-up", "title": "...", "description": "..." }] },
          "differentiators": { "title": "...", "points": [{ "icon": "bi-star", "title": "...", "description": "..." }] },
          "closing": { "title": "...", "subtitle": "..." },
          "stats": [{ "icon": "bi-people", "value": "...", "label": "..." }]
        },
        "fr": { /* same shape */ }
      }
    }
  ]
}
```

Content comes from `gamma-web/locales/en.json` and `fr.json` under `services.details.items` + the card-level `services.{key}` entries.

3b. Data migration: `2026_XX_XX_backfill_services_cms_content.php`.

**Important:** the migration uses `DB::table()` raw queries, NOT Eloquent models. Data migrations must be resilient to future model refactors, so they never depend on application model code.

- Reads `database/data/services-content-backfill.json`.
- Wraps in `DB::transaction()`.
- For each service in the fixture:
  - `DB::table('services')->updateOrInsert(['slug' => ...], [non-translatable cols + timestamps])` — preserves existing row if it exists.
  - Resolves `serviceId` from the resulting row.
  - For each locale:
    - `DB::table('service_translations')->updateOrInsert(['service_id' => $serviceId, 'locale' => $locale], [fat-table fields])` — merges fixture fields into existing translation rows from Phase 2.
  - For each child collection (stats, painPoints, capabilityGroups, etc.):
    - `DB::table('service_<thing>')->where('service_id', $serviceId)->delete()` — simple re-sync: fixture is authoritative for rich content on first run.
    - Re-insert rows from the fixture, then insert translation rows into `service_<thing>_translations` for each locale.
- Logs a summary at the end (services touched, rows written).
- Idempotent: re-running produces the same state.

**At end of Phase 3:** DB has all service content. `gamma-web` still reads from i18n. GraphQL queries the new data but nobody consumes them yet. Verify via GraphiQL / tinker.

### Phase 4 — model trait swap + drop old JSON columns

1. Swap `Service`, `ServiceFeature`, `ServiceBenefit`:
   - Remove `Spatie\Translatable\HasTranslations`.
   - Add `Astrotomic\Translatable\Translatable` + `Translatable` contract.
   - Configure `$translatedAttributes`.
2. Create relation models: `ServiceTranslation`, `ServiceFeatureTranslation`, `ServiceBenefitTranslation`.
3. Migration: drop `title`, `description`, `short_description` columns from `services`, and the equivalent JSON columns from `service_features` and `service_benefits`.
4. Update `ServiceSeeder.php` to use astrotomic's nested-array syntax (still only runs on local dev).

**Zero-downtime consideration:** between the drop-columns migration and the new code deploying, old code would fault on missing columns. Options:

- **Option A — brief maintenance window** (likely acceptable for a low-traffic consulting site): schedule the deploy, put the app in maintenance mode, run migrations, deploy code, drop maintenance mode. Downtime ~2–5 minutes.
- **Option B — two-deploy pattern**: Deploy 1 ships dual-reading code (reads from astrotomic if populated, falls back to spatie). Deploy 2 drops old columns. More conservative but twice the work.

This spec defaults to Option A with a maintenance window. If the production deployment platform (TBD) has no maintenance-mode support, the implementation plan will switch to Option B.

**Caveat:** `spatie/laravel-translatable` stays in `composer.json` because Solutions and Industries still use it. Removing it is out of scope.

### Phase 5 — implement GraphQL resolvers + tests

1. Write the new types + queries + mutations in `graphql/entities/service.graphql` (and possibly split into `service-admin.graphql` for admin-only types if the file grows too large).
2. Implement resolvers:
   - `App\GraphQL\Queries\ServiceDetail`
   - `App\GraphQL\Queries\ServicesList`
   - `App\GraphQL\Queries\Admin\ServiceForAdmin`
   - `App\GraphQL\Queries\Admin\ServicesForAdmin`
   - Admin mutations under `App\GraphQL\Mutations\Admin\Services\*`
3. Write PHPUnit feature tests (see Testing section below).
4. `./sail artisan test` — all green.

### Phase 6 — mcp-graphql verification

1. Use the `mcp-graphql` MCP to introspect the updated schema against the running local API. Confirm every new type is present.
2. Fire the `service(slug:)` query for every seeded service in both locales. Diff the response against the expected frontend shape.
3. Fire admin queries with a Sanctum admin token. Verify every child type returns all translations.
4. Fire each admin mutation once against a test service. Verify response + DB state.

### Phase 7 — `gamma-web` switches data source

In `gamma-web` (`feat/services-cms` branch):

1. Create `domains/services/composables/useServiceDetail.ts` — wraps the `service(slug:)` query, sets `X-Locale` header from Nuxt's active `$i18n.locale`, returns the grouped tree.
2. Update `domains/services/pages/service-detail.vue`:
   - Replace `const detail = tm('services.details.items.' + slug)` with `const { data: detail } = await useServiceDetail(slug)`.
   - Template stays unchanged because the API returns the same shape.
3. Update `domains/services/stores/useServicesStore.ts` to query the new listing fields (meta, hero fields used on cards if any).
4. **Keep the i18n fallback in place**: if the API query errors or returns null, fall back to `tm(...)`. This is the safety net for production rollout.
5. Test each service page in local dev against the local API. Pixel-diff against the current production page.

### Phase 8 — production deploy

1. Merge `gamma-api` PR to `main`. Deploy. Run `./sail artisan migrate --force` (Phases 1–4 all run automatically, tracked in `migrations` table).
2. Deploy `gamma-web`. Frontend queries the new API; i18n fallback still active.
3. Monitor error logs and page load metrics for 48 hours.

### Phase 9 — cleanup

1. Remove the `services.details.items` and card-level `services.{key}` blocks from `gamma-web/locales/en.json` + `fr.json`. Keep top-level `services.page.*` UI chrome.
2. Remove the i18n fallback branch in `service-detail.vue`.
3. Leave `spatie/laravel-translatable` installed (Solutions / Industries still use it).

## Testing Strategy

### Unit tests

- Model factories for every new entity.
- Astrotomic integration: `->translate('fr')->title` returns the French row; fallback returns the English row when FR is missing.

### Feature tests (PHPUnit)

**Public query coverage:**

- `ServiceDetailQueryTest::test_returns_full_tree_in_english` — seed one service with all sections + both locales, query with `X-Locale: en`, assert the full tree matches the expected frontend shape.
- `ServiceDetailQueryTest::test_returns_full_tree_in_french` — same with `X-Locale: fr`.
- `ServiceDetailQueryTest::test_falls_back_to_default_locale_when_translation_missing` — service has only EN, query with `X-Locale: fr`, expect EN content.
- `ServiceDetailQueryTest::test_returns_null_for_unpublished_service`.
- `ServiceDetailQueryTest::test_excludes_sections_with_no_items`.
- `ServiceListQueryTest::test_filters_inactive_and_unpublished`.

**Admin query coverage:**

- `ServiceAdminQueryTest::test_admin_sees_all_translations`.
- `ServiceAdminQueryTest::test_admin_query_rejects_unauthenticated`.

**Admin mutation coverage:**

- Parameterized test per child type: `create` → `update` → `reorder` → `delete`, each asserting DB state + translations.
- `PublishServiceTest::test_publish_and_unpublish_at_service_level`.
- `PublishServiceTest::test_publish_and_unpublish_at_translation_level`.

**Data migration coverage:**

- `ServicesCmsBackfillMigrationTest::test_fixture_seeds_all_services` — run the Phase 3 data migration on a fresh test DB, count rows per table, assert one known service has every section populated.
- `ServicesSpatieToAstrotomicMigrationTest::test_existing_spatie_data_migrates_without_loss`.

### Manual verification (post-deploy)

- `mcp-graphql` schema introspection against the production API.
- Query every production service slug in both locales, eyeball the response.
- Pixel diff one service detail page in `gamma-web` before and after Phase 7 cutover.

## Rollout Risks & Mitigations

| Risk | Impact | Mitigation |
|---|---|---|
| Data migration fails on production from fixture drift | Deploy blocked | Run the full migration set against a staging DB that mirrors prod before every deploy |
| `gamma-web` crashes when API returns unexpected null | Blank page | Phase 7 keeps the i18n fallback; if the API fails, the UI shows i18n content |
| Translations missing for some services at cutover | Some FR services render in EN | Astrotomic fallback is built-in. Content team fills gaps via admin after cutover. |
| Spatie / astrotomic coexistence causes trait conflicts | Model behavior breaks | Only `Service`, `ServiceFeature`, `ServiceBenefit` migrate. Solutions / Industries keep spatie. Document clearly in model headers. |
| Admin mutations accidentally exposed publicly | Security incident | Every admin mutation `@guard(with: ["sanctum"])` + role check. A dedicated feature test asserts unauthenticated requests are rejected. |
| Drop-columns migration breaks old code during deploy | Brief outage | Option A maintenance window (default), or Option B two-deploy dual-read pattern |
| Phase 3 fixture overwrites manual admin edits made between Phase 3 and cutover | Content loss | Fixture is idempotent; run it only once on first deploy. Subsequent deploys do not re-run the data migration. |

## Future Work (out of scope)

- Apply the same pattern to Solutions, Industries, Blog, Team, FAQ.
- Generalize into polymorphic content tables if three entities share the same shape.
- Remove `spatie/laravel-translatable` from `composer.json` after all models are migrated.
- Media library (hero images, section images, icons uploaded to S3).
- Content versioning / revision history.
- Approval workflow (draft → review → publish).
- Full-text search across translated content (Meilisearch / Typesense).
- Pick-from-catalog icon picker in `gamma-admin` instead of raw Bootstrap class strings.

## Open Questions

None at time of writing. Additional questions may surface while writing the implementation plan.
