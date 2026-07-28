<?php

namespace Webkul\Maintenance\Filament\Clusters\Maintenance\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Dashboard;
use Webkul\Maintenance\Filament\Clusters\Maintenance;
use Webkul\Maintenance\Filament\Widgets\MaintenanceCalendarWidget;

class Calendar extends Dashboard
{
    use HasPageShield;

    protected static string $routePath = 'calendar';

    protected static ?string $cluster = Maintenance::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?int $navigationSort = 1;

    protected static function getPagePermission(): ?string
    {
        return 'page_maintenance_calendar';
    }

    public static function getNavigationLabel(): string
    {
        return __('maintenance::filament/clusters/maintenance/pages/calendar.navigation.title');
    }

    public function getTitle(): string
    {
        return __('maintenance::filament/clusters/maintenance/pages/calendar.title');
    }

    public function getWidgets(): array
    {
        return [
            MaintenanceCalendarWidget::class,
        ];
    }

    public function getHeaderWidgets(): array
    {
        return [];
    }
}
