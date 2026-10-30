<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\TeamResource\Tables;

use Filament\Actions\Action;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Table;
use Webkul\Crm\Filament\Actions\RecordBulkActions;
use Webkul\Crm\Filament\Clusters\Sales\Resources\TeamResource;
use Webkul\Crm\Filament\Exports\TeamExporter;
use Webkul\Crm\Filament\Support\LeadTableFilters;
use Webkul\Sale\Models\Team;

class TeamsTable
{
    public static function configure(Table $table, bool $grid = false): Table
    {
        $table = $grid
            ? $table
                ->columns(static::gridColumns())
                ->contentGrid(['xl' => 2, '2xl' => 3])
                ->recordUrl(null)
                ->recordActions([])
            : $table
                ->columns(static::listColumns())
                ->recordUrl(fn (Team $record): string => TeamResource::getUrl('opportunities', ['record' => $record]))
                ->recordActions([
                    Action::make('pipeline')
                        ->label(__('crm::filament/clusters/sales/resources/team.table.actions.pipeline.label'))
                        ->icon('heroicon-o-funnel')
                        ->url(fn (Team $record): string => TeamResource::getUrl('opportunities', ['record' => $record])),
                ]);

        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['user', 'company.currency']))
            ->filtersFormColumns(2)
            ->filters([
                LeadTableFilters::make([
                    TextConstraint::make('name')->label(__('crm::filament/clusters/sales/resources/team.table.columns.name'))->icon('heroicon-o-user-group'),
                    LeadTableFilters::relationship('user', __('crm::filament/clusters/sales/resources/team.table.filters.team-leader'), 'heroicon-o-user'),
                    LeadTableFilters::relationship('members', __('crm::filament/clusters/sales/resources/team.table.columns.members'), 'heroicon-o-users'),
                    LeadTableFilters::relationship('company', __('crm::filament/clusters/sales/resources/team.table.columns.company'), 'heroicon-o-building-office-2'),
                    DateConstraint::make('created_at')->label(__('crm::filament/clusters/sales/resources/team.table.filters.created-at')),
                ]),
            ])
            ->defaultSort('sort')
            ->toolbarActions([
                RecordBulkActions::make(exporter: TeamExporter::class, softDeletes: true),
            ]);
    }

    public static function listColumns(): array
    {
        return [
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
        ];
    }

    public static function gridColumns(): array
    {
        return [
            Stack::make([
                ViewColumn::make('name')
                    ->label(__('crm::filament/clusters/sales/resources/team.table.columns.name'))
                    ->searchable()
                    ->sortable()
                    ->view('crm::filament.teams.card'),
            ]),
        ];
    }
}
