<?php

namespace Webkul\Accounting\Filament\Clusters\Reporting\Pages\Concerns;

use Webkul\Accounting\Support\CompanyRateMap;

trait FormatsReportValues
{
    public function reportCurrency(): string
    {
        return CompanyRateMap::make()->presentationCurrency()?->name ?? default_currency_code();
    }
}
