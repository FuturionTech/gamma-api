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

    // -------------------------------------------------------------------------
    // Lighthouse resolver methods.
    //
    // Each child type needs four concrete methods (create, update, delete,
    // reorder) because Lighthouse validates method existence at boot time
    // via method_exists(), which does not detect __call magic.
    // -------------------------------------------------------------------------

    // -- Stat --
    public function createStat(mixed $r, array $a, mixed $c, mixed $i): array { return $this->handleCreate('Stat', $r, $a, $c, $i); }
    public function updateStat(mixed $r, array $a, mixed $c, mixed $i): array { return $this->handleUpdate('Stat', $r, $a, $c, $i); }
    public function deleteStat(mixed $r, array $a, mixed $c, mixed $i): bool  { return $this->handleDelete('Stat', $r, $a, $c, $i); }
    public function reorderStats(mixed $r, array $a, mixed $c, mixed $i): array { return $this->handleReorder('Stat', $r, $a, $c, $i); }

    // -- PainPoint --
    public function createPainPoint(mixed $r, array $a, mixed $c, mixed $i): array { return $this->handleCreate('PainPoint', $r, $a, $c, $i); }
    public function updatePainPoint(mixed $r, array $a, mixed $c, mixed $i): array { return $this->handleUpdate('PainPoint', $r, $a, $c, $i); }
    public function deletePainPoint(mixed $r, array $a, mixed $c, mixed $i): bool  { return $this->handleDelete('PainPoint', $r, $a, $c, $i); }
    public function reorderPainPoints(mixed $r, array $a, mixed $c, mixed $i): array { return $this->handleReorder('PainPoint', $r, $a, $c, $i); }

    // -- DeliveryItem --
    public function createDeliveryItem(mixed $r, array $a, mixed $c, mixed $i): array { return $this->handleCreate('DeliveryItem', $r, $a, $c, $i); }
    public function updateDeliveryItem(mixed $r, array $a, mixed $c, mixed $i): array { return $this->handleUpdate('DeliveryItem', $r, $a, $c, $i); }
    public function deleteDeliveryItem(mixed $r, array $a, mixed $c, mixed $i): bool  { return $this->handleDelete('DeliveryItem', $r, $a, $c, $i); }
    public function reorderDeliveryItems(mixed $r, array $a, mixed $c, mixed $i): array { return $this->handleReorder('DeliveryItem', $r, $a, $c, $i); }

    // -- CapabilityGroup --
    public function createCapabilityGroup(mixed $r, array $a, mixed $c, mixed $i): array { return $this->handleCreate('CapabilityGroup', $r, $a, $c, $i); }
    public function updateCapabilityGroup(mixed $r, array $a, mixed $c, mixed $i): array { return $this->handleUpdate('CapabilityGroup', $r, $a, $c, $i); }
    public function deleteCapabilityGroup(mixed $r, array $a, mixed $c, mixed $i): bool  { return $this->handleDelete('CapabilityGroup', $r, $a, $c, $i); }
    public function reorderCapabilityGroups(mixed $r, array $a, mixed $c, mixed $i): array { return $this->handleReorder('CapabilityGroup', $r, $a, $c, $i); }

    // -- CapabilityItem --
    public function createCapabilityItem(mixed $r, array $a, mixed $c, mixed $i): array { return $this->handleCreate('CapabilityItem', $r, $a, $c, $i); }
    public function updateCapabilityItem(mixed $r, array $a, mixed $c, mixed $i): array { return $this->handleUpdate('CapabilityItem', $r, $a, $c, $i); }
    public function deleteCapabilityItem(mixed $r, array $a, mixed $c, mixed $i): bool  { return $this->handleDelete('CapabilityItem', $r, $a, $c, $i); }
    public function reorderCapabilityItems(mixed $r, array $a, mixed $c, mixed $i): array { return $this->handleReorder('CapabilityItem', $r, $a, $c, $i); }

    // -- UseCase --
    public function createUseCase(mixed $r, array $a, mixed $c, mixed $i): array { return $this->handleCreate('UseCase', $r, $a, $c, $i); }
    public function updateUseCase(mixed $r, array $a, mixed $c, mixed $i): array { return $this->handleUpdate('UseCase', $r, $a, $c, $i); }
    public function deleteUseCase(mixed $r, array $a, mixed $c, mixed $i): bool  { return $this->handleDelete('UseCase', $r, $a, $c, $i); }
    public function reorderUseCases(mixed $r, array $a, mixed $c, mixed $i): array { return $this->handleReorder('UseCase', $r, $a, $c, $i); }

    // -- ApproachStep --
    public function createApproachStep(mixed $r, array $a, mixed $c, mixed $i): array { return $this->handleCreate('ApproachStep', $r, $a, $c, $i); }
    public function updateApproachStep(mixed $r, array $a, mixed $c, mixed $i): array { return $this->handleUpdate('ApproachStep', $r, $a, $c, $i); }
    public function deleteApproachStep(mixed $r, array $a, mixed $c, mixed $i): bool  { return $this->handleDelete('ApproachStep', $r, $a, $c, $i); }
    public function reorderApproachSteps(mixed $r, array $a, mixed $c, mixed $i): array { return $this->handleReorder('ApproachStep', $r, $a, $c, $i); }

    // -- IndustryApplication --
    public function createIndustryApplication(mixed $r, array $a, mixed $c, mixed $i): array { return $this->handleCreate('IndustryApplication', $r, $a, $c, $i); }
    public function updateIndustryApplication(mixed $r, array $a, mixed $c, mixed $i): array { return $this->handleUpdate('IndustryApplication', $r, $a, $c, $i); }
    public function deleteIndustryApplication(mixed $r, array $a, mixed $c, mixed $i): bool  { return $this->handleDelete('IndustryApplication', $r, $a, $c, $i); }
    public function reorderIndustryApplications(mixed $r, array $a, mixed $c, mixed $i): array { return $this->handleReorder('IndustryApplication', $r, $a, $c, $i); }

    // -- IndustryUseCase --
    public function createIndustryUseCase(mixed $r, array $a, mixed $c, mixed $i): array { return $this->handleCreate('IndustryUseCase', $r, $a, $c, $i); }
    public function updateIndustryUseCase(mixed $r, array $a, mixed $c, mixed $i): array { return $this->handleUpdate('IndustryUseCase', $r, $a, $c, $i); }
    public function deleteIndustryUseCase(mixed $r, array $a, mixed $c, mixed $i): bool  { return $this->handleDelete('IndustryUseCase', $r, $a, $c, $i); }
    public function reorderIndustryUseCases(mixed $r, array $a, mixed $c, mixed $i): array { return $this->handleReorder('IndustryUseCase', $r, $a, $c, $i); }

    // -- Technology --
    public function createTechnology(mixed $r, array $a, mixed $c, mixed $i): array { return $this->handleCreate('Technology', $r, $a, $c, $i); }
    public function updateTechnology(mixed $r, array $a, mixed $c, mixed $i): array { return $this->handleUpdate('Technology', $r, $a, $c, $i); }
    public function deleteTechnology(mixed $r, array $a, mixed $c, mixed $i): bool  { return $this->handleDelete('Technology', $r, $a, $c, $i); }
    public function reorderTechnologies(mixed $r, array $a, mixed $c, mixed $i): array { return $this->handleReorder('Technology', $r, $a, $c, $i); }

    // -- BusinessImpact --
    public function createBusinessImpact(mixed $r, array $a, mixed $c, mixed $i): array { return $this->handleCreate('BusinessImpact', $r, $a, $c, $i); }
    public function updateBusinessImpact(mixed $r, array $a, mixed $c, mixed $i): array { return $this->handleUpdate('BusinessImpact', $r, $a, $c, $i); }
    public function deleteBusinessImpact(mixed $r, array $a, mixed $c, mixed $i): bool  { return $this->handleDelete('BusinessImpact', $r, $a, $c, $i); }
    public function reorderBusinessImpacts(mixed $r, array $a, mixed $c, mixed $i): array { return $this->handleReorder('BusinessImpact', $r, $a, $c, $i); }

    // -- Differentiator --
    public function createDifferentiator(mixed $r, array $a, mixed $c, mixed $i): array { return $this->handleCreate('Differentiator', $r, $a, $c, $i); }
    public function updateDifferentiator(mixed $r, array $a, mixed $c, mixed $i): array { return $this->handleUpdate('Differentiator', $r, $a, $c, $i); }
    public function deleteDifferentiator(mixed $r, array $a, mixed $c, mixed $i): bool  { return $this->handleDelete('Differentiator', $r, $a, $c, $i); }
    public function reorderDifferentiators(mixed $r, array $a, mixed $c, mixed $i): array { return $this->handleReorder('Differentiator', $r, $a, $c, $i); }

    // -- Feature --
    public function createFeature(mixed $r, array $a, mixed $c, mixed $i): array { return $this->handleCreate('Feature', $r, $a, $c, $i); }
    public function updateFeature(mixed $r, array $a, mixed $c, mixed $i): array { return $this->handleUpdate('Feature', $r, $a, $c, $i); }
    public function deleteFeature(mixed $r, array $a, mixed $c, mixed $i): bool  { return $this->handleDelete('Feature', $r, $a, $c, $i); }
    public function reorderFeatures(mixed $r, array $a, mixed $c, mixed $i): array { return $this->handleReorder('Feature', $r, $a, $c, $i); }

    // -- Benefit --
    public function createBenefit(mixed $r, array $a, mixed $c, mixed $i): array { return $this->handleCreate('Benefit', $r, $a, $c, $i); }
    public function updateBenefit(mixed $r, array $a, mixed $c, mixed $i): array { return $this->handleUpdate('Benefit', $r, $a, $c, $i); }
    public function deleteBenefit(mixed $r, array $a, mixed $c, mixed $i): bool  { return $this->handleDelete('Benefit', $r, $a, $c, $i); }
    public function reorderBenefits(mixed $r, array $a, mixed $c, mixed $i): array { return $this->handleReorder('Benefit', $r, $a, $c, $i); }

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
