<?php

namespace App\GraphQL\Queries\Analytics;

use App\Models\PageView;
use Carbon\Carbon;

final class AnalyticsOverview
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args): array
    {
        $query = PageView::query()->where('is_bot', false);

        if (! empty($args['date_from']) && ! empty($args['date_to'])) {
            $query->betweenDates($args['date_from'], $args['date_to']);
        }

        $totalViews = (clone $query)->count();
        $uniqueVisitors = (clone $query)->distinct('session_id')->count('session_id');
        $uniqueSessions = (clone $query)->distinct('session_id')->count('session_id');
        $avgDuration = (clone $query)->whereNotNull('duration_ms')->avg('duration_ms');
        $avgPageLoad = (clone $query)->whereNotNull('page_load_ms')->avg('page_load_ms');

        $viewsToday = PageView::today()->where('is_bot', false)->count();
        $viewsThisWeek = PageView::thisWeek()->where('is_bot', false)->count();
        $viewsThisMonth = PageView::thisMonth()->where('is_bot', false)->count();

        // Bot views count (unfiltered by date range for overall awareness)
        $botQuery = PageView::query()->where('is_bot', true);
        if (! empty($args['date_from']) && ! empty($args['date_to'])) {
            $botQuery->betweenDates($args['date_from'], $args['date_to']);
        }
        $botViews = $botQuery->count();

        // Pages per session = total views / unique sessions
        $pagesPerSession = $uniqueSessions > 0
            ? round($totalViews / $uniqueSessions, 2)
            : null;

        return [
            'total_views' => $totalViews,
            'unique_visitors' => $uniqueVisitors,
            'views_today' => $viewsToday,
            'views_this_week' => $viewsThisWeek,
            'views_this_month' => $viewsThisMonth,
            'avg_duration_ms' => $avgDuration ? (int) round($avgDuration) : null,
            'pages_per_session' => $pagesPerSession,
            'avg_page_load_ms' => $avgPageLoad ? (int) round($avgPageLoad) : null,
            'bot_views' => $botViews,
        ];
    }
}
