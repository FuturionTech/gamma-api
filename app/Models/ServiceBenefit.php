<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceBenefit extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'title',
        'description',
        'icon',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
