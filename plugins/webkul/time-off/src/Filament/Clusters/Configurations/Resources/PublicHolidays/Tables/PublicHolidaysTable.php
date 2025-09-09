<?php

namespace Webkul\TimeOff\Filament\Clusters\Configurations\Resources\PublicHolidays\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PublicHolidaysTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.table.columns.name')),
                TextColumn::make('date_from')
                    ->sortable()
                    ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.table.columns.date-from')),
                TextColumn::make('date_to')
                    ->sortable()
                    ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.table.columns.date-to')),
                TextColumn::make('calendar.name')
                    ->sortable()
                    ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.table.columns.calendar')),
            ])
            ->groups([
                Tables\Grouping\Group::make('date_from')
                    ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.table.groups.date-from'))
                    ->collapsible(),
                Tables\Grouping\Group::make('date_to')
                    ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.table.groups.date-to'))
                    ->collapsible(),
                Tables\Grouping\Group::make('company.name')
                    ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.table.groups.company-name'))
                    ->collapsible(),
            ])
            ->filters([
                SelectFilter::make('company_id')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload()
                    ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.table.filters.company-name')),
                SelectFilter::make('creator_id')
                    ->relationship('createdBy', 'name')
                    ->searchable()
                    ->preload()
                    ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.table.filters.created-by')),
                QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        TextConstraint::make('name')
                            ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.table.filters.name'))
                            ->icon('heroicon-o-clock'),
                        TextConstraint::make('date_from')
                            ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.table.filters.date-from'))
                            ->icon('heroicon-o-calendar'),
                        TextConstraint::make('date_to')
                            ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.table.filters.date-to'))
                            ->icon('heroicon-o-calendar'),
                        DateConstraint::make('created_at')
                            ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('time-off::filament/clusters/configurations/resources/public-holiday.table.filters.updated-at')),
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('time-off::filament/clusters/configurations/resources/public-holiday.table.actions.edit.notification.title'))
                            ->body(__('time-off::filament/clusters/configurations/resources/public-holiday.table.actions.edit.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('time-off::filament/clusters/configurations/resources/public-holiday.table.actions.delete.notification.title'))
                            ->body(__('time-off::filament/clusters/configurations/resources/public-holiday.table.actions.delete.notification.body')),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('time-off::filament/clusters/configurations/resources/public-holiday.table.bulk-actions.delete.notification.title'))
                                ->body(__('time-off::filament/clusters/configurations/resources/public-holiday.table.bulk-actions.delete.notification.body')),
                        ),
                ]),
            ]);
    }
}
