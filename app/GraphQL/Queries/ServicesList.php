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
     * If a caller requests a nested section field on a list result, we return
     * null/empty placeholders for the optional sections so GraphQL validation
     * doesn't fail. Callers who need the full grouped tree should use the
     * `service(slug: ...)` query.
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
            // marked non-nullable in the schema. Return minimal shapes with the
            // scalar fields populated in case the UI renders them on a card.
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
