<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceBusinessImpactTranslation extends Model
{
    public $timestamps = true;

    protected $fillable = ['title', 'description'];
}
