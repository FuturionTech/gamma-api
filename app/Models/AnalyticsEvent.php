<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class AnalyticsEvent extends Model
{
    public $timestamps = false;

    const CREATED_AT = 'created_at';

    protected $fillable = [
        'session_id',
        'event_name',
        'event_data',
        'path',
    ];

    protected $casts = [
        'event_data' => 'array',
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (AnalyticsEvent $event) {
            if (! $event->created_at) {
                $event->created_at = Carbon::now();
            }
        });
    }

    public function scopeToday(Builder $query): void
    {
        $query->whereDate('created_at', Carbon::today());
    }

    public function scopeThisWeek(Builder $query): void
    {
        $query->where('created_at', '>=', Carbon::now()->startOfWeek());
    }

    public function scopeThisMonth(Builder $query): void
    {
        $query->where('created_at', '>=', Carbon::now()->startOfMonth());
    }

    public function scopeBetweenDates(Builder $query, string $from, string $to): void
    {
        $query->whereBetween('created_at', [
            Carbon::parse($from)->startOfDay(),
            Carbon::parse($to)->endOfDay(),
        ]);
    }
}
