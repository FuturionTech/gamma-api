<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceCapabilityGroup extends Model implements TranslatableContract
{
    use HasFactory;
    use Translatable;

    protected $fillable = ['service_id', 'icon', 'order'];

    protected $casts = ['order' => 'integer'];

    public array $translatedAttributes = ['name'];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ServiceCapabilityItem::class)->orderBy('order');
    }
}
