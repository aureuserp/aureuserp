<?php

namespace Webkul\Crm\Filament\Support\Reports;

class PipelineReport extends LeadReport
{
    public static function dimensions(): array
    {
        return collect(parent::dimensions())->except(['type'])->all();
    }

    protected static function getPresetDefaults(): array
    {
        return [
            'active'            => true,
            'my_pipeline'       => false,
            'unassigned'        => false,
            'upcoming_closings' => false,
            'ongoing'           => false,
            'won'               => false,
            'lost'              => false,
            'created_this_year' => false,
            'closed_this_year'  => false,
        ];
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
