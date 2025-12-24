<?php

namespace Webkul\Accounting\Filament\Pages;

use Filament\Pages\Page;

class Dashboard extends Page
{
    protected string $view = 'accounting::filament.pages.dashboard';

    public static function getNavigationGroup(): string
    {
        return __('accounting::filament/pages/dashboard.navigation.group');
    }
}
