<?php

namespace Webkul\PointOfSale\Support;

use Illuminate\Support\Facades\Auth;
use Webkul\PointOfSale\Models\Config;
use Webkul\PointOfSale\Models\Order;
use Webkul\PointOfSale\Models\Session;
use Webkul\Support\Services\CompanyContext;

class PosAccess
{
    public static function companyIds(): array
    {
        return app(CompanyContext::class)->allowedIds();
    }

    public static function reachesCompany(?int $companyId): bool
    {
        if (! Auth::check()) {
            return false;
        }

        if ($companyId === null) {
            return true;
        }

        $allowed = static::companyIds();

        return $allowed === [] || in_array($companyId, $allowed, true);
    }

    public static function reachesConfig(?Config $config): bool
    {
        return $config !== null && static::reachesCompany($config->company_id);
    }

    public static function reachesSession(?Session $session): bool
    {
        return $session !== null && static::reachesCompany($session->company_id);
    }

    public static function reachesActiveSession(?Session $session): bool
    {
        if (! static::reachesSession($session)) {
            return false;
        }

        return (bool) $session->config?->is_active || $session->isLive();
    }

    public static function reachesOrder(?Order $order): bool
    {
        return $order !== null && static::reachesCompany($order->company_id);
    }
}
