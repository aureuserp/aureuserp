<?php

namespace Webkul\Account\Filament\Resources\PaymentTermResource\Tables;

use Closure;
use Exception;
use Filament\Actions\Action;
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
use Illuminate\Support\Facades\DB;
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
                    ->action(fn (PaymentTerm $record, DeleteAction $action) => static::runDeletion(
                        fn () => $record->delete(),
                        $action,
                    ))
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('accounts::filament/resources/payment-term.table.actions.delete.notification.title'))
                            ->body(__('accounts::filament/resources/payment-term.table.actions.delete.notification.body'))
                    ),
                ForceDeleteAction::make()
                    ->action(fn (PaymentTerm $record, ForceDeleteAction $action) => static::runDeletion(
                        fn () => $record->forceDelete(),
                        $action,
                    ))
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
                        ->action(fn (Collection $records, DeleteBulkAction $action) => static::runDeletion(
                            fn () => $records->each(fn (Model $record) => $record->delete()),
                            $action,
                        ))
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/payment-term.table.bulk-actions.delete.notification.title'))
                                ->body(__('accounts::filament/resources/payment-term.table.bulk-actions.delete.notification.body'))
                        ),
                    ForceDeleteBulkAction::make()
                        ->action(fn (Collection $records, ForceDeleteBulkAction $action) => static::runDeletion(
                            fn () => $records->each(fn (Model $record) => $record->forceDelete()),
                            $action,
                        ))
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
                                ->title(__('accounts::filament/resources/payment-term.table.bulk-actions.restore.notification.title'))
                                ->body(__('accounts::filament/resources/payment-term.table.bulk-actions.restore.notification.body'))
                        ),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function runDeletion(Closure $deletion, Action $action): void
    {
        try {
            DB::transaction($deletion);
        } catch (Exception $exception) {
            $action->failureNotificationTitle($exception->getMessage());

            $action->failure();

            return;
        }

        $action->success();
    }
}
