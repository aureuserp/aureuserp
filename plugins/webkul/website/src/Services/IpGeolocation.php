<?php

namespace Webkul\Website\Services;

use DateTimeZone;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class IpGeolocation
{
    public const TABLE = 'website_ip_countries';

    public const CACHE_VERSION_KEY = 'website-ip-country-version';

    public const MAIN_TIMEZONES = [
        'US' => 'America/New_York',
        'CA' => 'America/Toronto',
        'MX' => 'America/Mexico_City',
        'BR' => 'America/Sao_Paulo',
        'AR' => 'America/Argentina/Buenos_Aires',
        'CL' => 'America/Santiago',
        'EC' => 'America/Guayaquil',
        'GL' => 'America/Nuuk',
        'RU' => 'Europe/Moscow',
        'UA' => 'Europe/Kyiv',
        'ES' => 'Europe/Madrid',
        'PT' => 'Europe/Lisbon',
        'DE' => 'Europe/Berlin',
        'NL' => 'Europe/Amsterdam',
        'CN' => 'Asia/Shanghai',
        'ID' => 'Asia/Jakarta',
        'KZ' => 'Asia/Almaty',
        'MY' => 'Asia/Kuala_Lumpur',
        'MN' => 'Asia/Ulaanbaatar',
        'UZ' => 'Asia/Tashkent',
        'CY' => 'Asia/Nicosia',
        'PS' => 'Asia/Gaza',
        'AU' => 'Australia/Sydney',
        'NZ' => 'Pacific/Auckland',
        'PF' => 'Pacific/Tahiti',
        'KI' => 'Pacific/Tarawa',
        'FM' => 'Pacific/Pohnpei',
        'PG' => 'Pacific/Port_Moresby',
        'CD' => 'Africa/Kinshasa',
        'EG' => 'Africa/Cairo',
    ];

    public static function toBinary(string $ip): ?string
    {
        $packed = @inet_pton($ip);

        if ($packed === false) {
            return null;
        }

        return strlen($packed) === 4 ? str_repeat("\0", 10)."\xff\xff".$packed : $packed;
    }

    public static function isPublic(?string $ip): bool
    {
        return filled($ip) && filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false;
    }

    public function countryCode(?string $ip): ?string
    {
        if (! static::isPublic($ip)) {
            return null;
        }

        $key = 'website-ip-country:'.Cache::get(self::CACHE_VERSION_KEY, 0).':'.$ip;

        return Cache::remember($key, now()->addDays(30), function () use ($ip): string {
            $binary = static::toBinary($ip);

            if ($binary === null || ! Schema::hasTable(self::TABLE)) {
                return '';
            }

            try {
                $range = DB::table(self::TABLE)
                    ->where('ip_from', '<=', $binary)
                    ->orderByDesc('ip_from')
                    ->first(['ip_to', 'country_code']);
            } catch (Throwable) {
                return '';
            }

            return $range && strcmp($range->ip_to, $binary) >= 0 && $range->country_code !== 'ZZ' ? strtoupper($range->country_code) : '';
        }) ?: null;
    }

    public function timezoneForCountry(?string $countryCode): ?string
    {
        if (! $countryCode || ! preg_match('/^[A-Z]{2}$/', $countryCode)) {
            return null;
        }

        try {
            $timezones = DateTimeZone::listIdentifiers(DateTimeZone::PER_COUNTRY, $countryCode);

            $main = self::MAIN_TIMEZONES[$countryCode] ?? null;

            return $main && in_array($main, $timezones, true) ? $main : ($timezones[0] ?? null);
        } catch (Throwable) {
            return null;
        }
    }
}
