<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Casts\Attribute;

class BlogPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image_url',
        'author_id',
        'category',
        'tags',
        'status',
        'published_at',
        'view_count',
    ];

    protected $casts = [
        'tags' => 'array',
        'published_at' => 'datetime',
        'view_count' => 'integer',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'author_id');
    }

    public function scopePublished(Builder $query): void
    {
        $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeByStatus(Builder $query, string $status): void
    {
        $query->where('status', $status);
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
        static::creating(function (BlogPost $post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
        });
    }

    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }
}

