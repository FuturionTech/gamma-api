<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Partner extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'logo_url',
        'website_url',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    protected function logoUrl(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value && !filter_var($value, FILTER_VALIDATE_URL) 
                ? Storage::disk('s3')->url($value) 
                : $value,
        );
    }
}

