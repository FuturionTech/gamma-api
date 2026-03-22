<?php

namespace App\GraphQL\Queries\Analytics;

use App\Models\PageView;

final class AnalyticsCities
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
            ->select('city', 'country')
            ->selectRaw('COUNT(*) as views')
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->whereNotNull('country')
            ->groupBy('city', 'country')
            ->orderByDesc('views')
            ->limit($limit);

        if (! empty($args['date_from']) && ! empty($args['date_to'])) {
            $query->betweenDates($args['date_from'], $args['date_to']);
        }

        return $query->get()->map(fn ($row) => [
            'city' => $row->city,
            'country' => $row->country,
            'views' => (int) $row->views,
        ])->toArray();
    }
}
