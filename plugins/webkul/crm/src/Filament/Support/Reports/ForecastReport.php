<?php

namespace Webkul\Crm\Filament\Support\Reports;

use Illuminate\Support\Facades\Auth;
use Webkul\Crm\Filament\Support\LeadTableFilters;

class ForecastReport extends LeadReport
{
    public static function dimensions(): array
    {
        return collect(parent::dimensions())->except(['type'])->all();
    }

    public static function getDefaultRules(): array
    {
        return array_values(array_filter([
            filled(Auth::id()) ? LeadTableFilters::salespersonRule(Auth::id()) : null,
            LeadTableFilters::relativeDateRule('expected_closing_date', 'this_month'),
            LeadTableFilters::activeRule(),
        ]));
    }

    public static function getDefaultMeasure(): string
    {
        return 'prorated_revenue';
    }

    public static function getDefaultGroupBy(): string
    {
        return 'expected_closing';
    }
}
