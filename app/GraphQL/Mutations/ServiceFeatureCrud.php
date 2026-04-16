<?php

namespace App\GraphQL\Mutations;

use App\Models\ServiceFeature;
use Illuminate\Support\Facades\DB;

class ServiceFeatureCrud
{
    private const DEFAULT_LOCALE = 'en';
    private const SUPPORTED_LOCALES = ['en', 'fr'];

    public function create(mixed $root, array $args): ServiceFeature
    {
        $input = $args['input'];
        $serviceId = $args['serviceId'];
        $locale = $this->resolveLocale($input);

        return DB::transaction(function () use ($input, $serviceId, $locale) {
            $maxOrder = ServiceFeature::where('service_id', $serviceId)->max('order') ?? 0;

            $feature = ServiceFeature::create([
                'service_id' => $serviceId,
                'icon' => $input['icon'] ?? null,
                'order' => $input['order'] ?? $maxOrder + 1,
            ]);

            $this->writeTranslation($feature, $input, $locale);

            return $feature->fresh();
        });
    }

    public function update(mixed $root, array $args): ServiceFeature
    {
        $feature = ServiceFeature::findOrFail($args['id']);
        $input = $args['input'];
        $locale = $this->resolveLocale($input);

        return DB::transaction(function () use ($feature, $input, $locale) {
            $columns = [];
            if (array_key_exists('icon', $input)) {
                $columns['icon'] = $input['icon'];
            }
            if (isset($input['order'])) {
                $columns['order'] = $input['order'];
            }
            if ($columns) {
                $feature->update($columns);
            }

            $this->writeTranslation($feature, $input, $locale);

            return $feature->fresh();
        });
    }

    public function delete(mixed $root, array $args): array
    {
        try {
            ServiceFeature::findOrFail($args['id'])->delete();
            return ['success' => true, 'message' => 'Feature deleted successfully.'];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Failed to delete feature: '.$e->getMessage()];
        }
    }

    private function resolveLocale(array $input): string
    {
        $locale = $input['locale'] ?? self::DEFAULT_LOCALE;
        return in_array($locale, self::SUPPORTED_LOCALES, true) ? $locale : self::DEFAULT_LOCALE;
    }

    private function writeTranslation(ServiceFeature $feature, array $input, string $locale): void
    {
        $payload = array_filter([
            'title' => $input['title'] ?? null,
            'description' => $input['description'] ?? null,
        ], static fn ($v) => $v !== null);

        if (empty($payload)) {
            return;
        }

        $feature->translateOrNew($locale)->fill($payload)->save();
    }
}
