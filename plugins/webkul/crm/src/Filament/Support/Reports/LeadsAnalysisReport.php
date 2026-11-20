<?php

namespace Webkul\Crm\Filament\Support\Reports;

use Webkul\Crm\Filament\Support\LeadTableFilters;

class LeadsAnalysisReport extends LeadReport
{
    public static function getDefaultRules(): array
    {
        return [LeadTableFilters::relativeDateRule('created_at', 'this_year')];
    }

    public static function getDefaultMeasure(): string
    {
        return 'count';
    }

    public static function getDefaultGroupBy(): string
    {
        return 'created_at';
    }

    public static function getDefaultSplitBy(): ?string
    {
        return 'team';
    }
}
