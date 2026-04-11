<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceCapabilityItem extends Model implements TranslatableContract
{
    use HasFactory;
    use Translatable;

    protected $fillable = ['service_capability_group_id', 'order'];

    protected $casts = ['order' => 'integer'];

    public array $translatedAttributes = ['name'];

    public function group(): BelongsTo
    {
        return $this->belongsTo(ServiceCapabilityGroup::class, 'service_capability_group_id');
    }
}
