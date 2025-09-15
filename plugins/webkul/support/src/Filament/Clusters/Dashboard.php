<?php

namespace Webkul\Support\Filament\Clusters;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Facades\Filament;
use Filament\Panel;

class Dashboard extends Cluster
{
    protected static ?string $slug = '/';

    protected static string $routePath = '/';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?int $navigationSort = 0;

    protected string $view = 'filament-panels::pages.page';

    public static function getRoutePath(Panel $panel): string
    {
        return static::$routePath;
    }

    public function getWidgets(): array
    {
        return Filament::getWidgets();
    }

    public function getVisibleWidgets(): array
    {
        return $this->getWidgetsSchemaComponents($this->getWidgets());
    }

    public function getColumns(): int|string|array
    {
        return 2;
    }
}
