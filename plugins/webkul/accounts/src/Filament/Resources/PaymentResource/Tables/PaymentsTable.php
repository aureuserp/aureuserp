<?php

namespace Webkul\Account\Filament\Resources\PaymentResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Table;
use Webkul\Account\Filament\Exports\PaymentExporter;
use Webkul\Account\Models\Payment;
use Webkul\Chatter\Filament\Actions\ActivityTableAction;

class PaymentsTable
{
    public static function configure(Table $table, array $customColumns = [], array $customFilters = []): Table
    {
        return $table
            ->reorderableColumns()
            ->columns(array_merge([
                TextColumn::make('date')
                    ->label(__('accounts::filament/resources/payment.table.columns.date'))
                    ->placeholder('-')
                    ->date()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('accounts::filament/resources/payment.table.columns.name'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('journal.name')
                    ->label(__('accounts::filament/resources/payment.table.columns.journal'))
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('paymentMethod.name')
                    ->label(__('accounts::filament/resources/payment.table.columns.payment-method'))
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('partner.name')
                    ->label(__('accounts::filament/resources/payment.table.columns.partner'))
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('amount_company_currency_signed')
                    ->label(__('accounts::filament/resources/payment.table.columns.amount-currency'))
                    ->placeholder('-')
                    ->sortable()
                    ->money(fn (Payment $record) => $record->company?->currency_code, true),
                TextColumn::make('amount')
                    ->label(__('accounts::filament/resources/payment.table.columns.amount'))
                    ->placeholder('-')
                    ->sortable()
                    ->money(fn (Payment $record) => $record->company?->currency_code, true),
                TextColumn::make('state')
                    ->label(__('accounts::filament/resources/payment.table.columns.state'))
                    ->placeholder('-')
                    ->sortable()
                    ->badge(),
                TextColumn::make('company.name')
                    ->label(__('accounts::filament/resources/payment.table.columns.company'))
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ], $customColumns))
            ->groups([
                Tables\Grouping\Group::make('name')
                    ->label(__('accounts::filament/resources/payment.table.groups.name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('company.name')
                    ->label(__('accounts::filament/resources/payment.table.groups.company'))
                    ->collapsible(),
                Tables\Grouping\Group::make('journal.name')
                    ->label(__('accounts::filament/resources/payment.table.groups.journal'))
                    ->collapsible(),
                Tables\Grouping\Group::make('paymentMethodLine.id')
                    ->label(__('accounts::filament/resources/payment.table.groups.payment-method-line'))
                    ->getTitleFromRecordUsing(fn ($record) => $record->paymentMethodLine?->display_name)
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
                Tables\Grouping\Group::make('created_at')
                    ->label(__('accounts::filament/resources/payment.table.groups.created-at'))
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('accounts::filament/resources/payment.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->filtersFormColumns(2)
            ->filters(array_merge([
                QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        RelationshipConstraint::make('journal')
                            ->label(__('accounts::filament/resources/payment.table.filters.journal'))
                            ->icon('heroicon-o-book-open')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('accounts::filament/resources/payment.table.filters.journal'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('company')
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
                        RelationshipConstraint::make('partnerBank')
                            ->label(__('accounts::filament/resources/payment.table.filters.customer-bank-account'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('account_number')
                                    ->label(__('accounts::filament/resources/payment.table.filters.customer-bank-account'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('paymentMethodLine')
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
                        RelationshipConstraint::make('paymentMethod')
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
                        RelationshipConstraint::make('currency')
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
                        RelationshipConstraint::make('partner')
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
            ], $customFilters))
            ->recordActions([
                ActivityTableAction::make(),
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
                ExportAction::make()
                    ->label(__('accounts::filament/resources/payment.table.toolbar-actions.export.label'))
                    ->icon('heroicon-o-arrow-up-tray')
                    ->exporter(PaymentExporter::class),
            ]);
    }
}
