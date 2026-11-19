<?php

namespace Webkul\Asset;

use Filament\Panel;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\HtmlString;
use Webkul\Asset\Database\Seeders\DatabaseSeeder;
use Webkul\Asset\Database\Seeders\RolePermissionSeeder;
use Webkul\Asset\Filament\Resources\AssetSiteResource\RelationManagers\SiteAssetsRelationManager;
use Webkul\Asset\Models\Asset;
use Webkul\Asset\Models\AssetCategory;
use Webkul\Asset\Models\AssetMovement;
use Webkul\Asset\Models\AssetProduct;
use Webkul\Asset\Models\AssetSite;
use Webkul\Asset\Models\AssetWarehouse;
use Webkul\Asset\Observers\EmployeeObserver;
use Webkul\Asset\Policies\AssetCategoryPolicy;
use Webkul\Asset\Policies\AssetMovementPolicy;
use Webkul\Asset\Policies\AssetPolicy;
use Webkul\Asset\Policies\AssetProductPolicy;
use Webkul\Asset\Policies\AssetSitePolicy;
use Webkul\Asset\Policies\AssetWarehousePolicy;
use Webkul\Asset\Services\AssetCustodyService;
use Webkul\Chatter\Services\ChatterCleanupService;
use Webkul\Employee\Models\Employee;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;
use Webkul\Support\Services\SequenceService;

class AssetServiceProvider extends PackageServiceProvider
{
    public static string $name = 'assets';

    public static string $viewNamespace = 'assets';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasTranslations()
            ->hasMigrations([
                '2026_03_20_000001_create_asset_sites_table',
                '2026_03_20_000002_create_assets_table',
                '2026_03_20_000003_create_asset_assignments_table',
                '2026_11_03_000001_add_consumable_quantities_to_assets_tables',
            ])
            ->runsMigrations()
            ->hasSettings([
                '2026_03_20_000001_create_assets_settings',
                '2026_11_03_000001_add_consumables_settings_to_assets_settings',
            ])
            ->runsSettings()
            ->hasDependencies(['employees'])
            ->hasSeeder(DatabaseSeeder::class)
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command
                    ->installDependencies()
                    ->runsMigrations()
                    ->endWith(function (InstallCommand $command): void {
                        $command->call(RolePermissionSeeder::class);
                    });
            })
            ->hasUninstallCommand(function (UninstallCommand $command): void {
                $command->endWith(function (): void {
                    SequenceService::purge([AssetCustodyService::SEQUENCE_CODE]);

                    ChatterCleanupService::purgeForModels([
                        Asset::class,
                        AssetProduct::class,
                        AssetSite::class,
                        AssetWarehouse::class,
                    ]);
                });
            })
            ->icon('assets');
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(AssetPlugin::make()));

        FilamentView::registerRenderHook(
            PanelsRenderHook::RESOURCE_RELATION_MANAGER_BEFORE,
            fn (): View => view('table-views::filament.resources.pages.list-records.favorites-views'),
            scopes: SiteAssetsRelationManager::class,
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::STYLES_AFTER,
            fn (): HtmlString => new HtmlString('<style>
.fi-ta-sticky-scroll .fi-ta-content-ctn {
    scrollbar-width: none;
    -ms-overflow-style: none;
}
.fi-ta-sticky-scroll .fi-ta-content-ctn::-webkit-scrollbar {
    display: none;
    width: 0;
    height: 0;
}
.fi-ta-floating-hscroll {
    position: fixed;
    bottom: 0;
    z-index: 30;
    overflow-x: auto;
    overflow-y: hidden;
    height: 14px;
    background-color: rgba(156, 163, 175, 0.16);
    border-top: 1px solid rgba(156, 163, 175, 0.25);
    backdrop-filter: blur(6px);
    scrollbar-width: thin;
}
.fi-ta-floating-hscroll::-webkit-scrollbar {
    height: 12px;
}
.fi-ta-floating-hscroll::-webkit-scrollbar-thumb {
    background-color: rgba(156, 163, 175, 0.55);
    border-radius: 6px;
}
.fi-ta-floating-hscroll[hidden] {
    display: none !important;
}
.fi-ta-hscroll-track {
    height: 1px;
}
.fi-fo-select-scrollable .fi-select-input-value-badges-ctn {
    max-height: 7.5rem;
    overflow-y: auto;
    padding-inline-end: 0.25rem;
    scrollbar-width: thin;
}
</style>'),
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::SCRIPTS_AFTER,
            fn (): HtmlString => new HtmlString(<<<'JS'
<script>
(() => {
    if (window.__fiTaFloatingHScroll) {
        return;
    }

    window.__fiTaFloatingHScroll = true;

    let floatingBar = null;
    let track = null;
    let activeContentCtn = null;
    let resizeObserver = null;
    let scheduled = false;

    const buildBar = () => {
        floatingBar = document.createElement('div');
        floatingBar.className = 'fi-ta-floating-hscroll';
        floatingBar.hidden = true;

        track = document.createElement('div');
        track.className = 'fi-ta-hscroll-track';

        floatingBar.append(track);
        document.body.append(floatingBar);

        floatingBar.addEventListener('scroll', () => {
            if (! activeContentCtn || floatingBar.__fiTaSyncing) {
                return;
            }

            activeContentCtn.scrollLeft = floatingBar.scrollLeft;
        });

        return floatingBar;
    };

    const ensureBar = () => {
        if (! floatingBar || ! floatingBar.isConnected) {
            buildBar();
        }

        return floatingBar;
    };

    const bindContentCtn = (contentCtn) => {
        if (contentCtn.__fiTaFloatingBound) {
            return;
        }

        contentCtn.__fiTaFloatingBound = true;

        contentCtn.addEventListener('scroll', () => {
            if (contentCtn !== activeContentCtn || ! floatingBar || floatingBar.__fiTaSyncing) {
                return;
            }

            floatingBar.__fiTaSyncing = true;
            floatingBar.scrollLeft = contentCtn.scrollLeft;

            window.requestAnimationFrame(() => {
                if (floatingBar) {
                    floatingBar.__fiTaSyncing = false;
                }
            });
        });
    };

    const isInViewport = (rect) => rect.top < (window.innerHeight - 20) && rect.bottom > 40;

    const measure = () => {
        const bar = ensureBar();

        const candidates = Array.from(
            document.querySelectorAll('.fi-ta-sticky-scroll .fi-ta-content-ctn'),
        );

        candidates.forEach(bindContentCtn);

        let active = null;
        let fallback = null;

        for (const contentCtn of candidates) {
            if (contentCtn.scrollWidth <= (contentCtn.clientWidth + 2)) {
                continue;
            }

            if (isInViewport(contentCtn.getBoundingClientRect())) {
                active = contentCtn;

                break;
            }

            fallback ??= contentCtn;
        }

        active ??= fallback;

        if (activeContentCtn !== active) {
            activeContentCtn = active;

            if (resizeObserver) {
                resizeObserver.disconnect();
            }

            if (active && typeof ResizeObserver !== 'undefined') {
                resizeObserver = new ResizeObserver(scheduleUpdate);
                resizeObserver.observe(active);

                if (active.firstElementChild) {
                    resizeObserver.observe(active.firstElementChild);
                }
            }
        }

        if (! active) {
            bar.hidden = true;

            return;
        }

        const rect = active.getBoundingClientRect();

        if (! isInViewport(rect)) {
            bar.hidden = true;

            return;
        }

        bar.hidden = false;
        bar.style.left = rect.left + 'px';
        bar.style.width = rect.width + 'px';
        bar.dir = getComputedStyle(active).direction;
        track.style.width = active.scrollWidth + 'px';

        bar.__fiTaSyncing = true;
        bar.scrollLeft = active.scrollLeft;

        window.requestAnimationFrame(() => {
            if (floatingBar) {
                floatingBar.__fiTaSyncing = false;
            }
        });
    };

    const scheduleUpdate = () => {
        if (scheduled) {
            return;
        }

        scheduled = true;

        window.requestAnimationFrame(() => {
            scheduled = false;

            measure();
        });
    };

    const boot = () => {
        ensureBar();

        new MutationObserver(scheduleUpdate).observe(document.documentElement, {
            childList: true,
            subtree: true,
        });

        window.addEventListener('scroll', scheduleUpdate, { passive: true });
        window.addEventListener('resize', scheduleUpdate);
        document.addEventListener('livewire:navigated', scheduleUpdate);

        measure();
    };

    if (document.body) {
        boot();
    } else {
        document.addEventListener('DOMContentLoaded', boot, { once: true });
    }
})();
</script>
JS),
        );
    }

    public function packageBooted(): void
    {
        Gate::policy(Asset::class, AssetPolicy::class);
        Gate::policy(AssetCategory::class, AssetCategoryPolicy::class);
        Gate::policy(AssetMovement::class, AssetMovementPolicy::class);
        Gate::policy(AssetProduct::class, AssetProductPolicy::class);
        Gate::policy(AssetSite::class, AssetSitePolicy::class);
        Gate::policy(AssetWarehouse::class, AssetWarehousePolicy::class);

        if (! Package::isPluginInstalled(static::$name)) {
            return;
        }

        Employee::observe(EmployeeObserver::class);
    }
}
