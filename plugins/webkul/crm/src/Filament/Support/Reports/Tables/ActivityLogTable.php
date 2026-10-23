<?php

namespace Webkul\Crm\Filament\Support\Reports\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Crm\Filament\Support\LeadResourceResolver;
use Webkul\Crm\Models\ActivityLog;
use Webkul\Crm\Models\Lead;

class ActivityLogTable
{
    public static function configure(Table $table): Table
    {
        $label = fn (string $key): string => __('crm::filament/clusters/reporting/report.table.columns.'.$key);

        return $table
            ->columns([
                TextColumn::make('done_at')
                    ->label($label('done-at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('activityType.name')
                    ->label($label('activity-type'))
                    ->badge()
                    ->placeholder('—'),
                TextColumn::make('summary')
                    ->label($label('summary'))
                    ->searchable()
                    ->limit(50)
                    ->placeholder('—'),
                TextColumn::make('lead.name')
                    ->label($label('lead'))
                    ->searchable()
                    ->weight('medium')
                    ->placeholder('—'),
                TextColumn::make('lead_type')
                    ->label($label('type'))
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => $state === Lead::TYPE_LEAD ? __('crm::models/lead.title') : __('crm::models/opportunity.title'))
                    ->color(fn (?string $state): string => $state === Lead::TYPE_LEAD ? 'gray' : 'info')
                    ->toggleable(),
                TextColumn::make('assignedTo.name')
                    ->label($label('assigned-to'))
                    ->placeholder('—'),
                TextColumn::make('doneBy.name')
                    ->label($label('done-by'))
                    ->placeholder('—'),
                TextColumn::make('user.name')
                    ->label($label('salesperson'))
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('team.name')
                    ->label($label('team'))
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('stage.name')
                    ->label($label('stage'))
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('date_deadline')
                    ->label($label('due-date'))
                    ->date()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('done_at', 'desc')
            ->recordUrl(fn (ActivityLog $record): ?string => $record->lead && ! $record->lead->trashed() ? LeadResourceResolver::viewUrl($record->lead) : null);
    }
}
