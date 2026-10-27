<?php

namespace Webkul\Crm\Filament\Support\Reports\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Crm\Filament\Support\LeadResourceResolver;
use Webkul\Crm\Models\ActivityLog;

class ActivityLogTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('done_at')
                    ->label(__('crm::filament/clusters/reporting/report.table.columns.done-at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('activityType.name')
                    ->label(__('crm::filament/clusters/reporting/report.table.columns.activity-type'))
                    ->badge()
                    ->placeholder('—'),
                TextColumn::make('summary')
                    ->label(__('crm::filament/clusters/reporting/report.table.columns.summary'))
                    ->searchable()
                    ->limit(50)
                    ->placeholder('—'),
                TextColumn::make('lead.name')
                    ->label(__('crm::filament/clusters/reporting/report.table.columns.lead'))
                    ->searchable()
                    ->weight('medium')
                    ->placeholder('—'),
                TextColumn::make('lead_type')
                    ->label(__('crm::filament/clusters/reporting/report.table.columns.type'))
                    ->badge()
                    ->toggleable(),
                TextColumn::make('assignedTo.name')
                    ->label(__('crm::filament/clusters/reporting/report.table.columns.assigned-to'))
                    ->placeholder('—'),
                TextColumn::make('doneBy.name')
                    ->label(__('crm::filament/clusters/reporting/report.table.columns.done-by'))
                    ->placeholder('—'),
                TextColumn::make('user.name')
                    ->label(__('crm::filament/clusters/reporting/report.table.columns.salesperson'))
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('team.name')
                    ->label(__('crm::filament/clusters/reporting/report.table.columns.team'))
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('stage.name')
                    ->label(__('crm::filament/clusters/reporting/report.table.columns.stage'))
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('date_deadline')
                    ->label(__('crm::filament/clusters/reporting/report.table.columns.due-date'))
                    ->date()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('done_at', 'desc')
            ->recordUrl(fn (ActivityLog $record): ?string => $record->lead && ! $record->lead->trashed() ? LeadResourceResolver::viewUrl($record->lead) : null);
    }
}
