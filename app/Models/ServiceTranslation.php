<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceTranslation extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'title',
        'short_description',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'hero_tagline',
        'hero_headline',
        'hero_subheadline',
        'hero_cta_primary_label',
        'hero_cta_secondary_label',
        'challenge_title',
        'challenge_description',
        'delivery_title',
        'delivery_description',
        'capabilities_title',
        'use_cases_title',
        'use_cases_description',
        'approach_title',
        'approach_description',
        'industry_title',
        'industry_description',
        'technologies_title',
        'technologies_description',
        'business_impact_title',
        'business_impact_description',
        'differentiators_title',
        'closing_title',
        'closing_subtitle',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];
}
