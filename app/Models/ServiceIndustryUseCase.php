<?php

namespace App\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceIndustryUseCase extends Model implements TranslatableContract
{
    use HasFactory;
    use Translatable;

    protected $fillable = ['service_industry_application_id', 'order'];

    protected $casts = ['order' => 'integer'];

    public array $translatedAttributes = ['text'];

    public function industryApplication(): BelongsTo
    {
        return $this->belongsTo(ServiceIndustryApplication::class, 'service_industry_application_id');
    }
}
