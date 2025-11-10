<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SolutionFeature extends Model
{
    use HasFactory;

    protected $fillable = [
        'solution_id',
        'title',
        'description',
        'icon',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    public function solution(): BelongsTo
    {
        return $this->belongsTo(Solution::class);
    }
}

