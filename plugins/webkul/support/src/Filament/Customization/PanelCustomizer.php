<?php

namespace Webkul\Support\Filament\Customization;

use Closure;
use Filament\Facades\Filament;
use Filament\Panel;
use Filament\Schemas\Schema;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\Table;
use Filament\View\PanelsRenderHook;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Livewire\Livewire;
use UnitEnum;

class PanelCustomizer
{
    protected static array $panelCallbacks = [];

    protected static array $excludedResources = [];

    protected static array $replacedResources = [];

    protected static array $hiddenNavigationItems = [];

    protected static array $navigationGroups = [];

    protected static array $replacedPages = [];

    protected static array $headerActions = [];

    public static function panel(?string $panelId, Closure $callback): void
    {
        static::$panelCallbacks[] = [$panelId, $callback];
    }

    public static function table(Closure $callback): void
    {
        Table::configureUsing($callback, isImportant: true);
    }

    public static function schema(Closure $callback): void
    {
        Schema::configureUsing($callback, isImportant: true);
    }

    public static function excludeResources(?string $panelId, array $resources): void
    {
        foreach ($resources as $resource) {
            static::$excludedResources[static::panelKey($panelId)][$resource] = true;
        }
    }

    public static function replaceResource(?string $panelId, string $resource, string $replacement): void
    {
        static::$replacedResources[static::panelKey($panelId)][$resource] = $replacement;
    }

    public static function hideNavigationItems(?string $panelId, array $components): void
    {
        foreach ($components as $component) {
            static::$hiddenNavigationItems[static::panelKey($panelId)][$component] = true;
        }
    }

    public static function navigationGroup(?string $panelId, string $component, string|UnitEnum|null $group): void
    {
        static::$navigationGroups[static::panelKey($panelId)][$component] = $group;
    }

    public static function resource(string $resource): string
    {
        return static::forPanel(static::$replacedResources, static::currentPanelId())[$resource] ?? $resource;
    }

    public static function replacePage(string $page, string $replacement): void
    {
        static::$replacedPages[$page] = $replacement;
    }

    public static function headerActions(string $page, Closure $factory): void
    {
        if (! isset(static::$headerActions[$page])) {
            FilamentView::registerRenderHook(
                PanelsRenderHook::PAGE_HEADER_ACTIONS_AFTER,
                fn (): string => static::renderHeaderActions($page),
                scopes: $page,
            );
        }

        static::$headerActions[$page][] = $factory;
    }

    public static function headerActionsFor(string $page, ?Model $record): array
    {
        $actions = [];

        foreach (static::$headerActions[$page] ?? [] as $factory) {
            $actions = [...$actions, ...$factory($record)];
        }

        return $actions;
    }

    public static function swapPageRoutes(): void
    {
        if (! static::$replacedPages || app()->routesAreCached()) {
            return;
        }

        $routes = Route::getRoutes();

        foreach ($routes->getRoutes() as $route) {
            $controller = $route->getAction('controller');

            $page = is_string($controller) ? Str::before($controller, '@') : null;

            if (! $page || ! isset(static::$replacedPages[$page])) {
                continue;
            }

            $replacement = static::$replacedPages[$page];

            $route->setAction(array_merge($route->getAction(), [
                'uses'       => $replacement.'@__invoke',
                'controller' => $replacement,
            ]));
        }

        $routes->refreshActionLookups();
    }

    public static function apply(Panel $panel): void
    {
        foreach (static::$panelCallbacks as [$panelId, $callback]) {
            if ($panelId === null || $panelId === $panel->getId()) {
                $callback($panel);
            }
        }

        if (static::$replacedPages) {
            $panel->livewireComponents(array_values(static::$replacedPages));
        }

        $panel->customizeResources(
            static::forPanel(static::$excludedResources, $panel->getId()),
            static::forPanel(static::$replacedResources, $panel->getId()),
        );
    }

    public static function isNavigationItemHidden(string $key): bool
    {
        return isset(static::forPanel(static::$hiddenNavigationItems, static::currentPanelId())[$key]);
    }

    public static function hasNavigationGroup(string $key): bool
    {
        return array_key_exists($key, static::forPanel(static::$navigationGroups, static::currentPanelId()));
    }

    public static function navigationGroupFor(string $key): string|UnitEnum|null
    {
        return static::forPanel(static::$navigationGroups, static::currentPanelId())[$key] ?? null;
    }

    public static function flush(): void
    {
        static::$panelCallbacks = [];
        static::$excludedResources = [];
        static::$replacedResources = [];
        static::$hiddenNavigationItems = [];
        static::$navigationGroups = [];
        static::$replacedPages = [];
        static::$headerActions = [];
    }

    protected static function renderHeaderActions(string $page): string
    {
        $livewire = Livewire::current();

        $record = $livewire && method_exists($livewire, 'getRecord') ? $livewire->getRecord() : null;

        return Livewire::mount('page-header-actions', [
            'page'      => $page,
            'model'     => $record ? $record::class : null,
            'recordKey' => $record?->getKey(),
        ], 'page-header-actions-'.md5($page.'|'.$record?->getKey()));
    }

    protected static function forPanel(array $registry, ?string $panelId): array
    {
        return array_merge($registry['*'] ?? [], $panelId ? ($registry[$panelId] ?? []) : []);
    }

    protected static function panelKey(?string $panelId): string
    {
        return $panelId ?? '*';
    }

    protected static function currentPanelId(): ?string
    {
        return Filament::getCurrentPanel()?->getId();
    }
}
