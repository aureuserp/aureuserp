<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\Quotations\Tables;

use Filament\Actions\ActionGroup;
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
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Filament\Clusters\Orders\Resources\Quotations\QuotationResource;

class QuotationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.number'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('state')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.status'))
                    ->placeholder('-')
                    ->badge()
                    ->sortable(),
                TextColumn::make('invoice_status')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.invoice-status'))
                    ->placeholder('-')
                    ->badge()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.creation-date'))
                    ->placeholder('-')
                    ->date()
                    ->sortable(),
                TextColumn::make('amount_untaxed')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.untaxed-amount'))
                    ->placeholder('-')
                    ->summarize(Sum::make()->label('Total'))
                    ->money(fn ($record) => $record->currency->code)
                    ->sortable(),
                TextColumn::make('amount_tax')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.amount-tax'))
                    ->placeholder('-')
                    ->summarize(Sum::make()->label('Taxes'))
                    ->money(fn ($record) => $record->currency->code)
                    ->sortable(),
                TextColumn::make('amount_total')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.amount-total'))
                    ->placeholder('-')
                    ->summarize(Sum::make()->label('Total Amount'))
                    ->money(fn ($record) => $record->currency->code)
                    ->sortable(),
                TextColumn::make('commitment_date')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.commitment-date'))
                    ->placeholder('-')
                    ->date()
                    ->sortable(),
                TextColumn::make('expected_date')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.expected-date'))
                    ->placeholder('-')
                    ->date()
                    ->sortable(),
                TextColumn::make('partner.name')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.customer'))
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.sales-person'))
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('team.name')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.sales-team'))
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('client_order_ref')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.columns.customer-reference'))
                    ->placeholder('-')
                    ->badge()
                    ->searchable()
                    ->sortable(),
            ])
            ->filtersFormColumns(2)
            ->filters([
                QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        RelationshipConstraint::make('user.name')
                            ->label(__('sales::filament/clusters/orders/resources/quotation.table.filters.sales-person'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.filters.sales-person'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('utm_source_id.name')
                            ->label(__('sales::filament/clusters/orders/resources/quotation.table.filters.utm-source'))
                            ->icon('heroicon-o-speaker-wave')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.filters.utm-source'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('company.name')
                            ->label(__('sales::filament/clusters/orders/resources/quotation.table.filters.company'))
                            ->icon('heroicon-o-building-office')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.filters.company'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('partner.name')
                            ->label(__('sales::filament/clusters/orders/resources/quotation.table.filters.customer'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.filters.customer'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('journal.name')
                            ->label(__('sales::filament/clusters/orders/resources/quotation.table.filters.journal'))
                            ->icon('heroicon-o-speaker-wave')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.filters.journal'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('partnerInvoice.name')
                            ->label(__('sales::filament/clusters/orders/resources/quotation.table.filters.invoice-address'))
                            ->icon('heroicon-o-map')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.filters.invoice-address'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('partnerShipping.name')
                            ->label(__('sales::filament/clusters/orders/resources/quotation.table.filters.shipping-address'))
                            ->icon('heroicon-o-map')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.filters.shipping-address'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('fiscalPosition.name')
                            ->label(__('sales::filament/clusters/orders/resources/quotation.table.filters.fiscal-position'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.filters.fiscal-position'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('paymentTerm.name')
                            ->label(__('sales::filament/clusters/orders/resources/quotation.table.filters.payment-term'))
                            ->icon('heroicon-o-currency-dollar')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.filters.payment-term'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('currency.name')
                            ->label(__('sales::filament/clusters/orders/resources/quotation.table.filters.currency'))
                            ->icon('heroicon-o-banknotes')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.filters.currency'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        DateConstraint::make('created_at')
                            ->label(__('sales::filament/clusters/orders/resources/quotation.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('sales::filament/clusters/orders/resources/quotation.table.filters.updated-at')),
                    ]),
            ])
            ->groups([
                Tables\Grouping\Group::make('medium.name')
                    ->label(__('Medium'))
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.groups.medium'))
                    ->collapsible(),
                Tables\Grouping\Group::make('utmSource.name')
                    ->label(__('Source'))
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.groups.source'))
                    ->collapsible(),
                Tables\Grouping\Group::make('team.name')
                    ->label(__('Team'))
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.groups.team'))
                    ->collapsible(),
                Tables\Grouping\Group::make('user.name')
                    ->label(__('Sales Person'))
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.groups.sales-person'))
                    ->collapsible(),
                Tables\Grouping\Group::make('currency.full_name')
                    ->label(__('Currency'))
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.groups.currency'))
                    ->collapsible(),
                Tables\Grouping\Group::make('company.name')
                    ->label(__('Company'))
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.groups.company'))
                    ->collapsible(),
                Tables\Grouping\Group::make('partner.name')
                    ->label(__('Customer'))
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.groups.customer'))
                    ->collapsible(),
                Tables\Grouping\Group::make('date_order')
                    ->label(__('Quotation Date'))
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.groups.quotation-date'))
                    ->date()
                    ->collapsible(),
                Tables\Grouping\Group::make('commitment_date')
                    ->label(__('Commitment Date'))
                    ->label(__('sales::filament/clusters/orders/resources/quotation.table.groups.commitment-date'))
                    ->date()
                    ->collapsible(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                        ->hidden(fn (Model $record) => $record->state == OrderState::SALE)
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('sales::filament/clusters/orders/resources/quotation.table.actions.delete.notification.title'))
                                ->body(__('sales::filament/clusters/orders/resources/quotation.table.actions.delete.notification.body'))
                        ),
                    ForceDeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('sales::filament/clusters/orders/resources/quotation.table.actions.force-delete.notification.title'))
                                ->body(__('sales::filament/clusters/orders/resources/quotation.table.actions.force-delete.notification.body'))
                        ),
                    RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('sales::filament/clusters/orders/resources/quotation.table.actions.restore.notification.title'))
                                ->body(__('sales::filament/clusters/orders/resources/quotation.table.actions.restore.notification.body'))
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('sales::filament/clusters/orders/resources/quotation.table.bulk-actions.delete.notification.title'))
                                ->body(__('sales::filament/clusters/orders/resources/quotation.table.bulk-actions.delete.notification.body'))
                        ),
                    ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('sales::filament/clusters/orders/resources/quotation.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('sales::filament/clusters/orders/resources/quotation.table.bulk-actions.force-delete.notification.body'))
                        ),
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('sales::filament/clusters/orders/resources/quotation.table.bulk-actions.restore.notification.title'))
                                ->body(__('sales::filament/clusters/orders/resources/quotation.table.bulk-actions.restore.notification.body'))
                        ),
                ]),
            ])
            ->checkIfRecordIsSelectableUsing(
                fn (Model $record): bool => QuotationResource::can('delete', $record) && $record->state !== OrderState::SALE,
            );
    }
}
