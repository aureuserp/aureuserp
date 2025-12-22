<?php

namespace Webkul\Accounting\Filament\Clusters\Reporting\Pages;

use Filament\Pages\Page;
use Webkul\Accounting\Filament\Clusters\Reporting;

class BalanceSheet extends Page
{
    protected string $view = 'accounting::filament.clusters.reporting.pages.balance-sheet';

    protected static ?string $cluster = Reporting::class;
}
