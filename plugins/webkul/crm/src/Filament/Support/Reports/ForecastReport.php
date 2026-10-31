<?php

namespace Webkul\Crm\Filament\Support\Reports;

class ForecastReport extends LeadReport
{
    public static function dimensions(): array
    {
        return collect(parent::dimensions())->except(['type'])->all();
    }

    protected static function getPresetDefaults(): array
    {
        return [
            'upcoming_closings' => true,
            'my_pipeline'       => true,
            'unassigned'        => false,
            'active'            => true,
            'ongoing'           => false,
            'won'               => false,
            'lost'              => false,
            'created_this_year' => false,
            'closed_this_year'  => false,
        ];
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
