<?php

namespace App\GraphQL\Queries\Analytics;

use App\Models\PageView;

final class AnalyticsBrowsers
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args): array
    {
        $query = PageView::query()
            ->where('is_bot', false)
            ->select('browser')
            ->selectRaw('COUNT(*) as count')
            ->whereNotNull('browser')
            ->where('browser', '!=', '')
            ->groupBy('browser')
            ->orderByDesc('count');

        if (! empty($args['date_from']) && ! empty($args['date_to'])) {
            $query->betweenDates($args['date_from'], $args['date_to']);
        }

        return $query->get()->map(fn ($row) => [
            'browser' => $row->browser,
            'count' => (int) $row->count,
        ])->toArray();
    }
}
