<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\Eloquent\Model;
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

        if ($queryLang !== null) {
            $this->rememberLanguage($user, $queryLang);
        }

        $locale = $queryLang
            ?? $this->pick($user?->language ?? null, $supported)
            ?? $this->pick(Session::get('locale'), $supported)
            ?? $fallback;

        if (App::getLocale() !== $locale) {
            App::setLocale($locale);
        }

        return $next($request);
    }

    /**
     * Stores a language picked with `?lang=` in the session and, for a signed-in
     * user, on their profile, so the saved language and the session never disagree.
     */
    protected function rememberLanguage(mixed $user, string $language): void
    {
        if (Session::get('locale') !== $language) {
            Session::put('locale', $language);
        }

        if (
            $user instanceof Model
            && in_array('language', $user->getFillable(), true)
            && $user->getAttribute('language') !== $language
        ) {
            $user->forceFill(['language' => $language])->saveQuietly();
        }
    }

    protected function pick(mixed $candidate, array $supported): ?string
    {
        return is_string($candidate) && in_array($candidate, $supported, true)
            ? $candidate
            : null;
    }
}
