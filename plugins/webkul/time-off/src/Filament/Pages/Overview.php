<?php

namespace Webkul\TimeOff\Filament\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\TimeOff\Filament\Widgets\OverviewCalendarWidget;

class Overview extends BaseDashboard
{
    use HasPageShield;

    protected static string $routePath = 'time-off';

    protected static ?int $navigationSort = 2;

    protected static function getPagePermission(): ?string
    {
        return 'page_time_off_overview';
    }

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return null;
    }

    public function getTitle(): string
    {
        return __('time-off::filament/pages/overview.navigation.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('time-off::filament/pages/overview.navigation.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('time-off::filament/pages/overview.navigation.group');
    }

    public function getWidgets(): array
    {
        return [
            OverviewCalendarWidget::class,
        ];
    }
}
