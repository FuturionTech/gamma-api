<?php

namespace App\GraphQL\Queries\Analytics;

use App\Models\PageView;

final class AnalyticsTrafficSources
{
    private const SEARCH_ENGINES = [
        'google',
        'bing',
        'yahoo',
        'duckduckgo',
        'baidu',
        'yandex',
    ];

    private const SOCIAL_NETWORKS = [
        'linkedin',
        'twitter',
        'facebook',
        'instagram',
        'youtube',
        'tiktok',
        'pinterest',
        'reddit',
    ];

    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args): array
    {
        $query = PageView::query()
            ->where('is_bot', false)
            ->select('referrer', 'utm_source', 'utm_medium', 'utm_campaign');

        if (! empty($args['date_from']) && ! empty($args['date_to'])) {
            $query->betweenDates($args['date_from'], $args['date_to']);
        }

        $rows = $query->get();

        $categories = [
            'Direct' => 0,
            'Search' => 0,
            'Social' => 0,
            'Referral' => 0,
            'Campaign' => 0,
        ];

        foreach ($rows as $row) {
            $category = $this->classifySource($row->referrer, $row->utm_source, $row->utm_medium, $row->utm_campaign);
            $categories[$category]++;
        }

        $results = [];
        foreach ($categories as $name => $views) {
            if ($views > 0) {
                $results[] = [
                    'referrer' => $name,
                    'views' => $views,
                ];
            }
        }

        // Sort by views descending
        usort($results, fn ($a, $b) => $b['views'] <=> $a['views']);

        return $results;
    }

    private function classifySource(?string $referrer, ?string $utmSource, ?string $utmMedium, ?string $utmCampaign): string
    {
        // Has UTM params → Campaign
        if ($utmSource || $utmCampaign) {
            return 'Campaign';
        }

        // No referrer → Direct
        if (empty($referrer)) {
            return 'Direct';
        }

        $referrerLower = strtolower($referrer);

        // Check for search engines
        foreach (self::SEARCH_ENGINES as $engine) {
            if (str_contains($referrerLower, $engine)) {
                return 'Search';
            }
        }

        // Check for social networks
        foreach (self::SOCIAL_NETWORKS as $network) {
            if (str_contains($referrerLower, $network)) {
                return 'Social';
            }
        }

        // Everything else is a referral
        return 'Referral';
    }
}
