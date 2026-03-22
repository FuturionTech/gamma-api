<?php

namespace App\GraphQL\Queries\Analytics;

use App\Models\PageView;
use Illuminate\Support\Facades\DB;

final class AnalyticsTopPages
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
            ->select('path')
            ->selectRaw('COUNT(*) as views')
            ->selectRaw('COUNT(DISTINCT session_id) as unique_visitors')
            ->groupBy('path')
            ->orderByDesc('views')
            ->limit($limit);

        if (! empty($args['date_from']) && ! empty($args['date_to'])) {
            $query->betweenDates($args['date_from'], $args['date_to']);
        }

        return $query->get()->map(fn ($row) => [
            'path' => $row->path,
            'views' => (int) $row->views,
            'unique_visitors' => (int) $row->unique_visitors,
        ])->toArray();
    }
}
