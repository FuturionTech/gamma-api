<?php

namespace App\Models;

use App\Jobs\SendContactRequestNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ContactRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'subject',
        'message',
        'project_type',
        'status',
        'locale',
    ];

    protected static function booted(): void
    {
        static::creating(function (ContactRequest $contactRequest) {
            if (empty($contactRequest->locale)) {
                $contactRequest->locale = app()->getLocale();
            }
        });

        static::created(function (ContactRequest $contactRequest) {
            SendContactRequestNotification::dispatch($contactRequest);
        });
    }

    public function scopeByStatus(Builder $query, string $status): void
    {
        $query->where('status', $status);
    }
}

