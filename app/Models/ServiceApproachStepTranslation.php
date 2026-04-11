<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceApproachStepTranslation extends Model
{
    public $timestamps = true;

    protected $fillable = ['title', 'description'];
}
