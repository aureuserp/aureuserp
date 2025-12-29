<?php

namespace Webkul\Accounting\Filament\Pages;

use Filament\Pages\Page;
use Webkul\Accounting\Filament\Widgets\JournalChartsWidget;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class Dashboard extends Page
{
    use HasPageShield;
    
    protected string $view = 'accounting::filament.pages.dashboard';

    public static function getNavigationGroup(): string
    {
        return __('accounting::filament/pages/dashboard.navigation.group');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            JournalChartsWidget::class,
        ];
    }
}
