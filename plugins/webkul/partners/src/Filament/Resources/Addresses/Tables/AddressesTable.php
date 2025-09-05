<?php

namespace Webkul\Partner\Filament\Resources\Addresses\Tables;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Webkul\Partner\Enums\AccountType;

class AddressesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sub_type')
                    ->label(__('partners::filament/resources/address.table.columns.type'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(__('partners::filament/resources/address.table.columns.name'))
                    ->searchable(),
                TextColumn::make('country.name')
                    ->label(__('partners::filament/resources/address.table.columns.country'))
                    ->searchable(),
                TextColumn::make('state.name')
                    ->label(__('partners::filament/resources/address.table.columns.state'))
                    ->searchable(),
                TextColumn::make('street1')
                    ->label(__('partners::filament/resources/address.table.columns.street1'))
                    ->searchable(),
                TextColumn::make('street2')
                    ->label(__('partners::filament/resources/address.table.columns.street2'))
                    ->searchable(),
                TextColumn::make('city')
                    ->label(__('partners::filament/resources/address.table.columns.city'))
                    ->searchable(),
                TextColumn::make('zip')
                    ->label(__('partners::filament/resources/address.table.columns.zip'))
                    ->searchable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('partners::filament/resources/address.table.header-actions.create.label'))
                    ->icon('heroicon-o-plus-circle')
                    ->mutateDataUsing(function (array $data): array {
                        $data['account_type'] = AccountType::ADDRESS;
                        $data['creator_id'] = Auth::id();

                        return $data;
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/address.table.header-actions.create.notification.title'))
                            ->body(__('partners::filament/resources/address.table.header-actions.create.notification.body')),
                    ),
            ])
            ->recordActions([
                EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/address.table.actions.edit.notification.title'))
                            ->body(__('partners::filament/resources/address.table.actions.edit.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/address.table.actions.delete.notification.title'))
                            ->body(__('partners::filament/resources/address.table.actions.delete.notification.body')),
                    ),
            ])
            ->toolbarActions([
                DeleteBulkAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('partners::filament/resources/address.table.bulk-actions.delete.notification.title'))
                            ->body(__('partners::filament/resources/address.table.bulk-actions.delete.notification.body')),
                    ),
            ]);
    }
}
