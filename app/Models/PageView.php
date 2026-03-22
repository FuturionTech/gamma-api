<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class PageView extends Model
{
    public $timestamps = false;

    const CREATED_AT = 'created_at';

    protected $fillable = [
        'session_id',
        'path',
        'referrer',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'device_type',
        'browser',
        'os',
        'screen_width',
        'language',
        'country',
        'city',
        'duration_ms',
        'is_bot',
        'bot_name',
        'browser_version',
        'os_version',
        'device_brand',
        'device_model',
        'timezone',
        'page_load_ms',
        'connection_type',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'screen_width' => 'integer',
        'duration_ms' => 'integer',
        'is_bot' => 'boolean',
        'page_load_ms' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (PageView $pageView) {
            if (! $pageView->created_at) {
                $pageView->created_at = Carbon::now();
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
