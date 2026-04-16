<?php

namespace App\GraphQL\Mutations;

use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Thin create/update/delete resolvers for the public Service card.
 *
 * These are deliberately narrow — they only expose the card-level fields
 * (slug, icon, icon_color, category, order, is_active + title/short_description/description
 * translations for the default locale). For the full multi-section editor use
 * the admin mutations (createServiceAdmin / updateServiceAdmin) instead.
 */
class ServiceCrud
{
    private const DEFAULT_LOCALE = 'en';
    private const SUPPORTED_LOCALES = ['en', 'fr'];

    public function create(mixed $root, array $args): Service
    {
        $input = $args['input'];

        return DB::transaction(function () use ($input) {
            $service = Service::create([
                'slug' => $input['slug'] ?? Str::slug($input['title']),
                'icon' => $input['icon'] ?? null,
                'icon_color' => $input['icon_color'] ?? null,
                'category' => $input['category'] ?? null,
                'order' => $input['order'] ?? 0,
                'is_active' => $input['is_active'] ?? true,
            ]);

            $this->writeTranslation($service, $input);

            return $service->fresh();
        });
    }

    public function update(mixed $root, array $args): Service
    {
        $service = Service::findOrFail($args['id']);
        $input = $args['input'];

        return DB::transaction(function () use ($service, $input) {
            $columns = [];
            foreach (['slug', 'icon', 'icon_color', 'category', 'order', 'is_active'] as $key) {
                if (array_key_exists($key, $input)) {
                    $columns[$key] = $input[$key];
                }
            }

            if ($columns) {
                $service->update($columns);
            }

            $this->writeTranslation($service, $input);

            return $service->fresh();
        });
    }

    public function delete(mixed $root, array $args): array
    {
        try {
            $service = Service::findOrFail($args['id']);
            $service->delete();

            return [
                'success' => true,
                'message' => 'Service deleted successfully.',
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete service: '.$e->getMessage(),
            ];
        }
    }

    private function resolveLocale(array $input): string
    {
        $locale = $input['locale'] ?? self::DEFAULT_LOCALE;

        return in_array($locale, self::SUPPORTED_LOCALES, true) ? $locale : self::DEFAULT_LOCALE;
    }

    private const TRANSLATABLE_KEYS = [
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
    ];

    private function writeTranslation(Service $service, array $input): void
    {
        $locale = $this->resolveLocale($input);

        $payload = [];
        foreach (self::TRANSLATABLE_KEYS as $key) {
            if (array_key_exists($key, $input)) {
                $payload[$key] = $input[$key];
            }
        }

        if (empty($payload)) {
            return;
        }

        $service->translateOrNew($locale)->fill($payload)->save();
    }
}
