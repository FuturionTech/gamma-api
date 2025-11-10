<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ProcessStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'short_description',
        'step_number',
        'icon',
        'icon_color',
        'slug',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'step_number' => 'integer',
        'order' => 'integer',
    ];

    // Relationships
    public function items(): HasMany
    {
        return $this->hasMany(ProcessStepItem::class)->orderBy('order');
    }

    // Scopes
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('step_number')->orderBy('order');
    }

    // Events
    protected static function booted(): void
    {
        static::creating(function (ProcessStep $processStep) {
            if (empty($processStep->slug)) {
                $processStep->slug = Str::slug($processStep->title);
            }
        });
    }
}
