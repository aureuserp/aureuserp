<?php

namespace Webkul\Inventory\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Webkul\Inventory\Filament\Widgets\OperationTypeOverviewWidget;
use Webkul\Support\Enums\NavigationGroup;

class Overview extends Page
{
    use HasPageShield;

    protected static ?string $slug = 'inventory/overview';

    protected string $view = 'inventories::filament.pages.overview';

    protected static ?int $navigationSort = 0;

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/pages/overview.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Inventory;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            OperationTypeOverviewWidget::class,
        ];
    }
}
