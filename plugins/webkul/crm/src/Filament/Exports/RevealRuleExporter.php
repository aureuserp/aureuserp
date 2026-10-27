<?php

namespace Webkul\Crm\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Webkul\Crm\Models\RevealRule;

class RevealRuleExporter extends CRMRecordExporter
{
    protected static ?string $model = RevealRule::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')->label(__('crm::filament/clusters/configurations/resources/reveal-rule.table.name')),
            ExportColumn::make('regex_url')->label(__('crm::filament/clusters/configurations/resources/reveal-rule.table.url')),
            ExportColumn::make('countries.name')->label(__('crm::filament/clusters/configurations/resources/reveal-rule.table.countries')),
            ExportColumn::make('lead_type')->label(__('crm::filament/clusters/configurations/resources/reveal-rule.table.type'))->formatStateUsing(fn (mixed $state): string => static::enumLabel($state)),
            ExportColumn::make('team.name')->label(__('crm::filament/clusters/configurations/resources/reveal-rule.table.team')),
            ExportColumn::make('user.name')->label(__('crm::filament/clusters/configurations/resources/reveal-rule.table.salesperson')),
            ExportColumn::make('is_active')->label(__('crm::filament/clusters/configurations/resources/reveal-rule.table.active')),
        ];
    }

    protected static function getFileNamePrefix(): string
    {
        return 'reveal-rules';
    }
}
