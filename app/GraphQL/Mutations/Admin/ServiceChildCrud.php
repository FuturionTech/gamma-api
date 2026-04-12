<?php

namespace App\GraphQL\Mutations\Admin;

use App\GraphQL\Queries\Admin\ServiceForAdminQuery;
use Illuminate\Support\Facades\DB;

final class ServiceChildCrud
{
    /**
     * Maps each child type to its model class, parent FK, GraphQL parent arg name,
     * and which base (non-translated) fields it has beyond id/order.
     */
    private const CONFIG = [
        'Stat' => [
            'model'     => \App\Models\ServiceStat::class,
            'parentKey' => 'service_id',
            'parentArg' => 'serviceId',
            'baseFields' => ['icon'],
        ],
        'PainPoint' => [
            'model'     => \App\Models\ServicePainPoint::class,
            'parentKey' => 'service_id',
            'parentArg' => 'serviceId',
            'baseFields' => [],
        ],
        'DeliveryItem' => [
            'model'     => \App\Models\ServiceDeliveryItem::class,
            'parentKey' => 'service_id',
            'parentArg' => 'serviceId',
            'baseFields' => ['icon'],
        ],
        'CapabilityGroup' => [
            'model'     => \App\Models\ServiceCapabilityGroup::class,
            'parentKey' => 'service_id',
            'parentArg' => 'serviceId',
            'baseFields' => ['icon'],
        ],
        'CapabilityItem' => [
            'model'     => \App\Models\ServiceCapabilityItem::class,
            'parentKey' => 'service_capability_group_id',
            'parentArg' => 'capabilityGroupId',
            'baseFields' => [],
        ],
        'UseCase' => [
            'model'     => \App\Models\ServiceUseCase::class,
            'parentKey' => 'service_id',
            'parentArg' => 'serviceId',
            'baseFields' => [],
        ],
        'ApproachStep' => [
            'model'     => \App\Models\ServiceApproachStep::class,
            'parentKey' => 'service_id',
            'parentArg' => 'serviceId',
            'baseFields' => ['icon'],
        ],
        'IndustryApplication' => [
            'model'     => \App\Models\ServiceIndustryApplication::class,
            'parentKey' => 'service_id',
            'parentArg' => 'serviceId',
            'baseFields' => ['icon'],
        ],
        'IndustryUseCase' => [
            'model'     => \App\Models\ServiceIndustryUseCase::class,
            'parentKey' => 'service_industry_application_id',
            'parentArg' => 'industryApplicationId',
            'baseFields' => [],
        ],
        'Technology' => [
            'model'     => \App\Models\ServiceTechnology::class,
            'parentKey' => 'service_id',
            'parentArg' => 'serviceId',
            'baseFields' => ['icon'],
        ],
        'BusinessImpact' => [
            'model'     => \App\Models\ServiceBusinessImpact::class,
            'parentKey' => 'service_id',
            'parentArg' => 'serviceId',
            'baseFields' => ['icon'],
        ],
        'Differentiator' => [
            'model'     => \App\Models\ServiceDifferentiator::class,
            'parentKey' => 'service_id',
            'parentArg' => 'serviceId',
            'baseFields' => ['icon'],
        ],
        'Feature' => [
            'model'     => \App\Models\ServiceFeature::class,
            'parentKey' => 'service_id',
            'parentArg' => 'serviceId',
            'baseFields' => ['icon'],
        ],
        'Benefit' => [
            'model'     => \App\Models\ServiceBenefit::class,
            'parentKey' => 'service_id',
            'parentArg' => 'serviceId',
            'baseFields' => ['icon'],
        ],
    ];

    /**
     * Explicit plural-to-singular map for reorder routing.
     *
     * Avoids fragile string manipulation (e.g. "Technologies" would fail
     * with a naive rtrim of 's').
     */
    private const REORDER_PLURAL_MAP = [
        'Stats'                 => 'Stat',
        'PainPoints'            => 'PainPoint',
        'DeliveryItems'         => 'DeliveryItem',
        'CapabilityGroups'      => 'CapabilityGroup',
        'CapabilityItems'       => 'CapabilityItem',
        'UseCases'              => 'UseCase',
        'ApproachSteps'         => 'ApproachStep',
        'IndustryApplications'  => 'IndustryApplication',
        'IndustryUseCases'      => 'IndustryUseCase',
        'Technologies'          => 'Technology',
        'BusinessImpacts'       => 'BusinessImpact',
        'Differentiators'       => 'Differentiator',
        'Features'              => 'Feature',
        'Benefits'              => 'Benefit',
    ];

    // -------------------------------------------------------------------------
    // Lighthouse calls methods like createStat, updatePainPoint, reorderStats
    // via @field(resolver: "...@methodName"). PHP __call routes them to the
    // four generic handlers.
    // -------------------------------------------------------------------------

    /** @param array<int, mixed> $arguments [$root, $args, $context, $resolveInfo] */
    public function __call(string $method, array $arguments): mixed
    {
        if (str_starts_with($method, 'create')) {
            return $this->handleCreate(substr($method, 6), ...$arguments);
        }

        if (str_starts_with($method, 'update')) {
            return $this->handleUpdate(substr($method, 6), ...$arguments);
        }

        if (str_starts_with($method, 'delete')) {
            return $this->handleDelete(substr($method, 6), ...$arguments);
        }

        if (str_starts_with($method, 'reorder')) {
            $plural = substr($method, 7);
            $type = self::REORDER_PLURAL_MAP[$plural]
                ?? throw new \InvalidArgumentException("Unknown reorder plural: {$plural}");

            return $this->handleReorder($type, ...$arguments);
        }

        throw new \BadMethodCallException("Unknown method: {$method}");
    }

    // -------------------------------------------------------------------------
    // Generic handlers
    // -------------------------------------------------------------------------

    private function handleCreate(string $type, mixed $root, array $args, mixed $context, mixed $resolveInfo): array
    {
        $config = $this->configFor($type);
        $input = $args['input'];
        $modelClass = $config['model'];

        return DB::transaction(function () use ($config, $input, $modelClass, $args) {
            $baseData = [
                $config['parentKey'] => $args[$config['parentArg']],
                'order'              => $input['order'] ?? 0,
            ];

            foreach ($config['baseFields'] as $field) {
                $baseData[$field] = $input[$field] ?? null;
            }

            $child = $modelClass::create($baseData);

            $this->syncChildTranslations($child, $input['translations'] ?? []);

            return ServiceForAdminQuery::projectChild(
                $child->fresh()->load('translations')
            );
        });
    }

    private function handleUpdate(string $type, mixed $root, array $args, mixed $context, mixed $resolveInfo): array
    {
        $config = $this->configFor($type);
        $modelClass = $config['model'];
        $child = $modelClass::findOrFail($args['id']);
        $input = $args['input'];

        return DB::transaction(function () use ($config, $input, $child) {
            $updates = [];

            if (isset($input['order'])) {
                $updates['order'] = $input['order'];
            }

            foreach ($config['baseFields'] as $field) {
                if (array_key_exists($field, $input)) {
                    $updates[$field] = $input[$field];
                }
            }

            if ($updates) {
                $child->update($updates);
            }

            if (isset($input['translations'])) {
                $this->syncChildTranslations($child, $input['translations']);
            }

            return ServiceForAdminQuery::projectChild(
                $child->fresh()->load('translations')
            );
        });
    }

    private function handleDelete(string $type, mixed $root, array $args, mixed $context, mixed $resolveInfo): bool
    {
        $config = $this->configFor($type);
        $modelClass = $config['model'];

        $modelClass::findOrFail($args['id'])->delete();

        return true;
    }

    private function handleReorder(string $type, mixed $root, array $args, mixed $context, mixed $resolveInfo): array
    {
        $config = $this->configFor($type);
        $modelClass = $config['model'];
        $parentKey = $config['parentKey'];
        $parentId = $args[$config['parentArg']];
        $orderedIds = $args['orderedIds'];

        return DB::transaction(function () use ($modelClass, $parentKey, $parentId, $orderedIds) {
            foreach ($orderedIds as $index => $id) {
                $modelClass::where('id', $id)
                    ->where($parentKey, $parentId)
                    ->update(['order' => $index]);
            }

            return $modelClass::where($parentKey, $parentId)
                ->orderBy('order')
                ->with('translations')
                ->get()
                ->map(fn ($child) => ServiceForAdminQuery::projectChild($child))
                ->all();
        });
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function configFor(string $type): array
    {
        return self::CONFIG[$type]
            ?? throw new \InvalidArgumentException("Unknown child type: {$type}");
    }

    /**
     * Sync translations for a child model.
     *
     * Child translation fields are already snake_case in the GraphQL inputs
     * (e.g. "value", "label", "text", "title", "description", "name"),
     * so no camelCase conversion is needed.
     */
    private function syncChildTranslations(mixed $child, array $translations): void
    {
        foreach ($translations as $trans) {
            $locale = $trans['locale'];
            unset($trans['locale']);

            $child->translateOrNew($locale)->fill($trans)->save();
        }
    }
}
