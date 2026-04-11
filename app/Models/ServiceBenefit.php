<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceBenefit extends Model implements TranslatableContract
{
    use HasFactory;
    use Translatable;

    public array $translatedAttributes = ['title', 'description'];

    protected $fillable = [
        'service_id',
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
