<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceBenefitTranslation extends Model
{
    public $timestamps = true;

    protected $fillable = ['title', 'description'];
}
