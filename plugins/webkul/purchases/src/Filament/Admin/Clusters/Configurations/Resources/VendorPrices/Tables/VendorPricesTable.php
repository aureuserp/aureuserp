<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\VendorPrices\Tables;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Webkul\Purchase\Models\ProductSupplier;

class VendorPricesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('partner.name')
                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.columns.vendor'))
                    ->searchable(),
                TextColumn::make('product.name')
                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.columns.product'))
                    ->searchable(),
                TextColumn::make('product_name')
                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.columns.vendor-product-name'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('product_code')
                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.columns.vendor-product-code'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('starts_at')
                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.columns.valid-from'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('ends_at')
                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.columns.valid-to'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('company.name')
                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.columns.company'))
                    ->sortable(),
                TextColumn::make('min_qty')
                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.columns.quantity'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('price')
                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.columns.unit-price'))
                    ->sortable(),
                TextColumn::make('discount')
                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.columns.discount'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('currency.name')
                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.columns.currency'))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Tables\Grouping\Group::make('partner.name')
                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.groups.vendor')),
                Tables\Grouping\Group::make('product.name')
                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.groups.product')),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.groups.created-at'))
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->filters([
                SelectFilter::make('partner_id')
                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.filters.vendor'))
                    ->relationship('partner', 'name', fn ($query) => $query->where('sub_type', 'supplier'))
                    ->searchable()
                    ->preload()
                    ->multiple(),

                SelectFilter::make('product_id')
                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.filters.product'))
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                SelectFilter::make('currency_id')
                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.filters.currency'))
                    ->relationship('currency', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                SelectFilter::make('company_id')
                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.filters.company'))
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Filter::make('price_range')
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextInput::make('price_from')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.filters.price-from'))
                                    ->numeric()
                                    ->prefix('From'),
                                TextInput::make('price_to')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.filters.price-to'))
                                    ->numeric()
                                    ->prefix('To'),
                            ])
                            ->columns(2),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['price_from'],
                                fn (Builder $query, $price): Builder => $query->where('price', '>=', $price),
                            )
                            ->when(
                                $data['price_to'],
                                fn (Builder $query, $price): Builder => $query->where('price', '<=', $price),
                            );
                    }),

                Filter::make('min_qty_range')
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextInput::make('min_qty_from')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.filters.min-qty-from'))
                                    ->numeric()
                                    ->prefix('From'),
                                TextInput::make('min_qty_to')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.filters.min-qty-to'))
                                    ->numeric()
                                    ->prefix('To'),
                            ])
                            ->columns(2),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_qty_from'],
                                fn (Builder $query, $qty): Builder => $query->where('min_qty', '>=', $qty),
                            )
                            ->when(
                                $data['min_qty_to'],
                                fn (Builder $query, $qty): Builder => $query->where('min_qty', '<=', $qty),
                            );
                    }),

                Filter::make('validity_period')
                    ->schema([
                        Grid::make()
                            ->schema([
                                DatePicker::make('starts_from')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.filters.starts-from'))
                                    ->native(false),
                                DatePicker::make('ends_before')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.filters.ends-before'))
                                    ->native(false),
                            ])
                            ->columns(2),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['starts_from'],
                                fn (Builder $query, $date): Builder => $query->where('starts_at', '>=', $date),
                            )
                            ->when(
                                $data['ends_before'],
                                fn (Builder $query, $date): Builder => $query->where('ends_at', '<=', $date),
                            );
                    }),

                Filter::make('created_at')
                    ->schema([
                        Grid::make()
                            ->schema([
                                DatePicker::make('created_from')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.filters.created-from'))
                                    ->native(false),
                                DatePicker::make('created_until')
                                    ->label(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.filters.created-until'))
                                    ->native(false),
                            ])
                            ->columns(2),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->action(function (ProductSupplier $record) {
                        try {
                            $record->delete();
                        } catch (QueryException $e) {
                            Notification::make()
                                ->danger()
                                ->title(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.actions.delete.notification.error.title'))
                                ->body(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.actions.delete.notification.error.body'))
                                ->send();
                        }
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.actions.delete.notification.success.title'))
                            ->body(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.actions.delete.notification.success.body')),
                    ),
            ])
            ->toolbarActions([
                DeleteBulkAction::make()
                    ->action(function (Collection $records) {
                        try {
                            $records->each(fn (Model $record) => $record->delete());
                        } catch (QueryException $e) {
                            Notification::make()
                                ->danger()
                                ->title(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.bulk-actions.delete.notification.error.title'))
                                ->body(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.bulk-actions.delete.notification.error.body'))
                                ->send();
                        }
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.bulk-actions.delete.notification.success.title'))
                            ->body(__('purchases::filament/admin/clusters/configurations/resources/vendor-price.table.bulk-actions.delete.notification.success.body')),
                    ),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle'),
            ]);
    }
}
