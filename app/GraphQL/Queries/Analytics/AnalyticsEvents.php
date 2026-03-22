<?php

namespace App\GraphQL\Queries\Analytics;

use App\Models\AnalyticsEvent;

final class AnalyticsEvents
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args): array
    {
        $limit = $args['limit'] ?? 20;

        $query = AnalyticsEvent::query()
            ->select('event_name')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('event_name')
            ->orderByDesc('count')
            ->limit($limit);

        if (! empty($args['date_from']) && ! empty($args['date_to'])) {
            $query->betweenDates($args['date_from'], $args['date_to']);
        }

        return $query->get()->map(fn ($row) => [
            'event_name' => $row->event_name,
            'count' => (int) $row->count,
        ])->toArray();
    }
}
