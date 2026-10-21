<?php

namespace Webkul\PointOfSale\Filament\Admin\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Throwable;
use UnitEnum;
use Webkul\PointOfSale\Enums\SessionState;
use Webkul\PointOfSale\Facades\PointOfSale;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\ConfigResource;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\OrderResource;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\SessionResource;
use Webkul\PointOfSale\Filament\Admin\Clusters\Reporting\Pages\OrderReport;
use Webkul\PointOfSale\Filament\Pos\Pages\Register;
use Webkul\PointOfSale\Models\Config;
use Webkul\PointOfSale\Models\Session;
use Webkul\Support\Enums\NavigationGroup;

class Registers extends Page
{
    use HasPageShield;

    protected static ?string $slug = 'point-of-sale/registers';

    protected static ?int $navigationSort = 0;

    protected string $view = 'point-of-sale::filament.admin.pages.registers';

    /**
     * The session has been open for an unusually long period once it crosses
     * this many days, which is when the reminder to close it appears.
     */
    protected const STALE_AFTER_DAYS = 1;

    protected const CRITICAL_AFTER_DAYS = 3;

    protected static function getPagePermission(): ?string
    {
        return 'page_point_of_sale_registers';
    }

    public static function getNavigationLabel(): string
    {
        return __('point-of-sale::filament/admin/pages/registers.navigation.label');
    }

    public static function getNavigationGroup(): string|UnitEnum
    {
        return NavigationGroup::PointOfSale;
    }

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return null;
    }

    public function getTitle(): string
    {
        return __('point-of-sale::filament/admin/pages/registers.title');
    }

    /**
     * @return Collection<int, Config>
     */
    public function getRegisters(): Collection
    {
        return Config::query()
            ->where('is_active', true)
            ->with('currency')
            ->orderBy('sort')
            ->orderBy('id')
            ->get();
    }

    public function liveSessionFor(Config $config): ?Session
    {
        return PointOfSale::liveSessionFor($config);
    }

    public function lastClosedSessionFor(Config $config): ?Session
    {
        return Session::query()
            ->where('config_id', $config->getKey())
            ->where('state', SessionState::CLOSED)
            ->latest('stopped_at')
            ->first();
    }

    public function rescueSessionCountFor(Config $config): int
    {
        return Session::query()
            ->where('config_id', $config->getKey())
            ->where('is_rescue', true)
            ->whereNot('state', SessionState::CLOSED)
            ->count();
    }

    /**
     * @return array{label: string, color: string, tooltip: ?string}|null
     */
    public function badgeFor(?Session $session): ?array
    {
        $prefix = 'point-of-sale::filament/admin/pages/registers.badges.';

        if (! $session) {
            return null;
        }

        if ($session->state === SessionState::OPENING_CONTROL) {
            return ['label' => __($prefix.'opening-control'), 'color' => 'info', 'tooltip' => null];
        }

        if ($session->state === SessionState::CLOSING_CONTROL) {
            return ['label' => __($prefix.'closing-control'), 'color' => 'info', 'tooltip' => null];
        }

        $days = $session->started_at?->diffInDays(now()) ?? 0;

        if ($session->state === SessionState::OPENED && $days > self::STALE_AFTER_DAYS) {
            return [
                'label'   => __($prefix.'to-close'),
                'color'   => $days > self::CRITICAL_AFTER_DAYS ? 'danger' : 'warning',
                'tooltip' => __($prefix.'to-close-tooltip'),
            ];
        }

        return [
            'label'   => __($prefix.'opened-by', ['name' => $session->user?->name]),
            'color'   => 'info',
            'tooltip' => null,
        ];
    }

    public function isClosing(?Session $session): bool
    {
        return $session?->state === SessionState::CLOSING_CONTROL;
    }

    public function configUrl(Config $config): string
    {
        return ConfigResource::getUrl('edit', ['record' => $config->getKey()]);
    }

    public function sessionUrl(Session $session): string
    {
        return SessionResource::getUrl('view', ['record' => $session->getKey()]);
    }

    public function sessionsUrl(Config $config): string
    {
        return SessionResource::getUrl('index', ['filters' => ['config_id' => ['value' => $config->getKey()]]]);
    }

    public function ordersUrl(Config $config): string
    {
        return OrderResource::getUrl('index', ['filters' => ['config_id' => ['value' => $config->getKey()]]]);
    }

    public function reportingUrl(Config $config): string
    {
        return OrderReport::getUrl(['configId' => $config->getKey()]);
    }

    public function openRegister(int $configId, bool $newTab = false): ?string
    {
        $config = Config::find($configId);

        if (! $config) {
            return null;
        }

        try {
            PointOfSale::liveSessionFor($config) ?? PointOfSale::openSession($config);
        } catch (Throwable $exception) {
            Notification::make()
                ->danger()
                ->body($exception->getMessage())
                ->send();

            return null;
        }

        $url = Register::getUrl(['config' => $config->getKey()], panel: 'pos');

        if ($newTab) {
            return $url;
        }

        $this->redirect($url);

        return null;
    }
}
