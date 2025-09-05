<?php

namespace Webkul\Account\Filament\Resources\Accounts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AccountsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->searchable()
                    ->label(__('accounts::filament/resources/account.table.columns.code')),
                TextColumn::make('name')
                    ->searchable()
                    ->label(__('accounts::filament/resources/account.table.columns.account-name')),
                TextColumn::make('account_type')
                    ->searchable()
                    ->label(__('accounts::filament/resources/account.table.columns.account-type')),
                TextColumn::make('currency.name')
                    ->searchable()
                    ->label(__('accounts::filament/resources/account.table.columns.currency')),
                IconColumn::make('deprecated')
                    ->boolean()
                    ->label(__('accounts::filament/resources/account.table.columns.deprecated')),
                IconColumn::make('reconcile')
                    ->boolean()
                    ->label(__('accounts::filament/resources/account.table.columns.reconcile')),
                IconColumn::make('non_trade')
                    ->boolean()
                    ->label(__('accounts::filament/resources/account.table.columns.non-trade')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('accounts::filament/resources/account.table.actions.delete.notification.title'))
                            ->body(__('accounts::filament/resources/account.table.actions.delete.notification.body'))
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/account.table.bulk-options.delete.notification.title'))
                                ->body(__('accounts::filament/resources/account.table.bulk-options.delete.notification.body'))
                        ),
                ]),
            ]);
    }
}
