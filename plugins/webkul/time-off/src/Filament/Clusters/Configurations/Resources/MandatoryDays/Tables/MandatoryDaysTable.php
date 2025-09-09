<?php

namespace Webkul\TimeOff\Filament\Clusters\Configurations\Resources\MandatoryDays\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class MandatoryDaysTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.columns.name'))
                    ->sortable(),
                TextColumn::make('company.name')
                    ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.columns.company-name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('createdBy.name')
                    ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.columns.created-by'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('start_date')
                    ->date()
                    ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.columns.start-date'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->date()
                    ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.columns.end-date'))
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('company_id')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload()
                    ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.filters.company-name')),
                SelectFilter::make('creator_id')
                    ->relationship('createdBy', 'name')
                    ->searchable()
                    ->preload()
                    ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.filters.created-by')),
                QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        TextConstraint::make('name')
                            ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.filters.name'))
                            ->icon('heroicon-o-clock'),
                        TextConstraint::make('start_date')
                            ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.filters.start-date'))
                            ->icon('heroicon-o-calendar'),
                        TextConstraint::make('end_date')
                            ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.filters.end-date'))
                            ->icon('heroicon-o-calendar'),
                        DateConstraint::make('created_at')
                            ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.filters.updated-at')),
                    ]),
            ])
            ->groups([
                Group::make('name')
                    ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.groups.name'))
                    ->collapsible(),
                Group::make('createdBy.name')
                    ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.groups.created-by'))
                    ->collapsible(),
                Group::make('company.name')
                    ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.groups.company-name'))
                    ->collapsible(),
                Group::make('start_date')
                    ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.groups.start-date'))
                    ->collapsible(),
                Group::make('end_date')
                    ->label(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.groups.end-date'))
                    ->collapsible(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.actions.edit.notification.title'))
                            ->body(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.actions.edit.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.actions.delete.notification.title'))
                            ->body(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.actions.delete.notification.body')),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.bulk-actions.delete.notification.title'))
                                ->body(__('time-off::filament/clusters/configurations/resources/mandatory-days.table.bulk-actions.delete.notification.body')),
                        ),
                ]),
            ]);
    }
}
