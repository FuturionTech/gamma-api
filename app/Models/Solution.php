<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Solution extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'slug',
        'industry_category',
        'icon',
        'icon_color',
        'hero_image_url',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    // Relationships
    public function features(): HasMany
    {
        return $this->hasMany(SolutionFeature::class)->orderBy('order');
    }

    public function benefits(): HasMany
    {
        return $this->hasMany(SolutionBenefit::class)->orderBy('order');
    }

    // Scopes
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    // Accessors
    protected function heroImageUrl(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value && !filter_var($value, FILTER_VALIDATE_URL) 
                ? Storage::disk('s3')->url($value) 
                : $value,
        );
    }

    // Events
    protected static function booted(): void
    {
        static::creating(function (Solution $solution) {
            if (empty($solution->slug)) {
                $solution->slug = Str::slug($solution->title);
            }
        });
    }
}

