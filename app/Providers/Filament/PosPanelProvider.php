<?php

namespace App\Providers\Filament;

use App\Http\Middleware\ApplyBrandSettings;
use App\Http\Middleware\SetLocale;
use Filament\Actions\Action;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Js;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\ConfigResource;
use Webkul\PointOfSale\Filament\Pos\Pages\Pos;
use Webkul\PointOfSale\Filament\Pos\Pages\Registers;

class PosPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('pos')
            ->path('pos')
            ->login()
            ->favicon(asset('images/favicon.ico'))
            ->brandLogo(asset('images/logo.svg'))
            ->brandLogoHeight('2rem')
            ->colors([
                'primary' => Color::Blue,
            ])
            ->maxContentWidth(Width::Full)
            ->homeUrl(fn (): string => Pos::getUrl(panel: 'pos'))
            ->topbar(false)
            ->topNavigation()
            ->userMenuItems([
                'orders' => Action::make('orders')
                    ->label(fn (): string => __('point-of-sale::filament/pos/pages/terminal.menu.orders'))
                    ->icon('heroicon-m-receipt-percent')
                    ->url('#')
                    ->extraAttributes([
                        'x-on:click.prevent' => "window.dispatchEvent(new CustomEvent('pos-open-orders'))",
                    ]),

                'cashMovement' => Action::make('cashMovement')
                    ->label(fn (): string => __('point-of-sale::filament/pos/pages/terminal.menu.cash-in-out'))
                    ->icon('heroicon-m-banknotes')
                    ->url('#')
                    ->extraAttributes([
                        'x-on:click.prevent' => "window.dispatchEvent(new CustomEvent('pos-open-cash-movement'))",
                    ]),

                'installApp' => Action::make('installApp')
                    ->label(fn (): string => __('point-of-sale::filament/pos/pages/terminal.install.label'))
                    ->icon('heroicon-m-arrow-down-tray')
                    ->url('#')
                    ->extraAttributes(fn (): array => [
                        'x-on:click.prevent' => 'window.pointOfSaleInstall('.Js::from([
                            'title'       => __('point-of-sale::filament/pos/pages/terminal.install.label'),
                            'installed'   => __('point-of-sale::filament/pos/pages/terminal.install.installed'),
                            'unavailable' => __('point-of-sale::filament/pos/pages/terminal.install.unavailable'),
                        ])->toHtml().')',
                    ]),

                'backOffice' => Action::make('backOffice')
                    ->label(fn (): string => __('point-of-sale::filament/pos/pages/terminal.menu.back-office'))
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->url(fn (): string => ConfigResource::getUrl(panel: 'admin')),

                'closeRegister' => Action::make('closeRegister')
                    ->label(fn (): string => __('point-of-sale::filament/pos/pages/terminal.menu.close-register'))
                    ->icon('heroicon-m-lock-closed')
                    ->color('danger')
                    ->url('#')
                    ->extraAttributes([
                        'x-on:click.prevent' => "window.dispatchEvent(new CustomEvent('pos-close-register'))",
                    ]),
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): View => view('point-of-sale::filament.pos.partials.pwa-head', [
                    'config' => Registers::currentSession()?->config,
                ]),
            )
            ->renderHook(
                PanelsRenderHook::GLOBAL_SEARCH_AFTER,
                fn (): View => view('point-of-sale::filament.pos.partials.status-slot'),
            )
            ->spa()
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                SetLocale::class,
                ApplyBrandSettings::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
