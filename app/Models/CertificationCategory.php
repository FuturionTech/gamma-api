<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CertificationCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function certifications(): HasMany
    {
        return $this->hasMany(Certification::class);
    }
}

