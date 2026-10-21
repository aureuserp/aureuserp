<?php

namespace Webkul\PointOfSale\Filament\Pos\Pages;

use BackedEnum;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Webkul\PointOfSale\Facades\PointOfSale;
use Webkul\PointOfSale\Filament\Admin\Pages\Registers as AdminRegisters;
use Webkul\PointOfSale\Http\Resources\V1\RegisterResource;
use Webkul\PointOfSale\Models\Config;
use Webkul\PointOfSale\Models\Session;
use Webkul\PointOfSale\Services\BootLoader;
use Webkul\PointOfSale\Services\SessionWorkflow;
use Webkul\PointOfSale\Support\PosAccess;

abstract class Terminal extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $slug = 'terminal/{config}';

    protected string $view = 'point-of-sale::filament.pos.pages.terminal';

    protected static bool $shouldRegisterNavigation = false;

    public Config $config;

    public ?Session $session = null;

    public function mount(Config $config): void
    {
        abort_unless(PosAccess::reachesConfig($config), 403);

        $this->config = $config;

        $session = app(SessionWorkflow::class)->liveSessionFor($config);

        if ($session && PosAccess::reachesSession($session)) {
            $this->session = PointOfSale::loginSession($session);
        }
    }

    public function hasSession(): bool
    {
        return $this->session !== null;
    }

    public function bootPayload(): ?array
    {
        if (! $this->session) {
            return null;
        }

        return array_merge(app(BootLoader::class)->load($this->config, $this->session), [
            'pending_refund' => session()->pull(static::pendingRefundKey($this->session)),
        ]);
    }

    public static function pendingRefundKey(Session $session): string
    {
        return 'point-of-sale.pending-refund.'.$session->getKey();
    }

    public function registers(): array
    {
        return RegisterResource::collection(collect([$this->config]))->resolve();
    }

    public function syncEndpoint(): string
    {
        return route('point-of-sale.till.orders.sync');
    }

    public function registersEndpoint(): string
    {
        return route('point-of-sale.till.registers');
    }

    public function openEndpointBase(): string
    {
        return route('point-of-sale.till.configs.session.open', ['config' => '__config__']);
    }

    public function bootEndpointBase(): string
    {
        return route('point-of-sale.till.sessions.boot', ['session' => '__session__']);
    }

    public function discardEndpointBase(): string
    {
        return route('point-of-sale.till.registers.discard', ['config' => '__config__']);
    }

    public function backUrl(): string
    {
        return AdminRegisters::getUrl(panel: 'admin');
    }

    public function backOfficeUrl(): string
    {
        return AdminRegisters::getUrl(panel: 'admin');
    }

    public function logoutUrl(): string
    {
        return route('point-of-sale.pos.logout');
    }

    public function brandLogo(): string
    {
        return asset('images/logo.svg');
    }

    public function userName(): string
    {
        return Auth::user()?->name ?? '';
    }

    public function userAvatar(): ?string
    {
        $user = Auth::user();

        return $user ? Filament::getUserAvatarUrl($user) : null;
    }

    public function strings(): array
    {
        $lines = trans('point-of-sale::filament/pos/pages/terminal');

        return is_array($lines) ? $lines : [];
    }
}
