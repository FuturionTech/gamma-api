# Services CMS — Cleanup Implementation Plan (Plan F)

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Remove dead code and fallback paths now that Plans A-E are stable. Clean up the i18n locale files, remove the i18n fallback from `service-detail.vue`, remove the old Lighthouse mutations from `service.graphql` that were superseded by the admin API, and optionally remove `spatie/laravel-translatable` from `composer.json` if no other models still use it.

**Architecture:** Pure deletion / simplification. No new features.

**Tech Stack:** gamma-api (Laravel), gamma-web (Nuxt 3).

**Reference spec:** `docs/superpowers/specs/2026-04-11-services-cms-design.md` — phase 9 ("Cleanup").

**Prior plans:** Plans A-E must be deployed and verified stable for at least 48 hours before executing Plan F.

---

## Tasks

### Task 1: Remove i18n fallback from `service-detail.vue` (gamma-web)

**Files:**
- Modify: `gamma-web/domains/services/pages/service-detail.vue`

Replace the current `detail` computed (which has the fallback to `tm()`) with a direct read from the composable:

```ts
// BEFORE (Plan D)
const { detail: apiDetail } = useServiceDetail(slug)

const detail = computed<ServiceDetail | null>(() => {
  if (apiDetail.value) return apiDetail.value
  const items = tm('services.details.items') as Record<string, ServiceDetail> | undefined
  if (!items || !items[slug.value]) return null
  return items[slug.value]
})

// AFTER (Plan F)
const { detail } = useServiceDetail(slug)
```

Also remove `tm` from the `useI18n()` destructure if it's no longer used elsewhere in the file. Keep `t` if it's used for static UI chrome like section labels (`$t('services.details.sectionLabels.challenge')`).

Commit to gamma-web.

### Task 2: Delete dead `services.details.items` from locale JSON files (gamma-web)

**Files:**
- Modify: `gamma-web/locales/en.json`
- Modify: `gamma-web/locales/fr.json`

Delete the entire `services.details.items` block from both files. This is the block that contained the hardcoded service detail content for all 6 services. It was the source of truth before Plan D but is now dead code.

Also delete the card-level `services.ai`, `services.dataEngineering`, `services.cloud`, `services.cybersecurity`, `services.bi`, `services.bigData` blocks IF they are no longer consumed anywhere in the codebase (grep for references first).

Keep `services.page.*`, `services.details.sectionLabels.*`, `services.details.ctaContact`, `services.details.viewApproach`, `services.details.notFound`, `services.details.backToServices` — these are UI chrome strings still used by the template.

**Before deleting, verify:**
```bash
cd gamma-web
grep -rn "tm('services.details.items')" domains/ components/ pages/ | head
grep -rn "services.ai\.\|services.dataEngineering\.\|services.cloud\.\|services.cybersecurity\.\|services.bi\.\|services.bigData\." domains/ components/ pages/ | head
```

Only delete keys that have ZERO references.

Commit to gamma-web.

### Task 3: Remove old Lighthouse mutations from `service.graphql` (gamma-api)

**Files:**
- Modify: `gamma-api/graphql/entities/service.graphql`

The Plan C schema rewrite kept the old `createService`, `updateService`, `deleteService`, `createServiceFeature`, etc. mutations from the pre-CMS era. Now that Plan E provides the proper admin mutations in `service-admin.graphql`, the old ones should be removed to avoid confusion.

Remove:
- The `extend type Mutation { ... }` block with the old mutations
- The old `CreateServiceInput`, `UpdateServiceInput` input types (Plan E has new ones)
- The old `CreateServiceFeatureInput`, `UpdateServiceFeatureInput`, `CreateServiceBenefitInput`, `UpdateServiceBenefitInput` input types

Keep: the public `extend type Query { ... }` block and all public type definitions.

**Before removing, verify** no code references the old mutation names:
```bash
cd gamma-api
grep -rn "createService\|updateService\|deleteService" app/GraphQL/ tests/ | head
```

If the old mutations are referenced in existing tests (`ServiceQueryTest.php` or similar), update or remove those tests.

Commit to gamma-api.

### Task 4: Evaluate removing `spatie/laravel-translatable` (gamma-api)

**Files:**
- Possibly modify: `composer.json`

Check if any model outside the services domain still uses spatie:

```bash
cd gamma-api
grep -rn "HasTranslations\|Spatie\\\\Translatable" app/Models/ | grep -v "Service"
```

If `Solution`, `Industry`, `FAQ`, `Stat`, `BlogPost`, etc. still use spatie, do NOT remove the package — those models will be migrated in their own future specs.

If NOTHING else uses spatie, run:
```bash
./sail composer remove spatie/laravel-translatable
```

And commit.

### Task 5: Final verification + push

- Run full test suite on gamma-api
- Run `pnpm dev` on gamma-web and verify service detail pages load from API
- Push both repos

---

## Execution notes

- Plan F is low-risk but destructive (removing dead code). Verify references before deleting.
- The locale files are large (~85KB EN, ~97KB FR). Removing the `services.details.items` block will shrink them significantly.
- Plan F should only execute AFTER Plans A-E have been deployed and verified stable in production for at least 48 hours.
- If the user's "production" is just local dev, the 48-hour wait is optional.
