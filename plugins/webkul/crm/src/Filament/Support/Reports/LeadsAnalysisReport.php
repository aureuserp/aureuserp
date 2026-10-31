<?php

namespace Webkul\Crm\Filament\Support\Reports;

class LeadsAnalysisReport extends LeadReport
{
    protected static function getPresetDefaults(): array
    {
        return [
            'created_this_year' => true,
            'my_pipeline'       => false,
            'opportunities'     => false,
            'leads'             => false,
            'active'            => false,
            'lost'              => false,
            'won'               => false,
            'upcoming_closings' => false,
            'closed_this_year'  => false,
        ];
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
