<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceApproachStep extends Model implements TranslatableContract
{
    use HasFactory;
    use Translatable;

    protected $fillable = ['service_id', 'icon', 'order'];

    protected $casts = ['order' => 'integer'];

    public array $translatedAttributes = ['title', 'description'];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
