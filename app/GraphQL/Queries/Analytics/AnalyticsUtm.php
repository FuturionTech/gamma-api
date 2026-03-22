<?php

namespace App\GraphQL\Queries\Analytics;

use App\Models\PageView;

final class AnalyticsUtm
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
            ->select('utm_source', 'utm_medium', 'utm_campaign')
            ->selectRaw('COUNT(*) as views')
            ->whereNotNull('utm_source')
            ->where('utm_source', '!=', '')
            ->groupBy('utm_source', 'utm_medium', 'utm_campaign')
            ->orderByDesc('views')
            ->limit($limit);

        if (! empty($args['date_from']) && ! empty($args['date_to'])) {
            $query->betweenDates($args['date_from'], $args['date_to']);
        }

        return $query->get()->map(fn ($row) => [
            'source' => $row->utm_source,
            'medium' => $row->utm_medium,
            'campaign' => $row->utm_campaign,
            'views' => (int) $row->views,
        ])->toArray();
    }
}
