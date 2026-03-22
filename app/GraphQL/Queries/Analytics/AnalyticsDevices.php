<?php

namespace App\GraphQL\Queries\Analytics;

use App\Models\PageView;

final class AnalyticsDevices
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args): array
    {
        $query = PageView::query()
            ->where('is_bot', false)
            ->select('device_type')
            ->selectRaw('COUNT(*) as count')
            ->whereNotNull('device_type')
            ->where('device_type', '!=', '')
            ->groupBy('device_type')
            ->orderByDesc('count');

        if (! empty($args['date_from']) && ! empty($args['date_to'])) {
            $query->betweenDates($args['date_from'], $args['date_to']);
        }

        $results = $query->get();
        $total = $results->sum('count');

        return $results->map(fn ($row) => [
            'device_type' => $row->device_type,
            'count' => (int) $row->count,
            'percentage' => $total > 0 ? round(((int) $row->count / $total) * 100, 2) : 0.0,
        ])->toArray();
    }
}
