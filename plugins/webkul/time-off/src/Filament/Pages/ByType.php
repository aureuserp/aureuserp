<?php

namespace Webkul\TimeOff\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Webkul\TimeOff\Filament\Clusters\Reporting;
use Webkul\TimeOff\Filament\Widgets\LeaveTypeWidget;

class ByType extends BaseDashboard
{
    protected static string $routePath = 'reporting/by-type';

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = Reporting::class;

    public function getTitle(): string
    {
        return __('time-off::filament/pages/by-type.navigation.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('time-off::filament/pages/by-type.navigation.title');
    }

    public function getWidgets(): array
    {
        return [
            LeaveTypeWidget::class,
        ];
    }
}
