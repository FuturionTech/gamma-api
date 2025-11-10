<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamSocialMediaLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'platform_id',
        'url',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function platform(): BelongsTo
    {
        return $this->belongsTo(SocialMediaPlatform::class, 'platform_id');
    }
}

