<?php

namespace Webkul\Crm\Filament\Support\Reports;

use Webkul\Crm\Filament\Support\LeadTableFilters;

class PipelineReport extends LeadReport
{
    public static function dimensions(): array
    {
        return collect(parent::dimensions())->except(['type'])->all();
    }

    public static function getDefaultRules(): array
    {
        return [LeadTableFilters::activeRule()];
    }

    public static function getDefaultMeasure(): string
    {
        return 'expected_revenue';
    }

    public static function getDefaultGroupBy(): string
    {
        return 'stage';
    }

    public static function getDefaultSplitBy(): ?string
    {
        return 'expected_closing';
    }
}
