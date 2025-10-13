<?php

namespace Webkul\TimeOff\Filament\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Dashboard as BaseDashboard;
use Webkul\TimeOff\Filament\Clusters\MyTime;
use Webkul\TimeOff\Filament\Widgets\CalendarWidget;
use Webkul\TimeOff\Filament\Widgets\MyTimeOffWidget;

class Dashboard extends BaseDashboard
{
    use HasPageShield;

    protected static string $routePath = 'time-off';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $cluster = MyTime::class;

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('time-off::filament/pages/dashboard.navigation.title');
    }

    public function getWidgets(): array
    {
        return [
            CalendarWidget::class,
        ];
    }

    public function getHeaderWidgets(): array
    {
        return [
            MyTimeOffWidget::make(),
        ];
    }
}
