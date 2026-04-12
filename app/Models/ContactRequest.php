<?php

namespace App\Models;

use App\Mail\ContactRequestConfirmation;
use App\Mail\ContactRequestReceived;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;

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
            // Send emails synchronously — no queue/Horizon needed
            Mail::to(config('mail.admin_email'))->send(
                new ContactRequestReceived($contactRequest)
            );

            Mail::to($contactRequest->email)->send(
                new ContactRequestConfirmation($contactRequest)
            );
        });
    }

    public function scopeByStatus(Builder $query, string $status): void
    {
        $query->where('status', $status);
    }
}

