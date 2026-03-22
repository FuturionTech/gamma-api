<?php

namespace App\GraphQL\Queries\Analytics;

use App\Models\PageView;

final class AnalyticsReferrers
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args): array
    {
        $limit = $args['limit'] ?? 20;

        $query = PageView::query()
            ->where('is_bot', false)
            ->select('referrer')
            ->selectRaw('COUNT(*) as views')
            ->whereNotNull('referrer')
            ->where('referrer', '!=', '')
            ->groupBy('referrer')
            ->orderByDesc('views')
            ->limit($limit);

        if (! empty($args['date_from']) && ! empty($args['date_to'])) {
            $query->betweenDates($args['date_from'], $args['date_to']);
        }

        return $query->get()->map(fn ($row) => [
            'referrer' => $row->referrer,
            'views' => (int) $row->views,
        ])->toArray();
    }
}
