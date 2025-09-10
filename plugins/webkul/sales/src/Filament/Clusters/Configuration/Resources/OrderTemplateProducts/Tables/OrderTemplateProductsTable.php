<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\OrderTemplateProducts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrderTemplateProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort')
                    ->label(__('sales::filament/clusters/configurations/resources/order-template.table.columns.sort'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('orderTemplate.name')
                    ->label(__('sales::filament/clusters/configurations/resources/order-template.table.columns.order-template'))
                    ->sortable(),
                TextColumn::make('company.name')
                    ->label(__('sales::filament/clusters/configurations/resources/order-template.table.columns.company'))
                    ->sortable(),
                TextColumn::make('product.name')
                    ->label(__('sales::filament/clusters/configurations/resources/order-template.table.columns.product'))
                    ->sortable(),
                TextColumn::make('uom.name')
                    ->label(__('sales::filament/clusters/configurations/resources/order-template.table.columns.product-uom'))
                    ->sortable(),
                TextColumn::make('createdBy.name')
                    ->label(__('sales::filament/clusters/configurations/resources/order-template.table.columns.created-by'))
                    ->sortable(),
                TextColumn::make('display_type')
                    ->label(__('sales::filament/clusters/configurations/resources/order-template.table.columns.display-type'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(__('sales::filament/clusters/configurations/resources/order-template.table.columns.name'))
                    ->searchable(),
                TextColumn::make('quantity')
                    ->label(__('sales::filament/clusters/configurations/resources/order-template.table.columns.quantity'))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('sales::filament/clusters/configurations/resources/order-template.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('sales::filament/clusters/configurations/resources/order-template.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('sales::filament/clusters/configurations/resources/order-template.table.actions.delete.notification.title'))
                            ->body(__('sales::filament/clusters/configurations/resources/order-template.table.actions.delete.notification.body'))
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('sales::filament/clusters/configurations/resources/order-template.table.bulk-actions.delete.notification.title'))
                                ->body(__('sales::filament/clusters/configurations/resources/order-template.table.bulk-actions.delete.notification.body'))
                        ),
                ]),
            ]);
    }
}
