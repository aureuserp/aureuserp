<?php

namespace Webkul\Account\Filament\Resources\BillResource\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\BooleanConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Filament\Exports\BillExporter;
use Webkul\Chatter\Filament\Actions\ActivityTableAction;

class BillsTable
{
    public static function configure(Table $table, string $resource, array $customColumns = [], array $customFilters = []): Table
    {
        return $table
            ->reorderableColumns()
            ->columns(array_merge([
                TextColumn::make('name')
                    ->placeholder('-')
                    ->label(__('accounts::filament/resources/bill.table.columns.number'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('state')
                    ->placeholder('-')
                    ->label(__('accounts::filament/resources/bill.table.columns.state'))
                    ->sortable()
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('invoice_partner_display_name')
                    ->label(__('accounts::filament/resources/bill.table.columns.customer'))
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('invoice_date')
                    ->date()
                    ->placeholder('-')
                    ->label(__('accounts::filament/resources/bill.table.columns.bill-date'))
                    ->sortable(),
                TextColumn::make('invoice_date_due')
                    ->state(function ($record) {
                        if ($record->payment_state == PaymentState::PAID) {
                            return null;
                        }

                        if (! $record->invoice_date_due) {
                            return '-';
                        }

                        if ($record->invoice_date_due->isToday()) {
                            return 'Today';
                        }

                        return $record->invoice_date_due->diffForHumans();
                    })
                    ->color(function ($record) {
                        if ($record->payment_state == PaymentState::PAID) {
                            return null;
                        }

                        if (! $record->invoice_date_due) {
                            return null;
                        }

                        if ($record->invoice_date_due->isToday()) {
                            return 'warning';
                        }

                        if ($record->invoice_date_due->isPast()) {
                            return 'danger';
                        }

                        return null;
                    })
                    ->placeholder('-')
                    ->label(__('accounts::filament/resources/bill.table.columns.due-date'))
                    ->sortable(),
                TextColumn::make('amount_untaxed_in_currency_signed')
                    ->label(__('accounts::filament/resources/bill.table.columns.tax-excluded'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->money(fn ($record) => $record->currency?->name)
                    ->summarize(Sum::make()->label(__('accounts::filament/resources/bill.table.total')))
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('amount_tax_signed')
                    ->label(__('accounts::filament/resources/bill.table.columns.tax'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->money(fn ($record) => $record->currency?->name)
                    ->summarize(Sum::make()->label(__('accounts::filament/resources/bill.table.total')))
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('amount_total_in_currency_signed')
                    ->label(__('accounts::filament/resources/bill.table.columns.total'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->summarize(Sum::make()->label(__('accounts::filament/resources/bill.table.total')))
                    ->money(fn ($record) => $record->currency?->name)
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('amount_residual_signed')
                    ->label(__('accounts::filament/resources/bill.table.columns.amount-due'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->summarize(Sum::make()->label(__('accounts::filament/resources/bill.table.summarizers.total')))
                    ->money(fn ($record) => $record->currency?->name)
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('payment_state')
                    ->label(__('Payment State'))
                    ->placeholder('-')
                    ->color(fn (PaymentState $state) => $state->getColor())
                    ->icon(fn (PaymentState $state) => $state->getIcon())
                    ->formatStateUsing(fn (PaymentState $state) => $state->getLabel())
                    ->badge()
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                IconColumn::make('checked')
                    ->boolean()
                    ->placeholder('-')
                    ->label(__('accounts::filament/resources/bill.table.columns.checked'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('date')
                    ->date()
                    ->placeholder('-')
                    ->label(__('accounts::filament/resources/bill.table.columns.accounting-date'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('invoice_origin')
                    ->placeholder('-')
                    ->label(__('accounts::filament/resources/bill.table.columns.source-document'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('reference')
                    ->label(__('accounts::filament/resources/bill.table.columns.reference'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('invoiceUser.name')
                    ->label(__('accounts::filament/resources/bill.table.columns.sales-person'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('currency.name')
                    ->label(__('accounts::filament/resources/bill.table.columns.bill-currency'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ], $customColumns))
            ->groups([
                Tables\Grouping\Group::make('name')
                    ->label(__('accounts::filament/resources/bill.table.groups.name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('invoice_partner_display_name')
                    ->label(__('accounts::filament/resources/bill.table.groups.bill-partner-display-name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('invoice_date')
                    ->label(__('accounts::filament/resources/bill.table.groups.bill-date'))
                    ->collapsible(),
                Tables\Grouping\Group::make('checked')
                    ->label(__('accounts::filament/resources/bill.table.groups.checked'))
                    ->collapsible(),
                Tables\Grouping\Group::make('date')
                    ->date()
                    ->label(__('accounts::filament/resources/bill.table.groups.date'))
                    ->collapsible(),
                Tables\Grouping\Group::make('invoice_date_due')
                    ->date()
                    ->label(__('accounts::filament/resources/bill.table.groups.bill-due-date'))
                    ->collapsible(),
                Tables\Grouping\Group::make('invoice_origin')
                    ->label(__('accounts::filament/resources/bill.table.groups.bill-origin'))
                    ->collapsible(),
                Tables\Grouping\Group::make('invoiceUser.name')
                    ->date()
                    ->label(__('accounts::filament/resources/bill.table.groups.sales-person'))
                    ->collapsible(),
                Tables\Grouping\Group::make('currency.name')
                    ->label(__('accounts::filament/resources/bill.table.groups.currency'))
                    ->collapsible(),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('accounts::filament/resources/bill.table.groups.created-at'))
                    ->date()
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('accounts::filament/resources/bill.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->filtersFormColumns(2)
            ->filters(array_merge([
                QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        TextConstraint::make('name')
                            ->label(__('accounts::filament/resources/bill.table.filters.number')),
                        TextConstraint::make('invoice_origin')
                            ->label(__('accounts::filament/resources/bill.table.filters.bill-origin')),
                        TextConstraint::make('reference')
                            ->label(__('accounts::filament/resources/bill.table.filters.reference')),
                        TextConstraint::make('invoice_partner_display_name')
                            ->label(__('accounts::filament/resources/bill.table.filters.bill-partner-display-name')),
                        TextConstraint::make('payment_reference')
                            ->label(__('accounts::filament/resources/bill.table.filters.payment-reference')),
                        TextConstraint::make('narration')
                            ->label(__('accounts::filament/resources/bill.table.filters.narration')),
                        RelationshipConstraint::make('partner')
                            ->label(__('accounts::filament/resources/bill.table.filters.partner'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('journal')
                            ->label(__('accounts::filament/resources/bill.table.filters.journal'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('fiscalPosition')
                            ->label(__('accounts::filament/resources/bill.table.filters.fiscal-position'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('currency')
                            ->label(__('accounts::filament/resources/bill.table.filters.currency'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('company')
                            ->label(__('accounts::filament/resources/bill.table.filters.company'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        DateConstraint::make('date')
                            ->label(__('accounts::filament/resources/bill.table.filters.date')),
                        DateConstraint::make('invoice_date')
                            ->label(__('accounts::filament/resources/bill.table.filters.bill-date')),
                        DateConstraint::make('invoice_date_due')
                            ->label(__('accounts::filament/resources/bill.table.filters.bill-due-date')),
                        DateConstraint::make('delivery_date')
                            ->label(__('accounts::filament/resources/bill.table.filters.delivery-date')),
                        NumberConstraint::make('amount_untaxed')
                            ->label(__('accounts::filament/resources/bill.table.filters.amount-untaxed')),
                        NumberConstraint::make('amount_tax')
                            ->label(__('accounts::filament/resources/bill.table.filters.amount-tax')),
                        NumberConstraint::make('amount_total')
                            ->label(__('accounts::filament/resources/bill.table.filters.amount-total')),
                        NumberConstraint::make('amount_residual')
                            ->label(__('accounts::filament/resources/bill.table.filters.amount-residual')),
                        BooleanConstraint::make('checked')
                            ->label(__('accounts::filament/resources/bill.table.filters.checked')),
                        BooleanConstraint::make('posted_before')
                            ->label(__('accounts::filament/resources/bill.table.filters.posted-before')),
                        BooleanConstraint::make('is_move_sent')
                            ->label(__('accounts::filament/resources/bill.table.filters.is-move-sent')),
                        DateConstraint::make('created_at')
                            ->label(__('accounts::filament/resources/bill.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('accounts::filament/resources/bill.table.filters.updated-at')),
                    ]),
            ], $customFilters))
            ->recordActions([
                ActivityTableAction::make(),
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                        ->hidden(fn (Model $record): bool => $record->state == MoveState::POSTED)
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/bill.table.actions.delete.notification.title'))
                                ->body(__('accounts::filament/resources/bill.table.actions.delete.notification.body'))
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/bill.table.bulk-actions.delete.notification.title'))
                                ->body(__('accounts::filament/resources/bill.table.bulk-actions.delete.notification.body'))
                        ),
                ]),
                ExportAction::make()
                    ->label(__('accounts::filament/resources/bill.table.toolbar-actions.export.label'))
                    ->icon('heroicon-o-arrow-up-tray')
                    ->exporter(BillExporter::class),
            ])
            ->checkIfRecordIsSelectableUsing(
                fn (Model $record): bool => $resource::can('delete', $record) && $record->state !== MoveState::POSTED,
            )
            ->modifyQueryUsing(function (Builder $query) {
                $query->with('currency');
            });
    }
}
