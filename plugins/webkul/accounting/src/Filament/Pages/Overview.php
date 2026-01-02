<?php

namespace Webkul\Accounting\Filament\Pages;

use Filament\Pages\Page;
use Webkul\Accounting\Filament\Widgets\JournalChartsWidget;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class Overview extends Page
{
    use HasPageShield;

    protected static ?string $slug = 'accounting/overview';

    protected string $view = 'accounting::filament.pages.overview';

    protected static function getPagePermission(): ?string
    {
        return 'page_accounting_overview';
    }

    public static function getNavigationLabel(): string
    {
        return __('accounting::filament/pages/overview.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('accounting::filament/pages/overview.navigation.group');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            JournalChartsWidget::class,
        ];
    }
}
