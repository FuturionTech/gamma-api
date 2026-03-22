<?php

namespace App\GraphQL\Mutations;

use App\Models\PageView;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Jaybizzle\CrawlerDetect\CrawlerDetect;
use Jenssegers\Agent\Agent;

final class TrackPageView
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args): bool
    {
        $ip = request()->ip();

        return RateLimiter::attempt(
            'analytics:pageview:' . $ip,
            60, // max 60 attempts per minute
            function () use ($args, $ip) {
                $userAgent = request()->userAgent() ?? '';

                // Detect bots
                $crawlerDetect = new CrawlerDetect();
                $isBot = $crawlerDetect->isCrawler($userAgent);
                $botName = $isBot ? $crawlerDetect->getMatches() : null;

                // Parse user agent for structured device data
                $agent = new Agent();
                $agent->setUserAgent($userAgent);

                $deviceType = $this->resolveDeviceType($agent, $args['device_type'] ?? null);
                $browser = $agent->browser() ?: ($args['browser'] ?? null);
                $browserVersion = $agent->version($agent->browser()) ?: null;
                $os = $agent->platform() ?: ($args['os'] ?? null);
                $osVersion = $agent->version($agent->platform()) ?: null;
                $deviceBrand = $this->resolveDeviceBrand($agent);
                $deviceModel = $agent->device() ?: null;

                // Resolve country/city from IP (cached for 24h per IP)
                $geo = $this->resolveGeoLocation($ip);

                PageView::create([
                    'session_id' => $args['session_id'],
                    'path' => $args['path'],
                    'referrer' => $args['referrer'] ?? null,
                    'utm_source' => $args['utm_source'] ?? null,
                    'utm_medium' => $args['utm_medium'] ?? null,
                    'utm_campaign' => $args['utm_campaign'] ?? null,
                    'device_type' => $deviceType,
                    'browser' => $browser,
                    'browser_version' => $browserVersion ? substr($browserVersion, 0, 20) : null,
                    'os' => $os,
                    'os_version' => $osVersion ? substr($osVersion, 0, 20) : null,
                    'device_brand' => $deviceBrand ? substr($deviceBrand, 0, 50) : null,
                    'device_model' => $deviceModel ? substr($deviceModel, 0, 50) : null,
                    'screen_width' => $args['screen_width'] ?? null,
                    'language' => $args['language'] ?? null,
                    'timezone' => $args['timezone'] ?? null,
                    'country' => $geo['country'],
                    'city' => $geo['city'],
                    'duration_ms' => $args['duration_ms'] ?? null,
                    'page_load_ms' => $args['page_load_ms'] ?? null,
                    'connection_type' => $args['connection_type'] ?? null,
                    'is_bot' => $isBot,
                    'bot_name' => $botName ? substr($botName, 0, 100) : null,
                ]);

                return true;
            },
            decaySeconds: 60
        );
    }

    /**
     * Resolve device type using jenssegers/agent, falling back to client-sent value.
     */
    private function resolveDeviceType(Agent $agent, ?string $clientDeviceType): ?string
    {
        if ($agent->isPhone()) {
            return 'mobile';
        }
        if ($agent->isTablet()) {
            return 'tablet';
        }
        if ($agent->isDesktop()) {
            return 'desktop';
        }

        return $clientDeviceType;
    }

    /**
     * Attempt to resolve device brand from the user agent.
     * jenssegers/agent doesn't have a direct brand method, so we infer from known patterns.
     */
    private function resolveDeviceBrand(Agent $agent): ?string
    {
        $device = $agent->device();

        if (! $device) {
            return null;
        }

        $brandMap = [
            'iPhone' => 'Apple',
            'iPad' => 'Apple',
            'iPod' => 'Apple',
            'Macintosh' => 'Apple',
            'Samsung' => 'Samsung',
            'Galaxy' => 'Samsung',
            'Pixel' => 'Google',
            'Nexus' => 'Google',
            'HTC' => 'HTC',
            'LG' => 'LG',
            'Sony' => 'Sony',
            'Xperia' => 'Sony',
            'Huawei' => 'Huawei',
            'Xiaomi' => 'Xiaomi',
            'Redmi' => 'Xiaomi',
            'OnePlus' => 'OnePlus',
            'Motorola' => 'Motorola',
            'Nokia' => 'Nokia',
            'OPPO' => 'OPPO',
            'Vivo' => 'Vivo',
            'Realme' => 'Realme',
        ];

        foreach ($brandMap as $keyword => $brand) {
            if (stripos($device, $keyword) !== false) {
                return $brand;
            }
        }

        return null;
    }

    /**
     * Resolve country and city from IP address using ip-api.com (free, no key needed).
     * Results are cached for 24 hours per IP to minimize API calls.
     * Returns ['country' => 'CA', 'city' => 'Toronto'] or nulls on failure.
     */
    private function resolveGeoLocation(string $ip): array
    {
        $default = ['country' => null, 'city' => null];

        // Skip private/local IPs
        if (in_array($ip, ['127.0.0.1', '::1']) || str_starts_with($ip, '192.168.') || str_starts_with($ip, '10.')) {
            return $default;
        }

        return Cache::remember("geo:{$ip}", 86400, function () use ($ip, $default) {
            try {
                $response = Http::timeout(2)->get("http://ip-api.com/json/{$ip}", [
                    'fields' => 'status,countryCode,city',
                ]);

                if ($response->successful() && $response->json('status') === 'success') {
                    return [
                        'country' => $response->json('countryCode'),  // "CA", "US", "FR"
                        'city' => $response->json('city'),            // "Toronto", "Paris"
                    ];
                }
            } catch (\Throwable $e) {
                Log::debug('GeoIP lookup failed', ['ip' => $ip, 'error' => $e->getMessage()]);
            }

            return $default;
        });
    }
}
