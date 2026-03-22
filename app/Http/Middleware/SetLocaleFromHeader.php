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
        $header = $request->header('Accept-Language');

        if (!empty($header)) {
            $primaryLang = strtolower(explode(',', $header)[0]);
            $locale = explode('-', $primaryLang)[0]; // "fr-CA" -> "fr"

            if (!in_array($locale, self::SUPPORTED_LOCALES, true)) {
                $locale = self::DEFAULT_LOCALE;
            }
        } else {
            $locale = self::DEFAULT_LOCALE;
        }

        App::setLocale($locale);

        return $next($request);
    }
}
