<?php

namespace Webkul\Accounting\Filament\Pages;

use Filament\Pages\Page;
use Webkul\Accounting\Filament\Widgets\JournalChartsWidget;

class Dashboard extends Page
{
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
