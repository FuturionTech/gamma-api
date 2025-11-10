<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcessStepItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'process_step_id',
        'title',
        'description',
        'icon',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    public function processStep(): BelongsTo
    {
        return $this->belongsTo(ProcessStep::class);
    }
}
