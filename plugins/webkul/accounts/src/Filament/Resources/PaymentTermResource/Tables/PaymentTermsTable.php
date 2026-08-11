<?php

namespace Webkul\Account\Filament\Resources\PaymentTermResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Webkul\Account\Models\PaymentTerm;

class PaymentTermsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('accounts::filament/resources/payment-term.table.columns.payment-term'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('company.name')
                    ->label(__('accounts::filament/resources/payment-term.table.columns.company'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->label(__('accounts::filament/resources/payment-term.table.columns.created-at'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->label(__('accounts::filament/resources/payment-term.table.columns.updated-at'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Tables\Grouping\Group::make('company.name')
                    ->label(__('accounts::filament/resources/payment-term.table.groups.company-name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('discount_days')
                    ->label(__('accounts::filament/resources/payment-term.table.groups.discount-days'))
                    ->collapsible(),
                Tables\Grouping\Group::make('early_pay_discount')
                    ->label(__('accounts::filament/resources/payment-term.table.groups.early-pay-discount'))
                    ->collapsible(),
                Tables\Grouping\Group::make('name')
                    ->label(__('accounts::filament/resources/payment-term.table.groups.payment-term'))
                    ->collapsible(),
                Tables\Grouping\Group::make('display_on_invoice')
                    ->label(__('accounts::filament/resources/payment-term.table.groups.display-on-invoice'))
                    ->collapsible(),
                Tables\Grouping\Group::make('early_discount')
                    ->label(__('Early Discount'))
                    ->label(__('accounts::filament/resources/payment-term.table.groups.early-discount'))
                    ->collapsible(),
                Tables\Grouping\Group::make('discount_percentage')
                    ->label(__('accounts::filament/resources/payment-term.table.groups.discount-percentage'))
                    ->collapsible(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('accounts::filament/resources/payment-term.table.actions.restore.notification.title'))
                            ->body(__('accounts::filament/resources/payment-term.table.actions.restore.notification.body'))
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('accounts::filament/resources/payment-term.table.actions.delete.notification.title'))
                            ->body(__('accounts::filament/resources/payment-term.table.actions.delete.notification.body'))
                    ),
                ForceDeleteAction::make()
                    ->action(function (PaymentTerm $record, ForceDeleteAction $action) {
                        if ($record->moves()->count() > 0) {
                            $action->failure();

                            return;
                        }

                        $record->forceDelete();

                        $action->success();
                    })
                    ->failureNotification(
                        Notification::make()
                            ->danger()
                            ->title(__('accounts::filament/resources/payment-term.table.actions.force-delete.notification.error.title'))
                            ->body(__('accounts::filament/resources/payment-term.table.actions.force-delete.notification.error.body'))
                    )
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('accounts::filament/resources/payment-term.table.actions.force-delete.notification.success.title'))
                            ->body(__('accounts::filament/resources/payment-term.table.actions.force-delete.notification.success.body'))
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/payment-term.table.bulk-actions.delete.notification.title'))
                                ->body(__('accounts::filament/resources/payment-term.table.bulk-actions.delete.notification.body'))
                        ),
                    ForceDeleteBulkAction::make()
                        ->action(function (Collection $records, ForceDeleteBulkAction $action) {
                            $hasMoves = $records->contains(function ($record) {
                                return $record->moves()->exists();
                            });

                            if ($hasMoves) {
                                $action->failure();

                                return;
                            }

                            $records->each(fn (Model $record) => $record->forceDelete());

                            $action->success();
                        })
                        ->failureNotification(
                            Notification::make()
                                ->danger()
                                ->title(__('accounts::filament/resources/payment-term.table.bulk-actions.force-delete.notification.error.title'))
                                ->body(__('accounts::filament/resources/payment-term.table.bulk-actions.force-delete.notification.error.body'))
                        )
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/payment-term.table.bulk-actions.force-delete.notification.success.title'))
                                ->body(__('accounts::filament/resources/payment-term.table.bulk-actions.force-delete.notification.success.body'))
                        ),
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/payment-term.table.bulk-actions.force-restore.notification.title'))
                                ->body(__('accounts::filament/resources/payment-term.table.bulk-actions.force-restore.notification.body'))
                        ),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
