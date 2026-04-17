<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class CertificationCategory extends Model
{
    use HasFactory;
    use HasTranslations;

    public array $translatable = ['name'];

    protected $fillable = [
        'name',
    ];

    public function certifications(): HasMany
    {
        return $this->hasMany(Certification::class);
    }
}

