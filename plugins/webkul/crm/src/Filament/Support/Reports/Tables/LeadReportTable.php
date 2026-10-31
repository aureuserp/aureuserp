<?php

namespace Webkul\Crm\Filament\Support\Reports\Tables;

use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Crm\Filament\Support\LeadResourceResolver;
use Webkul\Crm\Filament\Support\Reports\Report;
use Webkul\Crm\Models\Lead;

class LeadReportTable
{
    public static function configure(Table $table): Table
    {
        $currency = Report::getCurrencyCode();

        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('crm::filament/clusters/reporting/report.table.columns.name'))
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                TextColumn::make('type')
                    ->label(__('crm::filament/clusters/reporting/report.table.columns.type'))
                    ->badge()
                    ->toggleable(),
                TextColumn::make('partner.name')
                    ->label(__('crm::filament/clusters/reporting/report.table.columns.customer'))
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('stage.name')
                    ->label(__('crm::filament/clusters/reporting/report.table.columns.stage'))
                    ->badge()
                    ->color(fn (Lead $record): string => $record->trashed() ? 'danger' : ($record->isWon() ? 'success' : 'primary'))
                    ->formatStateUsing(fn (Lead $record, ?string $state): ?string => $record->trashed() ? __('crm::filament/clusters/reporting/report.table.lost') : $state),
                TextColumn::make('expected_revenue')
                    ->label(__('crm::filament/clusters/reporting/report.table.columns.expected-revenue'))
                    ->money($currency)
                    ->sortable()
                    ->summarize(Sum::make()->money($currency)),
                TextColumn::make('probability')
                    ->label(__('crm::filament/clusters/reporting/report.table.columns.probability'))
                    ->numeric(decimalPlaces: 0)
                    ->suffix('%')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label(__('crm::filament/clusters/reporting/report.table.columns.salesperson'))
                    ->placeholder('—'),
                TextColumn::make('team.name')
                    ->label(__('crm::filament/clusters/reporting/report.table.columns.team'))
                    ->placeholder('—'),
                TextColumn::make('expected_closing_date')
                    ->label(__('crm::filament/clusters/reporting/report.table.columns.expected-closing'))
                    ->date()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->label(__('crm::filament/clusters/reporting/report.table.columns.created-at'))
                    ->date()
                    ->sortable(),
                TextColumn::make('closed_at')
                    ->label(__('crm::filament/clusters/reporting/report.table.columns.closed-at'))
                    ->date()
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('lostReason.name')
                    ->label(__('crm::filament/clusters/reporting/report.table.columns.lost-reason'))
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('country.name')
                    ->label(__('crm::filament/clusters/reporting/report.table.columns.country'))
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('city')
                    ->label(__('crm::filament/clusters/reporting/report.table.columns.city'))
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordUrl(fn (Lead $record): ?string => $record->trashed() ? null : LeadResourceResolver::viewUrl($record));
    }
}
