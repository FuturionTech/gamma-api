# Services CMS — Admin GraphQL API Implementation Plan (Plan E)

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Implement the admin-facing GraphQL API for the services CMS: `ServiceForAdmin` types that expose ALL translations (not just the active locale), admin queries with Sanctum guard, and CRUD + reorder mutations for the Service parent and all 12 child entity types, plus per-locale publish/unpublish mutations.

**Architecture:** Admin types are separate from public types (no accidental leakage of draft translations). Every admin query/mutation is `@guard(with: ["sanctum"])`. Mutations accept nested `translations: [{ locale, ...fields }]` arrays. Resolvers return all translation rows (not locale-filtered). Reorder mutations accept `orderedIds: [ID!]!` and update the `order` column in bulk.

**Tech Stack:** Laravel 12, PHP 8.3, Lighthouse ^6.63, Sanctum, astrotomic/laravel-translatable ^11.17, PHPUnit 11.

**Reference spec:** `docs/superpowers/specs/2026-04-11-services-cms-design.md` — "Admin types", "Admin queries", "Admin mutations" sections.

**Prior plans:** Plans A-D must be deployed.

**Scope:** Admin read + write API only. No gamma-admin UI changes (that's a separate project).

---

## File Structure

### Created

**GraphQL schema:**
- `graphql/entities/service-admin.graphql` — all admin types, queries, mutations (keep separate from the public `service.graphql` to avoid file bloat)

**Resolvers (in `app/GraphQL/`):**
- `Queries/Admin/ServiceForAdmin.php` — single service with all translations
- `Queries/Admin/ServicesForAdmin.php` — list with all translations
- `Mutations/Admin/UpsertService.php` — create + update service
- `Mutations/Admin/DeleteService.php`
- `Mutations/Admin/PublishService.php` — set/unset `published_at`
- `Mutations/Admin/PublishServiceTranslation.php` — per-locale publish
- `Mutations/Admin/ServiceChildCrud.php` — generic CRUD handler for child entities (one class handling create/update/delete/reorder for all 12 child types via a configuration array — avoids 48 separate resolver classes)

**Tests:**
- `tests/Feature/GraphQL/Admin/ServiceAdminQueryTest.php`
- `tests/Feature/GraphQL/Admin/ServiceAdminMutationTest.php`
- `tests/Feature/GraphQL/Admin/ServiceChildCrudTest.php`

### Modified

- `graphql/schema.graphql` or equivalent — import the new `service-admin.graphql` file (verify how Lighthouse discovers `.graphql` files in this project — it may auto-discover all files under `graphql/`)

---

## Architecture — Admin types pattern

### ServiceForAdmin type

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
  translations: [ServiceTranslationForAdmin!]!
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
  createdAt: DateTime!
  updatedAt: DateTime!
}

type ServiceTranslationForAdmin {
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
```

### Child ForAdmin pattern (same for all 12)

```graphql
type ServiceStatForAdmin {
  id: ID!
  order: Int!
  icon: String
  translations: [ServiceStatTranslationForAdmin!]!
}

type ServiceStatTranslationForAdmin {
  locale: String!
  value: String!
  label: String!
}
```

### Admin query pattern

```graphql
extend type Query {
  serviceForAdmin(id: ID!): ServiceForAdmin @guard(with: ["sanctum"])
  servicesForAdmin(isActive: Boolean): [ServiceForAdmin!]! @guard(with: ["sanctum"])
}
```

### Admin mutation pattern

```graphql
extend type Mutation {
  createService(input: CreateServiceAdminInput!): ServiceForAdmin @guard(with: ["sanctum"])
  updateService(id: ID!, input: UpdateServiceAdminInput!): ServiceForAdmin @guard(with: ["sanctum"])
  deleteService(id: ID!): Boolean @guard(with: ["sanctum"])
  publishService(id: ID!): ServiceForAdmin @guard(with: ["sanctum"])
  unpublishService(id: ID!): ServiceForAdmin @guard(with: ["sanctum"])
  publishServiceTranslation(serviceId: ID!, locale: String!): ServiceTranslationForAdmin @guard(with: ["sanctum"])
  unpublishServiceTranslation(serviceId: ID!, locale: String!): ServiceTranslationForAdmin @guard(with: ["sanctum"])

  # Per child type — same pattern for all 12:
  createServiceStat(serviceId: ID!, input: ServiceStatInput!): ServiceStatForAdmin @guard(with: ["sanctum"])
  updateServiceStat(id: ID!, input: ServiceStatInput!): ServiceStatForAdmin @guard(with: ["sanctum"])
  deleteServiceStat(id: ID!): Boolean @guard(with: ["sanctum"])
  reorderServiceStats(serviceId: ID!, orderedIds: [ID!]!): [ServiceStatForAdmin!]! @guard(with: ["sanctum"])
  # ... repeat for painPoints, deliveryItems, capabilityGroups, capabilityItems,
  #     useCases, approachSteps, industryApplications, industryUseCases,
  #     technologies, businessImpacts, differentiators, features, benefits
}

input ServiceStatInput {
  order: Int
  icon: String
  translations: [ServiceStatTranslationInput!]!
}

input ServiceStatTranslationInput {
  locale: String!
  value: String!
  label: String!
}
```

### Generic child CRUD resolver

To avoid writing 48 nearly-identical mutation resolver classes, use a SINGLE configurable class:

```php
// app/GraphQL/Mutations/Admin/ServiceChildCrud.php

final class ServiceChildCrud
{
    private static array $config = [
        'ServiceStat' => [
            'model' => \App\Models\ServiceStat::class,
            'parentKey' => 'service_id',
            'translationFields' => ['value', 'label'],
            'baseFields' => ['icon'],
        ],
        'ServicePainPoint' => [
            'model' => \App\Models\ServicePainPoint::class,
            'parentKey' => 'service_id',
            'translationFields' => ['text'],
            'baseFields' => [],
        ],
        // ... for all 12 child types
    ];

    public function create(mixed $root, array $args, ...): array { /* generic create */ }
    public function update(mixed $root, array $args, ...): array { /* generic update */ }
    public function delete(mixed $root, array $args, ...): bool { /* generic delete */ }
    public function reorder(mixed $root, array $args, ...): array { /* generic reorder */ }
}
```

Each mutation in the schema uses `@field(resolver: "...ServiceChildCrud@create")` with the entity type passed as a directive arg or inferred from the mutation name.

---

## Tasks

### Task 1: Create `service-admin.graphql` schema

Write all admin types (ServiceForAdmin, ServiceTranslationForAdmin, 12 child ForAdmin types + their translation types), admin queries (serviceForAdmin, servicesForAdmin), and admin mutations (parent CRUD + publish + 12 child CRUD + reorder).

Also create the input types for each mutation.

Commit: 1 file.

### Task 2: Implement admin query resolvers

Create `Queries/Admin/ServiceForAdmin.php` and `Queries/Admin/ServicesForAdmin.php`.

The admin resolvers:
- Load ALL translations (no locale filter)
- Load ALL child relations eager-loaded
- Return arrays that Lighthouse maps to the ForAdmin types
- Every translation is returned as a separate object in the `translations` array

Key difference from public resolver: instead of projecting flat columns into sections, return the raw `$service->translations` collection. Each `ServiceTranslation` model instance becomes one entry in the `translations` array.

For child entities: return the raw model instances with their `translations` relation eager-loaded. Lighthouse maps `$stat->translations` to `ServiceStatTranslationForAdmin[]`.

Commit: 2 files.

### Task 3: PHPUnit tests for admin queries

Test that:
- Authenticated admin can query `serviceForAdmin(id:)` and gets ALL translations
- Authenticated admin can query `servicesForAdmin` and gets the full list
- Unauthenticated request returns 401/error
- Response includes nested translations arrays for every child type

Commit: 1 file.

### Task 4: Implement parent Service mutations

Create resolvers for:
- `createService` — creates a Service + its translations from the nested input
- `updateService` — updates base fields + upserts translations
- `deleteService` — soft-delete or hard-delete (spec says no SoftDeletes, so hard-delete with cascade)
- `publishService` / `unpublishService` — set/unset `published_at`
- `publishServiceTranslation` / `unpublishServiceTranslation` — set/unset `published_at` on a specific translation row

Commit: 1-2 files.

### Task 5: PHPUnit tests for parent mutations

Test create, update, delete, publish/unpublish at service level and translation level.

Commit: 1 file.

### Task 6: Implement generic child CRUD resolver

Create `ServiceChildCrud.php` with the config array for all 12 child types. Implement the 4 methods (create, update, delete, reorder).

The `create` method:
1. Creates the base child row (icon, order, parent FK)
2. For each translation in the input, creates a translation row via `translateOrNew($locale)->fill([...])->save()`
3. Returns the child with translations eager-loaded

The `update` method:
1. Finds the child by ID
2. Updates base fields
3. Upserts translations (create new locales, update existing)
4. Returns the child refreshed

The `delete` method: finds by ID, deletes (cascade removes translations). Returns true.

The `reorder` method:
1. Takes `serviceId` + `orderedIds: [ID!]!`
2. Validates all IDs belong to the given service
3. Updates `order` column for each ID based on array position
4. Returns the reordered collection

Commit: 1 file.

### Task 7: PHPUnit tests for child CRUD

Test create/update/delete/reorder for at least 3 representative child types:
- ServiceStat (simple: value + label)
- ServiceCapabilityGroup (has nested items)
- ServiceIndustryApplication (has nested use cases)

Each test:
- Creates via mutation, asserts DB state + response shape
- Updates via mutation, asserts translations updated
- Reorders, asserts order column updated
- Deletes, asserts cascade cleanup

Also test auth: unauthenticated requests should fail.

Commit: 1 file.

### Task 8: Final test suite + push

Run full test suite. Push.

---

## Execution notes

- All admin types use camelCase field names (GraphQL convention): `iconColor`, `isActive`, `publishedAt`, `heroTagline`, etc.
- Sanctum authentication: tests need to create an admin user and authenticate. Check existing `AuthenticationTest.php` for the pattern.
- The existing `createService` / `updateService` / `deleteService` mutations in `service.graphql` use Lighthouse's `@create` / `@update` / `@delete` directives with the old spatie-era input shapes. These should be REMOVED from `service.graphql` and replaced by the new admin mutations in `service-admin.graphql`. Or: keep both during transition and deprecate the old ones in Plan F.
- The generic child CRUD approach saves ~44 resolver files (12 types × 4 mutations - 4 methods in one class). Trade-off: slightly less discoverable, but much DRYer.

---

## Next plan

- **Plan F — Cleanup** (below)
