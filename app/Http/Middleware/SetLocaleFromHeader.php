<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromHeader
{
    private const SUPPORTED_LOCALES = ['en', 'fr'];
    private const DEFAULT_LOCALE = 'en';

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolveLocale($request);

        App::setLocale($locale);

        return $next($request);
    }

    private function resolveLocale(Request $request): string
    {
        // X-Locale takes priority over Accept-Language
        $xLocale = $request->header('X-Locale');
        if (!empty($xLocale)) {
            $candidate = strtolower(explode('-', $xLocale)[0]);
            if (in_array($candidate, self::SUPPORTED_LOCALES, true)) {
                return $candidate;
            }
        }

        $acceptLanguage = $request->header('Accept-Language');
        if (!empty($acceptLanguage)) {
            $primaryLang = strtolower(explode(',', $acceptLanguage)[0]);
            $candidate = explode('-', $primaryLang)[0]; // "fr-CA" -> "fr"
            if (in_array($candidate, self::SUPPORTED_LOCALES, true)) {
                return $candidate;
            }
        }

        return self::DEFAULT_LOCALE;
    }
}
