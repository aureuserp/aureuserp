<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\TeamResource\Tables;

use Filament\Actions\Action;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Crm\Filament\Clusters\Sales\Resources\TeamResource;
use Webkul\Sale\Models\Team;

class TeamsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ColorColumn::make('color')
                    ->label(''),
                TextColumn::make('name')
                    ->label(__('crm::filament/clusters/sales/resources/team.table.columns.name'))
                    ->description(fn (Team $record): ?string => $record->user?->name)
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),
                TextColumn::make('open_opportunities_count')
                    ->label(__('crm::filament/clusters/sales/resources/team.table.columns.open-opportunities'))
                    ->badge()
                    ->color('primary')
                    ->sortable(),
                TextColumn::make('open_expected_revenue')
                    ->label(__('crm::filament/clusters/sales/resources/team.table.columns.expected-revenue'))
                    ->money(fn (Team $record): ?string => $record->company?->currency?->code)
                    ->default(0)
                    ->sortable(),
                TextColumn::make('members.name')
                    ->label(__('crm::filament/clusters/sales/resources/team.table.columns.members'))
                    ->badge()
                    ->limitList(3)
                    ->toggleable(),
                TextColumn::make('company.name')
                    ->label(__('crm::filament/clusters/sales/resources/team.table.columns.company'))
                    ->placeholder('—')
                    ->toggleable(),
            ])
            ->defaultSort('sort')
            ->recordUrl(fn (Team $record): string => TeamResource::getUrl('opportunities', ['record' => $record]))
            ->recordActions([
                Action::make('pipeline')
                    ->label(__('crm::filament/clusters/sales/resources/team.table.actions.pipeline.label'))
                    ->icon('heroicon-o-funnel')
                    ->url(fn (Team $record): string => TeamResource::getUrl('opportunities', ['record' => $record])),
            ]);
    }
}
