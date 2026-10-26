<?php

namespace Webkul\Website\Services;

use DateTimeZone;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;
use Webkul\Support\Models\Country;
use Webkul\Website\Events\VisitorTracked;
use Webkul\Website\Models\Page;
use Webkul\Website\Models\Track;
use Webkul\Website\Models\Visitor;

class VisitorTracker
{
    public const COOKIE = 'website_visitor';

    public const TIMEZONE_COOKIE = 'website_tz';

    public const NEW_VISIT_HOURS = 8;

    public const TRACK_AGAIN_MINUTES = 30;

    public const TIMEZONE_ALIASES = [
        'Asia/Calcutta'        => 'Asia/Kolkata',
        'Asia/Katmandu'        => 'Asia/Kathmandu',
        'Asia/Saigon'          => 'Asia/Ho_Chi_Minh',
        'Asia/Rangoon'         => 'Asia/Yangon',
        'Europe/Kiev'          => 'Europe/Kyiv',
        'America/Buenos_Aires' => 'America/Argentina/Buenos_Aires',
        'Atlantic/Faeroe'      => 'Atlantic/Faroe',
        'Pacific/Truk'         => 'Pacific/Chuuk',
        'Pacific/Ponape'       => 'Pacific/Pohnpei',
    ];

    public const BOT_PATTERN = '/bot|crawl|spider|slurp|facebookexternalhit|embedly|preview|headless|lighthouse|pingdom|monitor|curl|wget|python-requests|httpclient/i';

    public function __construct(protected IpGeolocation $geolocation) {}

    public static function isBot(?string $userAgent): bool
    {
        return blank($userAgent) || (bool) preg_match(self::BOT_PATTERN, $userAgent);
    }

    public function track(Request $request, ?Model $partner, ?Page $page): ?Visitor
    {
        if (static::isBot($request->userAgent())) {
            return null;
        }

        $visitor = DB::transaction(fn (): Visitor => $this->upsertVisitor($request, $partner));

        if (! $page || $page->is_tracked) {
            $this->addTrack($visitor, $page, $request->fullUrl());
        }

        VisitorTracked::dispatch($visitor, $request->ip(), $request->fullUrl(), $page);

        return $visitor;
    }

    public function linkPartner(Model $partner, ?string $cookieToken): ?Visitor
    {
        return DB::transaction(function () use ($partner, $cookieToken): ?Visitor {
            $anonymous = $this->anonymousVisitor($cookieToken);
            $own = Visitor::query()->where('access_token', $this->partnerToken($partner))->first();

            if (! $anonymous) {
                return $own;
            }

            if (! $own) {
                $anonymous->forceFill(['partner_id' => $partner->getKey(), 'access_token' => $this->partnerToken($partner)])->save();

                return $anonymous;
            }

            Track::query()->where('visitor_id', $anonymous->getKey())->update(['visitor_id' => $own->getKey()]);

            $own->forceFill([
                'visit_count'              => $own->visit_count + $anonymous->visit_count,
                'last_connection_datetime' => max($own->last_connection_datetime, $anonymous->last_connection_datetime),
                'country_id'               => $own->country_id ?? $anonymous->country_id,
                'timezone'                 => $own->timezone ?? $anonymous->timezone,
                'lang'                     => $own->lang ?? $anonymous->lang,
            ])->save();

            $anonymous->delete();

            return $own;
        });
    }

    public function deleteInactive(int $days): int
    {
        return Visitor::query()
            ->whereNull('partner_id')
            ->where('last_connection_datetime', '<', now()->subDays($days))
            ->delete();
    }

    protected function upsertVisitor(Request $request, ?Model $partner): Visitor
    {
        $visitor = $partner
            ? Visitor::query()->where('access_token', $this->partnerToken($partner))->lockForUpdate()->first()
            : $this->anonymousVisitor($request->cookie(self::COOKIE), lock: true);

        if ($partner && ! $visitor && ($anonymous = $this->anonymousVisitor($request->cookie(self::COOKIE)))) {
            $visitor = $this->linkPartner($partner, $anonymous->access_token);
        }

        $now = now();

        $visitor ??= new Visitor([
            'access_token' => $partner ? $this->partnerToken($partner) : Str::lower(Str::random(32)),
            'visit_count'  => 1,
        ]);

        if ($visitor->exists && $visitor->last_connection_datetime?->lt($now->copy()->subHours(self::NEW_VISIT_HOURS))) {
            $visitor->visit_count++;
        }

        $visitor->forceFill([
            'partner_id'               => $partner?->getKey() ?? $visitor->partner_id,
            'country_id'               => $this->countryId($request, $partner) ?? $visitor->country_id,
            'lang'                     => app()->getLocale(),
            'timezone'                 => $this->timezone($request) ?? $this->geolocation->timezoneForCountry($this->countryCode($request)) ?? $visitor->timezone,
            'last_connection_datetime' => $now,
        ])->save();

        return $visitor;
    }

    protected function addTrack(Visitor $visitor, ?Page $page, string $url): void
    {
        $recent = Track::query()
            ->where('visitor_id', $visitor->getKey())
            ->where('visit_datetime', '>=', now()->subMinutes(self::TRACK_AGAIN_MINUTES))
            ->when($page, fn ($query) => $query->where('page_id', $page->getKey()), fn ($query) => $query->whereNull('page_id')->where('url', $url))
            ->exists();

        if ($recent) {
            return;
        }

        Track::query()->create([
            'visitor_id'     => $visitor->getKey(),
            'page_id'        => $page?->getKey(),
            'url'            => mb_substr($url, 0, 2000),
            'visit_datetime' => now(),
        ]);
    }

    protected function anonymousVisitor(?string $token, bool $lock = false): ?Visitor
    {
        if (blank($token) || ! preg_match('/^[a-z0-9]{32}$/', $token)) {
            return null;
        }

        return Visitor::query()
            ->where('access_token', $token)
            ->whereNull('partner_id')
            ->when($lock, fn ($query) => $query->lockForUpdate())
            ->first();
    }

    protected function partnerToken(Model $partner): string
    {
        return 'partner-'.$partner->getKey();
    }

    protected function countryId(Request $request, ?Model $partner): ?int
    {
        $code = $this->countryCode($request);

        if ($code && ($id = Country::query()->where('code', $code)->value('id'))) {
            return (int) $id;
        }

        if ($partner?->country_id) {
            return (int) $partner->country_id;
        }

        $timezoneCode = static::countryCodeForTimezone($this->timezone($request));
        $id = $timezoneCode ? Country::query()->where('code', $timezoneCode)->value('id') : null;

        return $id ? (int) $id : null;
    }

    protected function countryCode(Request $request): ?string
    {
        $header = strtoupper((string) ($request->header('CF-IPCountry') ?? $request->header('X-Country-Code') ?? ''));

        return $this->geolocation->countryCode($request->ip())
            ?? (preg_match('/^[A-Z]{2}$/', $header) && $header !== 'XX' ? $header : null);
    }

    public static function countryCodeForTimezone(?string $timezone): ?string
    {
        if (blank($timezone)) {
            return null;
        }

        try {
            $code = (new DateTimeZone($timezone))->getLocation()['country_code'] ?? null;
        } catch (Throwable) {
            return null;
        }

        return is_string($code) && preg_match('/^[A-Z]{2}$/', $code) ? $code : null;
    }

    protected function timezone(Request $request): ?string
    {
        $timezone = $request->cookie(self::TIMEZONE_COOKIE);

        if (! is_string($timezone) || ! preg_match('#^[A-Za-z_]+(/[A-Za-z0-9_+\-]+)*$#', $timezone)) {
            return null;
        }

        $timezone = self::TIMEZONE_ALIASES[$timezone] ?? $timezone;

        try {
            return (new DateTimeZone($timezone))->getName();
        } catch (Throwable) {
            return mb_substr($timezone, 0, 64);
        }
    }
}
