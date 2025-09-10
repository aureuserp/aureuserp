<?php

namespace Webkul\Product\Filament\Resources\Packaging\Tables;

use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Webkul\Product\Models\Packaging;

class PackagingTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('products::filament/resources/packaging.table.columns.name'))
                    ->searchable(),
                TextColumn::make('product.name')
                    ->label(__('products::filament/resources/packaging.table.columns.product'))
                    ->searchable()
                    ->numeric()
                    ->sortable(),
                TextColumn::make('qty')
                    ->label(__('products::filament/resources/packaging.table.columns.qty'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('barcode')
                    ->label(__('products::filament/resources/packaging.table.columns.barcode'))
                    ->searchable(),
                TextColumn::make('company.name')
                    ->label(__('products::filament/resources/packaging.table.columns.company'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('products::filament/resources/packaging.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('products::filament/resources/packaging.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Group::make('product.name')
                    ->label(__('products::filament/resources/packaging.table.groups.product'))
                    ->collapsible(),
                Group::make('created_at')
                    ->label(__('products::filament/resources/packaging.table.groups.created-at'))
                    ->collapsible(),
                Group::make('updated_at')
                    ->label(__('products::filament/resources/packaging.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->filters([
                SelectFilter::make('product')
                    ->label(__('products::filament/resources/packaging.table.filters.product'))
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('products::filament/resources/packaging.table.actions.edit.notification.title'))
                            ->body(__('products::filament/resources/packaging.table.actions.edit.notification.body')),
                    ),
                DeleteAction::make()
                    ->action(function (Packaging $record) {
                        try {
                            $record->delete();
                        } catch (QueryException $e) {
                            Notification::make()
                                ->danger()
                                ->title(__('products::filament/resources/packaging.table.actions.delete.notification.error.title'))
                                ->body(__('products::filament/resources/packaging.table.actions.delete.notification.error.body'))
                                ->send();
                        }
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('products::filament/resources/packaging.table.actions.delete.notification.success.title'))
                            ->body(__('products::filament/resources/packaging.table.actions.delete.notification.success.body')),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('print')
                        ->label(__('products::filament/resources/packaging.table.bulk-actions.print.label'))
                        ->icon('heroicon-o-printer')
                        ->action(function ($records) {
                            $pdf = PDF::loadView('products::filament.resources.packagings.actions.print', [
                                'records' => $records,
                            ]);

                            $pdf->setPaper('a4', 'portrait');

                            return response()->streamDownload(function () use ($pdf) {
                                echo $pdf->output();
                            }, 'Packaging-Barcode.pdf');
                        }),
                    DeleteBulkAction::make()
                        ->action(function (Collection $records) {
                            try {
                                $records->each(fn (Model $record) => $record->delete());
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('products::filament/resources/packaging.table.bulk-actions.delete.notification.error.title'))
                                    ->body(__('products::filament/resources/packaging.table.bulk-actions.delete.notification.error.body'))
                                    ->send();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('products::filament/resources/packaging.table.bulk-actions.delete.notification.success.title'))
                                ->body(__('products::filament/resources/packaging.table.bulk-actions.delete.notification.success.body')),
                        ),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->label(__('products::filament/resources/packaging.table.empty-state-actions.create.label'))
                    ->icon('heroicon-o-plus-circle')
                    ->mutateDataUsing(function (array $data): array {
                        $data['creator_id'] = Auth::id();

                        return $data;
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('products::filament/resources/packaging.table.empty-state-actions.create.notification.title'))
                            ->body(__('products::filament/resources/packaging.table.empty-state-actions.create.notification.body')),
                    ),
            ]);
    }
}
