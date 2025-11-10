<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'challenge',
        'solution',
        'results',
        'featured_image_url',
        'gallery_images',
        'client_name',
        'industry',
        'technologies',
        'status',
        'completion_date',
    ];

    protected $casts = [
        'gallery_images' => 'array',
        'technologies' => 'array',
        'completion_date' => 'date',
    ];

    public function scopePublished(Builder $query): void
    {
        $query->where('status', 'published');
    }

    protected function featuredImageUrl(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value && !filter_var($value, FILTER_VALIDATE_URL) 
                ? Storage::disk('s3')->url($value) 
                : $value,
        );
    }

    protected static function booted(): void
    {
        static::creating(function (Project $project) {
            if (empty($project->slug)) {
                $project->slug = Str::slug($project->title);
            }
        });
    }
}

