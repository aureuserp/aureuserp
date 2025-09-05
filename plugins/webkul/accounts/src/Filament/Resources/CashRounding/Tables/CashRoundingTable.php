<?php

namespace Webkul\Account\Filament\Resources\CashRounding\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Account\Enums\RoundingMethod;
use Webkul\Account\Enums\RoundingStrategy;

class CashRoundingTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('accounts::filament/resources/cash-rounding.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('strategy')
                    ->label(__('accounts::filament/resources/cash-rounding.table.columns.rounding-strategy'))
                    ->formatStateUsing(fn ($state) => RoundingStrategy::options()[$state] ?? $state)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('rounding_method')
                    ->label(__('accounts::filament/resources/cash-rounding.table.columns.rounding-method'))
                    ->formatStateUsing(fn ($state) => RoundingMethod::options()[$state] ?? $state)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('createdBy.name')
                    ->label(__('accounts::filament/resources/cash-rounding.table.columns.created-by'))
                    ->searchable()
                    ->sortable(),
            ])
            ->groups([
                Tables\Grouping\Group::make('name')
                    ->label(__('accounts::filament/resources/cash-rounding.table.groups.name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('rounding_strategy')
                    ->label(__('accounts::filament/resources/cash-rounding.table.groups.rounding-strategy'))
                    ->collapsible(),
                Tables\Grouping\Group::make('rounding_method')
                    ->label(__('accounts::filament/resources/cash-rounding.table.groups.rounding-method'))
                    ->collapsible(),
                Tables\Grouping\Group::make('createdBy.name')
                    ->label(__('accounts::filament/resources/cash-rounding.table.groups.created-by'))
                    ->collapsible(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('accounts::filament/resources/cash-rounding.table.actions.delete.notification.title'))
                            ->body(__('accounts::filament/resources/cash-rounding.table.actions.delete.notification.body'))
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/cash-rounding.table.actions.delete.notification.title'))
                                ->body(__('accounts::filament/resources/cash-rounding.table.actions.delete.notification.body'))
                        ),
                ]),
            ]);
    }
}
