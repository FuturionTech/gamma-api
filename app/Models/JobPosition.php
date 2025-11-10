<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class JobPosition extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'department',
        'location',
        'job_type',
        'is_remote',
        'salary_range',
        'experience_required',
        'summary',
        'description',
        'responsibilities',
        'requirements',
        'nice_to_have',
        'benefits',
        'skills',
        'posted_date',
        'status',
    ];

    protected $casts = [
        'is_remote' => 'boolean',
        'responsibilities' => 'array',
        'requirements' => 'array',
        'nice_to_have' => 'array',
        'benefits' => 'array',
        'skills' => 'array',
        'posted_date' => 'date',
    ];

    public function scopeActive(Builder $query): void
    {
        $query->where('status', 'active');
    }

    public function scopeByStatus(Builder $query, string $status): void
    {
        $query->where('status', $status);
    }
}

