<?php

namespace App\GraphQL\Queries;

use App\Models\Service;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

final class ServiceDetail
{
    /**
     * Resolve the `service(slug: String!)` query.
     *
     * Loads a single service by slug with every relation eager-loaded,
     * then projects the flat service_translations columns and child
     * collections into the grouped tree shape the frontend expects.
     *
     * Locale is resolved from the SetLocaleFromHeader middleware,
     * so calling $service->title implicitly returns the active locale
     * via astrotomic's property fallback.
     */
    public function __invoke(mixed $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): ?array
    {
        /** @var Service|null $service */
        $service = Service::query()
            ->with([
                'stats',
                'painPoints',
                'deliveryItems',
                'capabilityGroups.items',
                'useCases',
                'approachSteps',
                'industryApplications.useCases',
                'technologies',
                'businessImpacts',
                'differentiators',
                'features',
                'benefits',
            ])
            ->where('slug', $args['slug'])
            ->where('is_active', true)
            ->first();

        if ($service === null) {
            return null;
        }

        return $this->project($service);
    }

    private function project(Service $service): array
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

            'hero' => $this->projectHero($service),
            'challenge' => $this->projectChallenge($service),
            'howWeDeliver' => $this->projectHowWeDeliver($service),
            'capabilities' => $this->projectCapabilities($service),
            'keyUseCases' => $this->projectKeyUseCases($service),
            'ourApproach' => $this->projectOurApproach($service),
            'industryApplications' => $this->projectIndustryApplications($service),
            'technologies' => $this->projectTechnologies($service),
            'businessImpact' => $this->projectBusinessImpact($service),
            'differentiators' => $this->projectDifferentiators($service),
            'closing' => $this->projectClosing($service),

            'features' => $service->features->all(),
            'benefits' => $service->benefits->all(),

            'created_at' => $service->created_at,
            'updated_at' => $service->updated_at,
        ];
    }

    private function projectHero(Service $service): array
    {
        return [
            'tagline' => $service->hero_tagline,
            'headline' => $service->hero_headline,
            'subheadline' => $service->hero_subheadline,
            'ctaPrimaryLabel' => $service->hero_cta_primary_label,
            'ctaSecondaryLabel' => $service->hero_cta_secondary_label,
            'stats' => $service->stats->map(fn ($stat) => [
                'icon' => $stat->icon,
                'value' => $stat->value,
                'label' => $stat->label,
            ])->all(),
        ];
    }

    private function projectChallenge(Service $service): array
    {
        return [
            'title' => $service->challenge_title,
            'description' => $service->challenge_description,
            'painPoints' => $service->painPoints->map(fn ($p) => ['text' => $p->text])->all(),
        ];
    }

    private function projectHowWeDeliver(Service $service): array
    {
        return [
            'title' => $service->delivery_title,
            'description' => $service->delivery_description,
            'items' => $service->deliveryItems->map(fn ($i) => [
                'icon' => $i->icon,
                'text' => $i->text,
            ])->all(),
        ];
    }

    private function projectCapabilities(Service $service): array
    {
        return [
            'title' => $service->capabilities_title,
            'groups' => $service->capabilityGroups->map(fn ($group) => [
                'icon' => $group->icon,
                'name' => $group->name,
                'items' => $group->items->map(fn ($item) => ['name' => $item->name])->all(),
            ])->all(),
        ];
    }

    private function projectKeyUseCases(Service $service): array
    {
        return [
            'title' => $service->use_cases_title,
            'description' => $service->use_cases_description,
            'items' => $service->useCases->map(fn ($uc) => ['text' => $uc->text])->all(),
        ];
    }

    private function projectOurApproach(Service $service): array
    {
        return [
            'title' => $service->approach_title,
            'description' => $service->approach_description,
            'items' => $service->approachSteps->map(fn ($step) => [
                'icon' => $step->icon,
                'title' => $step->title,
                'description' => $step->description,
            ])->all(),
        ];
    }

    private function projectIndustryApplications(Service $service): array
    {
        return [
            'title' => $service->industry_title,
            'description' => $service->industry_description,
            'industries' => $service->industryApplications->map(fn ($ind) => [
                'icon' => $ind->icon,
                'name' => $ind->name,
                'description' => $ind->description,
                'useCases' => $ind->useCases->map(fn ($uc) => ['text' => $uc->text])->all(),
            ])->all(),
        ];
    }

    private function projectTechnologies(Service $service): array
    {
        return [
            'title' => $service->technologies_title,
            'description' => $service->technologies_description,
            'items' => $service->technologies->map(fn ($t) => [
                'icon' => $t->icon,
                'name' => $t->name,
            ])->all(),
        ];
    }

    private function projectBusinessImpact(Service $service): array
    {
        return [
            'title' => $service->business_impact_title,
            'description' => $service->business_impact_description,
            'items' => $service->businessImpacts->map(fn ($b) => [
                'icon' => $b->icon,
                'title' => $b->title,
                'description' => $b->description,
            ])->all(),
        ];
    }

    private function projectDifferentiators(Service $service): array
    {
        return [
            'title' => $service->differentiators_title,
            'points' => $service->differentiators->map(fn ($d) => [
                'icon' => $d->icon,
                'title' => $d->title,
                'description' => $d->description,
            ])->all(),
        ];
    }

    private function projectClosing(Service $service): array
    {
        return [
            'title' => $service->closing_title,
            'subtitle' => $service->closing_subtitle,
        ];
    }
}
