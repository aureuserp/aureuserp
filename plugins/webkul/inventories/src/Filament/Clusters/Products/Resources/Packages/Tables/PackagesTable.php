<?php

namespace Webkul\Inventory\Filament\Clusters\Products\Resources\Packages\Tables;

use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Webkul\Inventory\Models\Package;

class PackagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('inventories::filament/clusters/products/resources/package.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('packageType.name')
                    ->label(__('inventories::filament/clusters/products/resources/package.table.columns.package-type'))
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('location.full_name')
                    ->label(__('inventories::filament/clusters/products/resources/package.table.columns.location'))
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('company.name')
                    ->label(__('inventories::filament/clusters/products/resources/package.table.columns.company'))
                    ->sortable(),
            ])
            ->groups([
                Tables\Grouping\Group::make('packageType.name')
                    ->label(__('inventories::filament/clusters/products/resources/package.table.groups.package-type')),
                Tables\Grouping\Group::make('location.full_name')
                    ->label(__('inventories::filament/clusters/products/resources/package.table.groups.location')),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('inventories::filament/clusters/products/resources/package.table.groups.created-at'))
                    ->date(),
            ])
            ->filters([
                SelectFilter::make('package_type_id')
                    ->label(__('inventories::filament/clusters/products/resources/package.table.filters.package-type'))
                    ->relationship('packageType', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('location_id')
                    ->label(__('inventories::filament/clusters/products/resources/package.table.filters.location'))
                    ->relationship('location', 'full_name')
                    ->searchable()
                    ->multiple()
                    ->preload(),
                SelectFilter::make('creator_id')
                    ->label(__('inventories::filament/clusters/products/resources/package.table.filters.creator'))
                    ->relationship('creator', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('company_id')
                    ->label(__('inventories::filament/clusters/products/resources/package.table.filters.company'))
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                        ->action(function (Package $record) {
                            try {
                                $record->delete();
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('inventories::filament/clusters/products/resources/package.table.actions.delete.notification.error.title'))
                                    ->body(__('inventories::filament/clusters/products/resources/package.table.actions.delete.notification.error.body'))
                                    ->send();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/products/resources/package.table.actions.delete.notification.success.title'))
                                ->body(__('inventories::filament/clusters/products/resources/package.table.actions.delete.notification.success.body')),
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('print-without-content')
                        ->label(__('inventories::filament/clusters/products/resources/package.table.bulk-actions.print-without-content.label'))
                        ->icon('heroicon-o-printer')
                        ->action(function ($records) {
                            $pdf = PDF::loadView('inventories::filament.clusters.products.packages.actions.print-without-content', [
                                'records' => $records,
                            ]);

                            $pdf->setPaper('a4', 'portrait');

                            return response()->streamDownload(function () use ($pdf) {
                                echo $pdf->output();
                            }, 'Package-Barcode.pdf');
                        }),
                    BulkAction::make('print-with-content')
                        ->label(__('inventories::filament/clusters/products/resources/package.table.bulk-actions.print-with-content.label'))
                        ->icon('heroicon-o-printer')
                        ->action(function ($records) {
                            $pdf = PDF::loadView('inventories::filament.clusters.products.packages.actions.print-with-content', [
                                'records' => $records,
                            ]);

                            $pdf->setPaper('a4', 'portrait');

                            return response()->streamDownload(function () use ($pdf) {
                                echo $pdf->output();
                            }, 'Package-Barcode.pdf');
                        }),
                    DeleteBulkAction::make()
                        ->action(function (Collection $records) {
                            try {
                                $records->each(fn (Model $record) => $record->delete());
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('inventories::filament/clusters/products/resources/package.table.bulk-actions.delete.notification.error.title'))
                                    ->body(__('inventories::filament/clusters/products/resources/package.table.bulk-actions.delete.notification.error.body'))
                                    ->send();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/products/resources/package.table.bulk-actions.delete.notification.success.title'))
                                ->body(__('inventories::filament/clusters/products/resources/package.table.bulk-actions.delete.notification.success.body')),
                        ),
                ]),
            ]);
    }
}
