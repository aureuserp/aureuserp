<?php

namespace Webkul\TimeOff\Filament\Clusters\Configurations\Resources\AccrualPlans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AccrualPlansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->label(__('time-off::filament/clusters/configurations/resources/accrual-plan.table.columns.name')),
                TextColumn::make('leaveAccrualLevels')
                    ->searchable()
                    ->formatStateUsing(fn ($record) => $record->leaveAccrualLevels?->count())
                    ->label(__('time-off::filament/clusters/configurations/resources/accrual-plan.table.columns.levels')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->title(__('time-off::filament/clusters/configurations/resources/accrual-plan.table.actions.delete.notification.title'))
                            ->body(__('time-off::filament/clusters/configurations/resources/accrual-plan.table.actions.delete.notification.body'))
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->title(__('time-off::filament/clusters/configurations/resources/accrual-plan.table.bulk-actions.delete.notification.title'))
                                ->body(__('time-off::filament/clusters/configurations/resources/accrual-plan.table.bulk-actions.delete.notification.body'))
                        ),
                ]),
            ]);
    }
}
