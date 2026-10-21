<?php

namespace Webkul\PointOfSale\Filament\Pos\Pages;

use Filament\Pages\PageConfiguration;
use Filament\Panel;
use Illuminate\Support\Facades\Route;

class Register extends Terminal
{
    protected static ?string $slug = '{config}';

    protected string $view = 'point-of-sale::filament.pos.pages.terminal';

    public static function routes(Panel $panel, ?PageConfiguration $configuration = null): void
    {
        $middleware = static::getRouteMiddleware($panel);

        if ($configuration) {
            $middleware = [...$middleware, "page-configuration:{$configuration->getKey()}"];
        }

        Route::get(static::getRoutePath($panel), static::class)
            ->middleware($middleware)
            ->withoutMiddleware(static::getWithoutRouteMiddleware($panel))
            ->whereNumber('config')
            ->name(static::getRelativeRouteName($panel));
    }
}
