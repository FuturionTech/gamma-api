<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SocialMediaPlatform extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'icon',
        'base_url',
    ];

    public function teamSocialMediaLinks(): HasMany
    {
        return $this->hasMany(TeamSocialMediaLink::class, 'platform_id');
    }
}

