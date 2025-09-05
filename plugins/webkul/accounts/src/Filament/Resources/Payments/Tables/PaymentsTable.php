<?php

namespace Webkul\Account\Filament\Resources\Payments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Table;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('accounts::filament/resources/payment.table.columns.name'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('company.name')
                    ->label(__('accounts::filament/resources/payment.table.columns.company'))
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('partnerBank.account_holder_name')
                    ->label(__('accounts::filament/resources/payment.table.columns.bank-account-holder'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('pairedInternalTransferPayment.name')
                    ->label(__('accounts::filament/resources/payment.table.columns.paired-internal-transfer-payment'))
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('paymentMethodLine.name')
                    ->placeholder('-')
                    ->label(__('accounts::filament/resources/payment.table.columns.payment-method-line'))
                    ->sortable(),
                TextColumn::make('paymentMethod.name')
                    ->label(__('accounts::filament/resources/payment.table.columns.payment-method'))
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('currency.name')
                    ->label(__('accounts::filament/resources/payment.table.columns.currency'))
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('partner.name')
                    ->label(__('accounts::filament/resources/payment.table.columns.partner'))
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('outstandingAccount.name')
                    ->label(__('accounts::filament/resources/payment.table.columns.outstanding-amount'))
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('destinationAccount.name')
                    ->label(__('accounts::filament/resources/payment.table.columns.destination-account'))
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('createdBy.name')
                    ->label(__('accounts::filament/resources/payment.table.columns.created-by'))
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('paymentTransaction.name')
                    ->label(__('accounts::filament/resources/payment.table.columns.payment-transaction'))
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Tables\Grouping\Group::make('name')
                    ->label(__('accounts::filament/resources/payment.table.groups.name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('company.name')
                    ->label(__('accounts::filament/resources/payment.table.groups.company'))
                    ->collapsible(),
                Tables\Grouping\Group::make('paymentMethodLine.name')
                    ->label(__('accounts::filament/resources/payment.table.groups.payment-method-line'))
                    ->collapsible(),
                Tables\Grouping\Group::make('partner.name')
                    ->label(__('accounts::filament/resources/payment.table.groups.partner'))
                    ->collapsible(),
                Tables\Grouping\Group::make('paymentMethod.name')
                    ->label(__('accounts::filament/resources/payment.table.groups.payment-method'))
                    ->collapsible(),
                Tables\Grouping\Group::make('partnerBank.account_holder_name')
                    ->label(__('accounts::filament/resources/payment.table.groups.partner-bank-account'))
                    ->collapsible(),
                Tables\Grouping\Group::make('pairedInternalTransferPayment.name')
                    ->label(__('accounts::filament/resources/payment.table.groups.paired-internal-transfer-payment'))
                    ->collapsible(),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('accounts::filament/resources/payment.table.groups.created-at'))
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('accounts::filament/resources/payment.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->filtersFormColumns(2)
            ->filters([
                QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        RelationshipConstraint::make('company.name')
                            ->label(__('accounts::filament/resources/payment.table.filters.company'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('accounts::filament/resources/payment.table.filters.company'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('partnerBank.account_holder_name')
                            ->label(__('accounts::filament/resources/payment.table.filters.customer-bank-account'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('accounts::filament/resources/payment.table.filters.customer-bank-account'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('pairedInternalTransferPayment.name')
                            ->label(__('accounts::filament/resources/payment.table.filters.paired-internal-transfer-payment'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('accounts::filament/resources/payment.table.filters.paired-internal-transfer-payment'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('paymentMethodLine.name')
                            ->label(__('accounts::filament/resources/payment.table.filters.payment-method-line'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('accounts::filament/resources/payment.table.filters.payment-method-line'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('paymentMethod.name')
                            ->label(__('accounts::filament/resources/payment.table.filters.payment-method'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('accounts::filament/resources/payment.table.filters.payment-method'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('currency.name')
                            ->label(__('accounts::filament/resources/payment.table.filters.currency'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('accounts::filament/resources/payment.table.filters.currency'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('partner.name')
                            ->label(__('accounts::filament/resources/payment.table.filters.partner'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('accounts::filament/resources/payment.table.filters.partner'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        DateConstraint::make('created_at')
                            ->label(__('accounts::filament/resources/payment.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('accounts::filament/resources/payment.table.filters.updated-at')),
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('accounts::filament/resources/payment.table.actions.delete.notification.title'))
                            ->body(__('accounts::filament/resources/payment.table.actions.delete.notification.body'))
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/payment.table.bulk-actions.delete.notification.title'))
                                ->body(__('accounts::filament/resources/payment.table.bulk-actions.delete.notification.body'))
                        ),
                ]),
            ]);
    }
}
