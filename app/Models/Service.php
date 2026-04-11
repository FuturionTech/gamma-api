<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Service extends Model
{
    use HasFactory;
    use HasTranslations;

    public array $translatable = ['title', 'description', 'short_description'];

    protected $fillable = [
        'title',
        'description',
        'short_description',
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
        static::creating(function (Service $service) {
            if (empty($service->slug)) {
                $service->slug = Str::slug($service->getTranslation('title', 'en'));
            }
        });
    }
}
