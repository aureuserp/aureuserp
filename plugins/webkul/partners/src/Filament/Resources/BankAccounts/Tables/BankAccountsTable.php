<?php

namespace Webkul\Partner\Filament\Resources\BankAccounts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class BankAccountsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('account_number')
                    ->label(__('partners::filament/resources/bank-account.table.columns.account-number'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('bank.name')
                    ->label(__('partners::filament/resources/bank-account.table.columns.bank'))
                    ->numeric()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('partner.name')
                    ->label(__('partners::filament/resources/bank-account.table.columns.account-holder'))
                    ->numeric()
                    ->sortable(),
                IconColumn::make('can_send_money')
                    ->label(__('partners::filament/resources/bank-account.table.columns.send-money'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('deleted_at')
                    ->label(__('partners::filament/resources/bank-account.table.columns.deleted-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('partners::filament/resources/bank-account.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('partners::filament/resources/bank-account.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Group::make('bank.name')
                    ->label(__('partners::filament/resources/bank-account.table.groups.bank')),
                Group::make('can_send_money')
                    ->label(__('partners::filament/resources/bank-account.table.groups.can-send-money')),
                Group::make('created_at')
                    ->label(__('partners::filament/resources/bank-account.table.groups.created-at'))
                    ->date(),
            ])
            ->filters([
                TernaryFilter::make('can_send_money')
                    ->label(__('partners::filament/resources/bank-account.table.filters.can-send-money')),
                SelectFilter::make('bank_id')
                    ->label(__('partners::filament/resources/bank-account.table.filters.bank'))
                    ->relationship('bank', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('partner_id')
                    ->label(__('partners::filament/resources/bank-account.table.filters.account-holder'))
                    ->relationship('partner', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('creator_id')
                    ->label(__('partners::filament/resources/bank-account.table.filters.creator'))
                    ->relationship('creator', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make()
                    ->hidden(fn ($record) => $record->trashed())
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/bank-account.table.actions.edit.notification.title'))
                            ->body(__('partners::filament/resources/bank-account.table.actions.edit.notification.body')),
                    ),
                RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/bank-account.table.actions.restore.notification.title'))
                            ->body(__('partners::filament/resources/bank-account.table.actions.restore.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/bank-account.table.actions.delete.notification.title'))
                            ->body(__('partners::filament/resources/bank-account.table.actions.delete.notification.body')),
                    ),
                ForceDeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/bank-account.table.actions.force-delete.notification.title'))
                            ->body(__('partners::filament/resources/bank-account.table.actions.force-delete.notification.body')),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('partners::filament/resources/bank-account.table.bulk-actions.restore.notification.title'))
                                ->body(__('partners::filament/resources/bank-account.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('partners::filament/resources/bank-account.table.bulk-actions.delete.notification.title'))
                                ->body(__('partners::filament/resources/bank-account.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('partners::filament/resources/bank-account.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('partners::filament/resources/bank-account.table.bulk-actions.force-delete.notification.body')),
                        ),
                ]),
            ]);
    }
}
