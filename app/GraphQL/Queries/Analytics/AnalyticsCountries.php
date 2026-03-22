<?php

namespace App\GraphQL\Queries\Analytics;

use App\Helpers\CountryNames;
use App\Models\PageView;

final class AnalyticsCountries
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
            ->select('country')
            ->selectRaw('COUNT(*) as views')
            ->selectRaw('COUNT(DISTINCT session_id) as unique_visitors')
            ->whereNotNull('country')
            ->where('country', '!=', '')
            ->groupBy('country')
            ->orderByDesc('views')
            ->limit($limit);

        if (! empty($args['date_from']) && ! empty($args['date_to'])) {
            $query->betweenDates($args['date_from'], $args['date_to']);
        }

        $results = $query->get();

        $totalViews = $results->sum('views');

        return $results->map(fn ($row) => [
            'country' => $row->country,
            'country_name' => CountryNames::getName($row->country),
            'views' => (int) $row->views,
            'unique_visitors' => (int) $row->unique_visitors,
            'percentage' => $totalViews > 0
                ? round(((int) $row->views / $totalViews) * 100, 1)
                : 0.0,
        ])->toArray();
    }
}
