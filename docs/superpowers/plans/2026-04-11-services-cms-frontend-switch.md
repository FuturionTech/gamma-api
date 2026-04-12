# Services CMS — Frontend Switch Implementation Plan (Plan D)

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Switch `gamma-web`'s service detail page from reading hardcoded i18n JSON (`locales/en.json` + `locales/fr.json` under `services.details.items`) to querying the live gamma-api GraphQL endpoint (`service(slug:)`). The existing template must render identically — same layout, same content, same locale-switching behavior. Keep the i18n fallback path in place as a safety net in case the API errors or returns null.

**Architecture:** Create a new `useServiceDetail(slug)` composable in gamma-web that hits the GraphQL API via the existing `useGraphql()` composable (which already sends `Accept-Language` header from the current i18n locale). The composable includes an ADAPTER function that transforms the API response shape (arrays of `{text}` / `{name}` objects) into the legacy shape the existing template expects (arrays of strings). Template stays byte-for-byte unchanged except for the source of `detail` — instead of `tm('services.details.items')[slug]`, it reads from the composable's reactive data, with a computed fallback to i18n if the API returns null.

**Tech Stack:** Nuxt 3, Vue 3, TypeScript, `useGraphql()` custom composable (uses `$fetch` against `GQL_HOST`), vue-i18n for locale management and fallback rendering.

**Reference spec:** `docs/superpowers/specs/2026-04-11-services-cms-design.md` — phase 7 ("gamma-web switches data source"). The spec's stated constraint: "the template stays exactly as written; only the data source changes."

**Prior plans:** Plans A, B, and C must all be deployed. gamma-api exposes `service(slug: String!)` with the full grouped tree, header-based locale resolution is working, and all 6 production services are populated in the DB.

**Scope boundary:** Plan D only switches the DETAIL page (`service-detail.vue`). The listing page (`services.vue` / `useServicesStore.ts`) already uses GraphQL via `useGraphql()` — no change needed there except OPTIONALLY adding the new translatable SEO fields to the listing query. Cleanup of dead i18n content (`services.details.items` block in `locales/*.json`) is deferred to Plan F.

---

## Prerequisite reading

- `docs/superpowers/specs/2026-04-11-services-cms-design.md` — full design spec, especially phase 7
- `docs/superpowers/plans/2026-04-11-services-cms-public-graphql-api.md` — Plan C, the API side
- `gamma-web/composables/useGraphql.ts` — the custom GraphQL composable (sends Accept-Language)
- `gamma-web/domains/services/pages/service-detail.vue` — the template (1117 lines), especially the `<script setup>` block starting at line 398 where `const detail = computed(...)` reads from `tm()`
- `gamma-web/domains/services/stores/useServicesStore.ts` — reference for how existing GraphQL queries are structured
- `gamma-web/domains/services/index.ts` — Nuxt module file; has a commented-out `autoImports:dirs` for a domain-local composables directory (we'll keep composables in the global `composables/` dir instead to avoid touching the module config)

### Existing shape mismatch

The gamma-api GraphQL response uses nested object arrays:
```
challenge.painPoints: [{ text: "..." }, ...]
howWeDeliver.items: [{ icon, text }]
capabilities.groups[].items: [{ name }]
industryApplications.industries[].useCases: [{ text }]
technologies.items: [{ icon, name }]
businessImpact.items: [{ icon, title, description }]
```

The existing template renders primitive strings in each loop:
```vue
<div v-for="point in detail.challenge.painPoints">{{ point }}</div>
<div v-for="item in detail.howWeDeliver.items">{{ item }}</div>
<li v-for="item in group.items">{{ item }}</li>
<li v-for="useCase in industry.useCases">{{ useCase }}</li>
<span v-for="item in detail.technologies.items">{{ item }}</span>
<p v-for="item in detail.businessImpact.items">{{ item }}</p>
```

Two rendering paths already support both shapes:
- `ourApproach.items[]` — uses `typeof item === 'object'` check in the template, so it accepts both string and `{title, description}` object — the API shape already fits
- `capabilities.groups[].items[]` — same check NOT present, uses `{{ item }}` which needs flattening

The composable's adapter function converts the API's nested-object arrays into plain string arrays so the template can keep rendering `{{ item }}` / `{{ point }}` / `{{ useCase }}` unchanged.

### Field name renames

The API uses camelCase that differs from the legacy i18n JSON in two places:
- API: `hero.ctaPrimaryLabel` → legacy: `hero.ctaPrimary`
- API: `hero.ctaSecondaryLabel` → legacy: `hero.ctaSecondary`
- API: `title` (at Service root) → legacy: `name` (used as `detail.name` in breadcrumb and SEO)

The adapter handles all three renames.

---

## File Structure

### Created

- `gamma-web/composables/useServiceDetail.ts` — new async composable that fetches + adapts + caches per-slug, with Reactive state for loading/error

### Modified

- `gamma-web/domains/services/pages/service-detail.vue` — replace the `detail` computed to call the composable with fallback to i18n

### Untouched

- `gamma-web/composables/useGraphql.ts` — already sends Accept-Language, no changes needed
- `gamma-web/domains/services/stores/useServicesStore.ts` — listing path is already on GraphQL
- `gamma-web/locales/en.json`, `locales/fr.json` — the i18n content stays in place (dead code fallback). Cleanup is Plan F.
- `gamma-web/domains/services/pages/services.vue` — listing page, unchanged
- Any Nuxt config, i18n config, GraphQL client config — all unchanged

---

## Live Backend Assumption

The plan assumes the gamma-api backend is accessible at the endpoint configured in `gamma-web/nuxt.config.ts`:
- Default: `process.env.GQL_HOST || 'https://gamma.ngrok.app/graphql'`
- Override for local dev: `GQL_HOST=http://localhost:8880/graphql`

Verify the endpoint is reachable before starting Task 3 (the live smoke test). If it's not running, start gamma-api via `cd gamma-api && ./sail up -d` first.

---

## Tasks

### Task 1: Create `useServiceDetail` composable

**Files:**
- Create: `gamma-web/composables/useServiceDetail.ts`

This task creates the new composable. It exports a function that takes a `slug` ref (or string), runs the GraphQL query, adapts the response to the legacy shape, and returns reactive state.

- [ ] **Step 1: Inspect existing composables directory for conventions**

Run:
```bash
ls gamma-web/composables/
cat gamma-web/composables/useGraphql.ts
```

Note the file style: TypeScript, top-level JSDoc comment, exports a `use*` function that returns an object with methods/state, relies on `$fetch` and `useRuntimeConfig`.

Your new file should follow the same conventions.

- [ ] **Step 2: Write the composable**

Create `gamma-web/composables/useServiceDetail.ts`:

```ts
/**
 * Service detail composable — fetches one service by slug from the gamma-api
 * GraphQL endpoint and adapts the API response to the legacy shape that
 * `domains/services/pages/service-detail.vue` expects.
 *
 * The adapter flattens the API's nested object arrays (e.g.,
 * `painPoints: [{ text }]`) into primitive string arrays (`painPoints: string[]`)
 * so the template can render `{{ point }}` unchanged.
 *
 * Locale: the underlying `useGraphql()` composable already sends
 * `Accept-Language` based on `useI18n().locale.value`, so the API returns
 * translations in the correct locale automatically.
 *
 * Fallback: callers can fall back to i18n-loaded content if this composable
 * returns null (e.g., API offline, service not seeded, network error). This
 * composable does NOT handle the fallback itself — see service-detail.vue
 * for the merging logic.
 */

export interface ApiService {
  id: string
  slug: string
  icon: string | null
  icon_color: string | null
  category: string | null
  is_active: boolean
  title: string
  short_description: string | null
  description: string | null
  meta_title: string | null
  meta_description: string | null
  meta_keywords: string | null
  hero: {
    tagline: string | null
    headline: string | null
    subheadline: string | null
    ctaPrimaryLabel: string | null
    ctaSecondaryLabel: string | null
    stats: Array<{ icon: string | null; value: string; label: string }>
  }
  challenge: {
    title: string | null
    description: string | null
    painPoints: Array<{ text: string }>
  } | null
  howWeDeliver: {
    title: string | null
    description: string | null
    items: Array<{ icon: string | null; text: string }>
  } | null
  capabilities: {
    title: string | null
    groups: Array<{
      icon: string | null
      name: string
      items: Array<{ name: string }>
    }>
  } | null
  keyUseCases: {
    title: string | null
    description: string | null
    items: Array<{ text: string }>
  } | null
  ourApproach: {
    title: string | null
    description: string | null
    items: Array<{
      icon: string | null
      title: string
      description: string | null
    }>
  } | null
  industryApplications: {
    title: string | null
    description: string | null
    industries: Array<{
      icon: string | null
      name: string
      description: string | null
      useCases: Array<{ text: string }>
    }>
  } | null
  technologies: {
    title: string | null
    description: string | null
    items: Array<{ icon: string | null; name: string }>
  } | null
  businessImpact: {
    title: string | null
    description: string | null
    items: Array<{
      icon: string | null
      title: string
      description: string | null
    }>
  } | null
  differentiators: {
    title: string | null
    points: Array<{
      icon: string | null
      title: string
      description: string | null
    }>
  } | null
  closing: {
    title: string | null
    subtitle: string | null
  }
  features: Array<{
    id: string
    title: string
    description: string | null
    icon: string | null
  }>
  benefits: Array<{
    id: string
    title: string
    description: string | null
    icon: string | null
  }>
}

/**
 * Legacy shape consumed by service-detail.vue (and service-detail.vue only).
 * Matches the shape that was previously read from `tm('services.details.items')`.
 */
export interface LegacyServiceDetail {
  name: string
  icon: string
  hero: {
    tagline: string
    headline: string
    subheadline: string
    ctaPrimary: string
    ctaSecondary: string
    stats: Array<{ value: string; label: string }>
  }
  challenge?: {
    title: string
    description: string
    painPoints: string[]
  }
  howWeDeliver: {
    title: string
    description: string
    items: string[]
  }
  capabilities?: {
    title: string
    groups: Array<{ name: string; icon: string; items: string[] }>
  }
  keyUseCases: {
    title: string
    description: string
    items: string[]
  }
  ourApproach: {
    title: string
    description: string
    items: Array<{ title: string; description: string }>
  }
  industryApplications?: {
    title: string
    description: string
    industries: Array<{
      name: string
      icon: string
      description: string
      useCases: string[]
    }>
  }
  technologies: {
    title: string
    description: string
    items: string[]
  }
  businessImpact: {
    title: string
    description: string
    items: string[]
  }
  differentiators?: {
    title: string
    points: Array<{ title: string; description: string; icon: string }>
  }
  closing: {
    title: string
    subtitle: string
  }
}

const SERVICE_DETAIL_QUERY = `
  query ServiceDetail($slug: String!) {
    service(slug: $slug) {
      id
      slug
      icon
      icon_color
      category
      is_active
      title
      short_description
      description
      meta_title
      meta_description
      meta_keywords
      hero {
        tagline
        headline
        subheadline
        ctaPrimaryLabel
        ctaSecondaryLabel
        stats {
          icon
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
        description
        items { icon text }
      }
      capabilities {
        title
        groups {
          icon
          name
          items { name }
        }
      }
      keyUseCases {
        title
        description
        items { text }
      }
      ourApproach {
        title
        description
        items { icon title description }
      }
      industryApplications {
        title
        description
        industries {
          icon
          name
          description
          useCases { text }
        }
      }
      technologies {
        title
        description
        items { icon name }
      }
      businessImpact {
        title
        description
        items { icon title description }
      }
      differentiators {
        title
        points { icon title description }
      }
      closing {
        title
        subtitle
      }
      features {
        id
        title
        description
        icon
      }
      benefits {
        id
        title
        description
        icon
      }
    }
  }
`

/**
 * Adapt the API response to the legacy shape used by service-detail.vue.
 * Returns null if `api` is null (service not found).
 */
export function adaptServiceDetail(api: ApiService | null): LegacyServiceDetail | null {
  if (!api) return null

  return {
    name: api.title,
    icon: api.icon ?? '',
    hero: {
      tagline: api.hero.tagline ?? '',
      headline: api.hero.headline ?? api.title,
      subheadline: api.hero.subheadline ?? '',
      ctaPrimary: api.hero.ctaPrimaryLabel ?? '',
      ctaSecondary: api.hero.ctaSecondaryLabel ?? '',
      stats: api.hero.stats.map((s) => ({
        value: s.value,
        label: s.label,
      })),
    },
    challenge: api.challenge
      ? {
          title: api.challenge.title ?? '',
          description: api.challenge.description ?? '',
          painPoints: api.challenge.painPoints.map((p) => p.text),
        }
      : undefined,
    howWeDeliver: api.howWeDeliver
      ? {
          title: api.howWeDeliver.title ?? '',
          description: api.howWeDeliver.description ?? '',
          items: api.howWeDeliver.items.map((i) => i.text),
        }
      : { title: '', description: '', items: [] },
    capabilities: api.capabilities
      ? {
          title: api.capabilities.title ?? '',
          groups: api.capabilities.groups.map((g) => ({
            name: g.name,
            icon: g.icon ?? '',
            items: g.items.map((i) => i.name),
          })),
        }
      : undefined,
    keyUseCases: api.keyUseCases
      ? {
          title: api.keyUseCases.title ?? '',
          description: api.keyUseCases.description ?? '',
          items: api.keyUseCases.items.map((u) => u.text),
        }
      : { title: '', description: '', items: [] },
    ourApproach: api.ourApproach
      ? {
          title: api.ourApproach.title ?? '',
          description: api.ourApproach.description ?? '',
          items: api.ourApproach.items.map((s) => ({
            title: s.title,
            description: s.description ?? '',
          })),
        }
      : { title: '', description: '', items: [] },
    industryApplications: api.industryApplications
      ? {
          title: api.industryApplications.title ?? '',
          description: api.industryApplications.description ?? '',
          industries: api.industryApplications.industries.map((ind) => ({
            name: ind.name,
            icon: ind.icon ?? '',
            description: ind.description ?? '',
            useCases: ind.useCases.map((uc) => uc.text),
          })),
        }
      : undefined,
    technologies: api.technologies
      ? {
          title: api.technologies.title ?? '',
          description: api.technologies.description ?? '',
          items: api.technologies.items.map((t) => t.name),
        }
      : { title: '', description: '', items: [] },
    businessImpact: api.businessImpact
      ? {
          title: api.businessImpact.title ?? '',
          description: api.businessImpact.description ?? '',
          // Template renders each item as a string; flatten to title.
          // Description is currently not rendered in the UI — if that changes,
          // update the template to handle the richer shape.
          items: api.businessImpact.items.map((b) => b.title),
        }
      : { title: '', description: '', items: [] },
    differentiators: api.differentiators
      ? {
          title: api.differentiators.title ?? '',
          points: api.differentiators.points.map((d) => ({
            title: d.title,
            description: d.description ?? '',
            icon: d.icon ?? '',
          })),
        }
      : undefined,
    closing: {
      title: api.closing.title ?? '',
      subtitle: api.closing.subtitle ?? '',
    },
  }
}

/**
 * Fetch a single service's detail tree by slug.
 *
 * Returns reactive state:
 * - `detail`: the adapted legacy-shape detail or null if not found
 * - `loading`: true while the fetch is in flight
 * - `error`: Error message string if the fetch failed, else null
 * - `refresh`: re-fetch the current slug (e.g., on locale change)
 */
export function useServiceDetail(slug: Ref<string> | string) {
  const slugRef = typeof slug === 'string' ? ref(slug) : slug
  const { query } = useGraphql()

  const detail = ref<LegacyServiceDetail | null>(null)
  const loading = ref(false)
  const error = ref<string | null>(null)

  async function fetchDetail() {
    if (!slugRef.value) {
      detail.value = null
      return
    }
    loading.value = true
    error.value = null
    try {
      const data = await query<{ service: ApiService | null }>(
        SERVICE_DETAIL_QUERY,
        { slug: slugRef.value }
      )
      detail.value = adaptServiceDetail(data.service ?? null)
    } catch (e: any) {
      error.value = e?.message ?? 'Failed to fetch service detail.'
      detail.value = null
    } finally {
      loading.value = false
    }
  }

  // Fetch on mount and on slug change
  watchEffect(() => {
    // Capture the slug in the effect so Vue tracks it
    if (slugRef.value) {
      void fetchDetail()
    } else {
      detail.value = null
    }
  })

  // Also re-fetch when the current i18n locale changes
  const { locale } = useI18n()
  watch(locale, () => {
    void fetchDetail()
  })

  return {
    detail,
    loading,
    error,
    refresh: fetchDetail,
  }
}
```

Key points:
- Uses `useGraphql()` which already sets the Accept-Language header from the active i18n locale
- Auto-imports: Nuxt 3 with `autoImport: true` means `ref`, `watch`, `watchEffect`, `useI18n`, and our own `useGraphql` are all available without explicit imports
- Re-fetches on slug change AND on locale change
- Adapter handles all shape mismatches + field renames + null coalescing

- [ ] **Step 3: Type check**

Run:
```bash
cd gamma-web
pnpm exec nuxi typecheck 2>&1 | tail -30
```

Expected: no type errors on the new file. If there are errors:
- `Cannot find name 'ref'` or similar → Nuxt auto-imports may not be working in the file — add explicit `import { ref, watch, watchEffect } from 'vue'` and `import { useI18n } from '#imports'` at the top
- Type mismatch in `adaptServiceDetail` — check the API type definitions match the actual GraphQL response shape

- [ ] **Step 4: Commit**

```bash
cd gamma-web
git add composables/useServiceDetail.ts
git commit -m "$(cat <<'EOF'
Add useServiceDetail composable for GraphQL-backed detail fetch

Queries gamma-api's service(slug:) query and adapts the nested-object
response to the legacy shape expected by service-detail.vue (flat string
arrays for painPoints, useCases, capability items, etc. + renamed
ctaPrimary/ctaSecondary fields).

Re-fetches on slug change AND on i18n locale change. Relies on the
existing useGraphql() composable which sends Accept-Language from the
active locale.

Part of Plan D of the services CMS rollout.

Co-Authored-By: Claude Opus 4.6 (1M context) <noreply@anthropic.com>
EOF
)"
```

---

### Task 2: Update `service-detail.vue` to use the composable

**Files:**
- Modify: `gamma-web/domains/services/pages/service-detail.vue` (script block only, template unchanged)

- [ ] **Step 1: Read the current script setup**

```bash
cd gamma-web
sed -n '398,460p' domains/services/pages/service-detail.vue
```

Note the existing:
- `const { t, tm, locale } = useI18n()`
- `const route = useRoute()`
- `const slug = computed(() => String(route.params.slug || ''))`
- `const detail = computed<ServiceDetail | null>(() => { ... })` — this is what we replace
- `const impactIcons = [...]`
- `interface ServiceDetail { ... }` — keep this; it matches the `LegacyServiceDetail` we import from the composable

The `interface ServiceDetail` local declaration duplicates what's in the composable. Remove the local declaration and import `LegacyServiceDetail as ServiceDetail` from the composable to avoid drift.

- [ ] **Step 2: Edit the script block**

Open `domains/services/pages/service-detail.vue`.

**a)** REMOVE the local `interface ServiceDetail { ... }` declaration (approximately lines 417–452 based on current file shape).

**b)** REPLACE this block:

```ts
// Read service details from i18n
const detail = computed<ServiceDetail | null>(() => {
  const items = tm('services.details.items') as Record<string, ServiceDetail> | undefined
  if (!items || !items[slug.value]) return null
  return items[slug.value]
})
```

with this:

```ts
import type { LegacyServiceDetail as ServiceDetail } from '~/composables/useServiceDetail'

// Fetch service detail from the gamma-api GraphQL endpoint.
// On slug or locale change, the composable re-fetches automatically.
const { detail: apiDetail } = useServiceDetail(slug)

// Fallback to the legacy i18n content if the API is unreachable or the slug
// is missing from the backend. This ensures the page still renders during
// the cutover period and for any service that hasn't been migrated yet.
// After Plan F ships, the i18n fallback can be removed.
const detail = computed<ServiceDetail | null>(() => {
  if (apiDetail.value) return apiDetail.value

  // Fallback: read from i18n JSON (legacy behavior)
  const items = tm('services.details.items') as Record<string, ServiceDetail> | undefined
  if (!items || !items[slug.value]) return null
  return items[slug.value]
})
```

**c)** Make sure the existing `const { t, tm, locale } = useI18n()` is still present — the fallback uses `tm()`.

**d)** The `import type` statement goes at the top of `<script setup>`, right after the existing `import { useHead } from '#imports'` line. Nuxt 3 supports `import type` in script setup.

- [ ] **Step 3: Start the Nuxt dev server (for live verification in Task 3)**

You do NOT need to run the dev server in this task — just save the file and ensure there are no compile errors. Task 3 will start the server and verify.

Quick sanity check:

```bash
cd gamma-web
pnpm exec nuxi typecheck 2>&1 | grep -A 2 "service-detail.vue" | head -20
```

Expected: no type errors in `service-detail.vue`. If there are, debug.

- [ ] **Step 4: Commit**

```bash
cd gamma-web
git add domains/services/pages/service-detail.vue
git commit -m "$(cat <<'EOF'
Switch service-detail.vue to read from gamma-api GraphQL

Replaces the `tm('services.details.items')[slug]` lookup with a call
to the new useServiceDetail composable. The i18n path is kept as a
fallback so the page still renders if the API is unreachable or the
slug isn't yet in the database. Template is unchanged — only the
source of `detail` moves from static JSON to the live API.

Also imports the LegacyServiceDetail type from the composable so the
local ServiceDetail interface stays in sync with the adapter's output.

Part of Plan D of the services CMS rollout.

Co-Authored-By: Claude Opus 4.6 (1M context) <noreply@anthropic.com>
EOF
)"
```

---

### Task 3: Live smoke test against running backend

**Files:** none (verification only)

- [ ] **Step 1: Ensure gamma-api is running**

```bash
cd /Users/acompaore/Documents/Futurion/Development/Web/gamma/gamma-api
./sail ps 2>&1 | head
```

If Sail is down, start it:
```bash
./sail up -d
```

Verify the GraphQL endpoint responds:
```bash
curl -s -X POST http://localhost:8880/graphql \
  -H "Content-Type: application/json" \
  -d '{"query":"{ service(slug: \"ai-intelligent-systems\") { title } }"}' | python3 -m json.tool
```

Expected: `{"data":{"service":{"title":"AI & Intelligent Systems"}}}`.

- [ ] **Step 2: Start gamma-web dev server pointing at local API**

In a NEW terminal:
```bash
cd /Users/acompaore/Documents/Futurion/Development/Web/gamma/gamma-web
GQL_HOST=http://localhost:8880/graphql pnpm dev
```

Wait for "Local: http://localhost:3006" to appear.

- [ ] **Step 3: Fetch the rendered HTML for a service detail page (EN)**

In another terminal:
```bash
curl -s http://localhost:3006/en/services/ai-intelligent-systems | grep -o -E "AI &amp; Intelligent Systems|Build AI systems that deliver|Machine Learning" | head
```

Expected: at least one of those strings should appear in the output, indicating the API content is being rendered (not falling back to i18n).

Alternatively, fetch the HTML and save to a file for inspection:
```bash
curl -s http://localhost:3006/en/services/ai-intelligent-systems > /tmp/gamma-en-ai.html
grep -c "AI & Intelligent Systems" /tmp/gamma-en-ai.html
grep -c "Build AI systems" /tmp/gamma-en-ai.html
grep -c "Production model accuracy" /tmp/gamma-en-ai.html
```

Each grep should return a positive count.

- [ ] **Step 4: Fetch the rendered HTML in French**

```bash
curl -s http://localhost:3006/services/ai-intelligent-systems > /tmp/gamma-fr-ai.html
grep -c "IA et systèmes intelligents" /tmp/gamma-fr-ai.html
```

Expected: positive count (FR title rendered).

If the French version shows EN content, the locale isn't being detected. Check:
- The route uses `/services/...` (no prefix) for FR per the i18n config
- The `Accept-Language` or i18n detection is working

- [ ] **Step 5: Check every service**

```bash
for slug in ai-intelligent-systems data-engineering-platforms cloud-strategy-engineering cybersecurity business-intelligence big-data; do
  echo "=== $slug ==="
  curl -s "http://localhost:3006/en/services/$slug" | grep -o "<title>[^<]*</title>" | head -1
done
```

Expected: each title should match the expected service name (not "Service | Gamma Neutral" generic fallback).

- [ ] **Step 6: Verify the fallback still works**

Temporarily stop Sail or point GQL_HOST to a bogus URL:
```bash
# Kill dev server
# Restart with broken API
GQL_HOST=http://localhost:9999/graphql pnpm dev
```

Then fetch the page:
```bash
curl -s http://localhost:3006/en/services/ai-intelligent-systems | grep -c "AI & Intelligent Systems"
```

Expected: positive count — the fallback to i18n serves the content even though the API is unreachable.

Restart the dev server with the correct API endpoint when done.

- [ ] **Step 7: Document the result**

No commit required. Record the results in your report to the controller.

---

### Task 4: Commit cleanup, push, and final verification

**Files:** none (git)

- [ ] **Step 1: Check gamma-web git state**

```bash
cd /Users/acompaore/Documents/Futurion/Development/Web/gamma/gamma-web
git status
git log origin/main..HEAD --oneline
```

Expected: 2 commits (Task 1 composable + Task 2 page switch), working tree clean.

- [ ] **Step 2: Push gamma-web commits**

```bash
cd gamma-web
git push origin main
```

Expected: 2 commits pushed.

- [ ] **Step 3: Check gamma-api git state and push Plan D doc commit**

The Plan D document itself is committed to gamma-api. Confirm it's been pushed:

```bash
cd /Users/acompaore/Documents/Futurion/Development/Web/gamma/gamma-api
git log origin/main..HEAD --oneline
```

If there's an unpushed commit for the Plan D document, push it:
```bash
git push origin main
```

---

## Self-review checklist

After Task 4:

1. **Composable created** — `gamma-web/composables/useServiceDetail.ts` exists with the GraphQL query + adapter + reactive state
2. **Page updated** — `service-detail.vue` calls `useServiceDetail(slug)` and keeps the i18n fallback for resilience
3. **Live test** — at least 2 service pages (EN + FR) render content from the API
4. **Fallback works** — when the API is unreachable, i18n content is served
5. **No template changes** — the template HTML has not been modified (all edits are in the `<script setup>` block)
6. **Type safety** — `pnpm exec nuxi typecheck` passes

## Rollback considerations

If something in Plan D breaks the detail page:
- The `detail` computed still falls back to i18n, so the page should render content even if the API call fails
- To fully revert, `git revert` the Task 2 commit (service-detail.vue edit) and the page returns to the pure i18n path
- The composable (Task 1) is harmless — it's not imported by anything else

## Next plans (context)

- **Plan E — Admin GraphQL API**: gamma-admin-facing types, queries, and mutations. Enables admins to edit services content via the gamma-admin UI.
- **Plan F — Cleanup**: remove `services.details.items` + `services.{cardKey}` blocks from `gamma-web/locales/*.json`, remove the i18n fallback from `service-detail.vue`, remove the `businessImpact.items` description field mapping from the adapter (or update the template to render it).
