<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceStatTranslation extends Model
{
    public $timestamps = true;

    protected $fillable = ['value', 'label'];
}
