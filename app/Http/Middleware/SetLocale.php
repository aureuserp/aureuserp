<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supported = array_keys(config('app.supported_locales', []));

        $fallback = $this->pick(config('app.locale'), $supported)
            ?? $this->pick(config('app.fallback_locale'), $supported)
            ?? ($supported[0] ?? 'en');

        $queryLang = $this->pick($request->query('lang'), $supported);

        $user = $request->user();

        $locale = $queryLang
            ?? $this->pick(Session::get('locale'), $supported)
            ?? $this->pick($user?->language ?? null, $supported)
            ?? $fallback;

        if ($queryLang !== null && Session::get('locale') !== $queryLang) {
            Session::put('locale', $queryLang);
        }

        if (App::getLocale() !== $locale) {
            App::setLocale($locale);
        }

        return $next($request);
    }

    protected function pick(mixed $candidate, array $supported): ?string
    {
        return is_string($candidate) && in_array($candidate, $supported, true)
            ? $candidate
            : null;
    }
}
