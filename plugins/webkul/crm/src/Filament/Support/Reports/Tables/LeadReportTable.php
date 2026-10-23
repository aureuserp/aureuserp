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

        $label = fn (string $key): string => __('crm::filament/clusters/reporting/report.table.columns.'.$key);

        return $table
            ->columns([
                TextColumn::make('name')
                    ->label($label('name'))
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                TextColumn::make('type')
                    ->label($label('type'))
                    ->badge()
                    ->formatStateUsing(fn (Lead $record): string => $record->isLead() ? __('crm::models/lead.title') : __('crm::models/opportunity.title'))
                    ->color(fn (Lead $record): string => $record->isLead() ? 'gray' : 'info')
                    ->toggleable(),
                TextColumn::make('partner.name')
                    ->label($label('customer'))
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('stage.name')
                    ->label($label('stage'))
                    ->badge()
                    ->color(fn (Lead $record): string => $record->trashed() ? 'danger' : ($record->isWon() ? 'success' : 'primary'))
                    ->formatStateUsing(fn (Lead $record, ?string $state): ?string => $record->trashed() ? __('crm::filament/clusters/reporting/report.table.lost') : $state),
                TextColumn::make('expected_revenue')
                    ->label($label('expected-revenue'))
                    ->money($currency)
                    ->sortable()
                    ->summarize(Sum::make()->money($currency)),
                TextColumn::make('probability')
                    ->label($label('probability'))
                    ->numeric(decimalPlaces: 0)
                    ->suffix('%')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label($label('salesperson'))
                    ->placeholder('—'),
                TextColumn::make('team.name')
                    ->label($label('team'))
                    ->placeholder('—'),
                TextColumn::make('expected_closing_date')
                    ->label($label('expected-closing'))
                    ->date()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->label($label('created-at'))
                    ->date()
                    ->sortable(),
                TextColumn::make('closed_at')
                    ->label($label('closed-at'))
                    ->date()
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('lostReason.name')
                    ->label($label('lost-reason'))
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('country.name')
                    ->label($label('country'))
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('city')
                    ->label($label('city'))
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordUrl(fn (Lead $record): ?string => $record->trashed() ? null : LeadResourceResolver::viewUrl($record));
    }
}
