<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Certification extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'file_url',
        'certification_category_id',
        'issued_date',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'issued_date' => 'date',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(CertificationCategory::class, 'certification_category_id');
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    protected function fileUrl(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value && !filter_var($value, FILTER_VALIDATE_URL) 
                ? Storage::disk('s3')->url($value) 
                : $value,
        );
    }
}

