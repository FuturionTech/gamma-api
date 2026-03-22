<?php

namespace App\GraphQL\Mutations;

use App\Models\AnalyticsEvent;
use Illuminate\Support\Facades\RateLimiter;

final class TrackEvent
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args): bool
    {
        $ip = request()->ip();

        return RateLimiter::attempt(
            'analytics:event:' . $ip,
            120, // max 120 attempts
            function () use ($args) {
                AnalyticsEvent::create([
                    'session_id' => $args['session_id'],
                    'event_name' => $args['event_name'],
                    'event_data' => $args['event_data'] ?? null,
                    'path' => $args['path'],
                ]);

                return true;
            },
            decaySeconds: 60
        );
    }
}
