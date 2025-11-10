<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'logo_url',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    // Relationships
    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function solutions(): HasMany
    {
        return $this->hasMany(Solution::class);
    }

    public function partners(): HasMany
    {
        return $this->hasMany(Partner::class);
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class);
    }

    public function banners(): HasMany
    {
        return $this->hasMany(Banner::class);
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function certifications(): HasMany
    {
        return $this->hasMany(Certification::class);
    }

    public function jobPositions(): HasMany
    {
        return $this->hasMany(JobPosition::class);
    }

    public function contactRequests(): HasMany
    {
        return $this->hasMany(ContactRequest::class);
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(FAQ::class);
    }

    public function blogPosts(): HasMany
    {
        return $this->hasMany(BlogPost::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function stats(): HasMany
    {
        return $this->hasMany(Stat::class);
    }
}

