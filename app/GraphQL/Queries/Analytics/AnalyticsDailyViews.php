<?php

namespace App\GraphQL\Queries\Analytics;

use App\Models\PageView;
use Illuminate\Support\Facades\DB;

final class AnalyticsDailyViews
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args): array
    {
        $query = PageView::query()
            ->where('is_bot', false)
            ->select(DB::raw("DATE(created_at) as date"))
            ->selectRaw('COUNT(*) as views')
            ->selectRaw('COUNT(DISTINCT session_id) as unique_visitors')
            ->groupBy('date')
            ->orderBy('date');

        if (! empty($args['date_from']) && ! empty($args['date_to'])) {
            $query->betweenDates($args['date_from'], $args['date_to']);
        }

        return $query->get()->map(fn ($row) => [
            'date' => $row->date,
            'views' => (int) $row->views,
            'unique_visitors' => (int) $row->unique_visitors,
        ])->toArray();
    }
}
