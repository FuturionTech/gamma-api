<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Service extends Model implements TranslatableContract
{
    use HasFactory;
    use Translatable;

    public array $translatedAttributes = ['title', 'description', 'short_description'];

    protected $fillable = [
        'icon',
        'icon_color',
        'category',
        'slug',
        'order',
        'is_active',
        'published_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
        'published_at' => 'datetime',
    ];

    // Relationships
    public function features(): HasMany
    {
        return $this->hasMany(ServiceFeature::class)->orderBy('order');
    }

    public function benefits(): HasMany
    {
        return $this->hasMany(ServiceBenefit::class)->orderBy('order');
    }

    public function stats(): HasMany
    {
        return $this->hasMany(ServiceStat::class)->orderBy('order');
    }

    public function painPoints(): HasMany
    {
        return $this->hasMany(ServicePainPoint::class)->orderBy('order');
    }

    public function deliveryItems(): HasMany
    {
        return $this->hasMany(ServiceDeliveryItem::class)->orderBy('order');
    }

    public function capabilityGroups(): HasMany
    {
        return $this->hasMany(ServiceCapabilityGroup::class)->orderBy('order');
    }

    public function useCases(): HasMany
    {
        return $this->hasMany(ServiceUseCase::class)->orderBy('order');
    }

    public function approachSteps(): HasMany
    {
        return $this->hasMany(ServiceApproachStep::class)->orderBy('order');
    }

    public function industryApplications(): HasMany
    {
        return $this->hasMany(ServiceIndustryApplication::class)->orderBy('order');
    }

    public function technologies(): HasMany
    {
        return $this->hasMany(ServiceTechnology::class)->orderBy('order');
    }

    public function businessImpacts(): HasMany
    {
        return $this->hasMany(ServiceBusinessImpact::class)->orderBy('order');
    }

    public function differentiators(): HasMany
    {
        return $this->hasMany(ServiceDifferentiator::class)->orderBy('order');
    }

    // Scopes
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('order');
    }

    // Events
    protected static function booted(): void
    {
        static::saving(function (Service $service) {
            // Auto-slug from the EN title if the slug is empty
            if (empty($service->slug)) {
                $en = $service->translate('en', false);
                if ($en && ! empty($en->title)) {
                    $service->slug = Str::slug($en->title);
                } else {
                    // Fallback: use the current locale's title or a uuid-ish default
                    $fallback = $service->title ?? 'service-'.uniqid();
                    $service->slug = Str::slug($fallback);
                }
            }
        });
    }
}
