<?php

namespace App\GraphQL\Queries\Admin;

use App\Models\Service;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

final class ServiceForAdminQuery
{
    /** Eager-load paths used by the full service admin projection. */
    public const EAGER_LOADS = [
        'translations',
        'stats.translations',
        'painPoints.translations',
        'deliveryItems.translations',
        'capabilityGroups.translations',
        'capabilityGroups.items.translations',
        'useCases.translations',
        'approachSteps.translations',
        'industryApplications.translations',
        'industryApplications.useCases.translations',
        'technologies.translations',
        'businessImpacts.translations',
        'differentiators.translations',
        'features.translations',
        'benefits.translations',
    ];

    public function __invoke(mixed $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): ?array
    {
        $service = Service::query()
            ->with(self::EAGER_LOADS)
            ->find($args['id']);

        if (! $service) {
            return null;
        }

        return self::projectService($service);
    }

    /**
     * Project a Service model to its admin GraphQL shape (camelCase keys).
     *
     * Static so mutation resolvers can reuse the same projection.
     */
    public static function projectService(Service $service): array
    {
        return [
            'id'          => $service->id,
            'slug'        => $service->slug,
            'icon'        => $service->icon,
            'iconColor'   => $service->icon_color,
            'category'    => $service->category,
            'order'       => $service->order,
            'isActive'    => $service->is_active,
            'publishedAt' => $service->published_at,
            'translations'          => $service->translations->map(fn ($t) => self::projectTranslation($t))->all(),
            'stats'                 => $service->stats->map(fn ($c) => self::projectChild($c))->all(),
            'painPoints'            => $service->painPoints->map(fn ($c) => self::projectChild($c))->all(),
            'deliveryItems'         => $service->deliveryItems->map(fn ($c) => self::projectChild($c))->all(),
            'capabilityGroups'      => $service->capabilityGroups->map(fn ($g) => array_merge(
                self::projectChild($g),
                ['items' => $g->items->map(fn ($i) => self::projectChild($i))->all()]
            ))->all(),
            'useCases'              => $service->useCases->map(fn ($c) => self::projectChild($c))->all(),
            'approachSteps'         => $service->approachSteps->map(fn ($c) => self::projectChild($c))->all(),
            'industryApplications'  => $service->industryApplications->map(fn ($ind) => array_merge(
                self::projectChild($ind),
                ['useCases' => $ind->useCases->map(fn ($uc) => self::projectChild($uc))->all()]
            ))->all(),
            'technologies'          => $service->technologies->map(fn ($c) => self::projectChild($c))->all(),
            'businessImpacts'       => $service->businessImpacts->map(fn ($c) => self::projectChild($c))->all(),
            'differentiators'       => $service->differentiators->map(fn ($c) => self::projectChild($c))->all(),
            'features'              => $service->features->map(fn ($c) => self::projectChild($c))->all(),
            'benefits'              => $service->benefits->map(fn ($c) => self::projectChild($c))->all(),
            'createdAt'             => $service->created_at,
            'updatedAt'             => $service->updated_at,
        ];
    }

    /**
     * Project a ServiceTranslation to its admin GraphQL shape.
     */
    public static function projectTranslation(object $translation): array
    {
        return [
            'locale'               => $translation->locale,
            'title'                => $translation->title,
            'shortDescription'     => $translation->short_description,
            'description'          => $translation->description,
            'metaTitle'            => $translation->meta_title,
            'metaDescription'      => $translation->meta_description,
            'metaKeywords'         => $translation->meta_keywords,
            'heroTagline'          => $translation->hero_tagline,
            'heroHeadline'         => $translation->hero_headline,
            'heroSubheadline'      => $translation->hero_subheadline,
            'heroCtaPrimaryLabel'  => $translation->hero_cta_primary_label,
            'heroCtaSecondaryLabel' => $translation->hero_cta_secondary_label,
            'challengeTitle'       => $translation->challenge_title,
            'challengeDescription' => $translation->challenge_description,
            'deliveryTitle'        => $translation->delivery_title,
            'deliveryDescription'  => $translation->delivery_description,
            'capabilitiesTitle'    => $translation->capabilities_title,
            'useCasesTitle'        => $translation->use_cases_title,
            'useCasesDescription'  => $translation->use_cases_description,
            'approachTitle'        => $translation->approach_title,
            'approachDescription'  => $translation->approach_description,
            'industryTitle'        => $translation->industry_title,
            'industryDescription'  => $translation->industry_description,
            'technologiesTitle'    => $translation->technologies_title,
            'technologiesDescription' => $translation->technologies_description,
            'businessImpactTitle'  => $translation->business_impact_title,
            'businessImpactDescription' => $translation->business_impact_description,
            'differentiatorsTitle' => $translation->differentiators_title,
            'closingTitle'         => $translation->closing_title,
            'closingSubtitle'      => $translation->closing_subtitle,
            'publishedAt'          => $translation->published_at,
        ];
    }

    /**
     * Project a translatable child model to an array.
     *
     * Uses the translation model's $fillable to dynamically extract content
     * fields, so this works generically for any child type (stats, painPoints, etc.).
     */
    public static function projectChild(object $child): array
    {
        $result = [
            'id'    => $child->id,
            'order' => $child->order,
        ];

        if (array_key_exists('icon', $child->getAttributes())) {
            $result['icon'] = $child->icon;
        }

        $result['translations'] = $child->translations->map(function ($t) {
            $data = ['locale' => $t->locale];
            foreach ($t->getFillable() as $field) {
                $data[$field] = $t->{$field};
            }

            return $data;
        })->all();

        return $result;
    }
}
