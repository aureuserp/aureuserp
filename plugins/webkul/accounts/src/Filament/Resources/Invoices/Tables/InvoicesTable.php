<?php

namespace Webkul\Account\Filament\Resources\Invoices\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Table;
use Webkul\Account\Enums\PaymentState;

class InvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->placeholder('-')
                    ->label(__('accounts::filament/resources/invoice.table.columns.number'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('state')
                    ->placeholder('-')
                    ->label(__('accounts::filament/resources/invoice.table.columns.state'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('invoice_partner_display_name')
                    ->label(__('accounts::filament/resources/invoice.table.columns.customer'))
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('invoice_date')
                    ->date()
                    ->placeholder('-')
                    ->label(__('accounts::filament/resources/invoice.table.columns.invoice-date'))
                    ->sortable(),
                TextColumn::make('invoice_date_due')
                    ->date()
                    ->placeholder('-')
                    ->label(__('accounts::filament/resources/invoice.table.columns.due-date'))
                    ->sortable(),
                TextColumn::make('amount_untaxed_in_currency_signed')
                    ->label(__('accounts::filament/resources/invoice.table.columns.tax-excluded'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->money(fn ($record) => $record->currency->code)
                    ->summarize(Sum::make()->label(__('accounts::filament/resources/invoice.table.total')))
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('amount_tax_signed')
                    ->label(__('accounts::filament/resources/invoice.table.columns.tax'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->money(fn ($record) => $record->currency->code)
                    ->summarize(Sum::make()->label(__('accounts::filament/resources/invoice.table.total')))
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('amount_total_in_currency_signed')
                    ->label(__('accounts::filament/resources/invoice.table.columns.total'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->summarize(Sum::make()->label(__('accounts::filament/resources/invoice.table.total')))
                    ->money(fn ($record) => $record->currency->code)
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('amount_residual_signed')
                    ->label(__('accounts::filament/resources/invoice.table.columns.amount-due'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->summarize(Sum::make()->label('Total'))
                    ->money(fn ($record) => $record->currency->code)
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
                    ->label(__('accounts::filament/resources/invoice.table.columns.checked'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('date')
                    ->date()
                    ->placeholder('-')
                    ->label(__('accounts::filament/resources/invoice.table.columns.accounting-date'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('invoice_origin')
                    ->placeholder('-')
                    ->label(__('accounts::filament/resources/invoice.table.columns.source-document'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('reference')
                    ->label(__('accounts::filament/resources/invoice.table.columns.reference'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('invoiceUser.name')
                    ->label(__('accounts::filament/resources/invoice.table.columns.sales-person'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('currency.name')
                    ->label(__('accounts::filament/resources/invoice.table.columns.invoice-currency'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Tables\Grouping\Group::make('name')
                    ->label(__('accounts::filament/resources/invoice.table.groups.name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('invoice_partner_display_name')
                    ->label(__('accounts::filament/resources/invoice.table.groups.invoice-partner-display-name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('invoice_date')
                    ->label(__('accounts::filament/resources/invoice.table.groups.invoice-date'))
                    ->collapsible(),
                Tables\Grouping\Group::make('checked')
                    ->label(__('accounts::filament/resources/invoice.table.groups.checked'))
                    ->collapsible(),
                Tables\Grouping\Group::make('date')
                    ->date()
                    ->label(__('accounts::filament/resources/invoice.table.groups.date'))
                    ->collapsible(),
                Tables\Grouping\Group::make('invoice_date_due')
                    ->date()
                    ->label(__('accounts::filament/resources/invoice.table.groups.invoice-due-date'))
                    ->collapsible(),
                Tables\Grouping\Group::make('invoice_origin')
                    ->label(__('accounts::filament/resources/invoice.table.groups.invoice-origin'))
                    ->collapsible(),
                Tables\Grouping\Group::make('invoiceUser.name')
                    ->date()
                    ->label(__('accounts::filament/resources/invoice.table.groups.sales-person'))
                    ->collapsible(),
                Tables\Grouping\Group::make('currency.name')
                    ->label(__('accounts::filament/resources/invoice.table.groups.currency'))
                    ->collapsible(),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('accounts::filament/resources/invoice.table.groups.created-at'))
                    ->date()
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('accounts::filament/resources/invoice.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->filtersFormColumns(2)
            ->filters([
                QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        TextConstraint::make('name')
                            ->label(__('accounts::filament/resources/invoice.table.filters.number')),
                        TextConstraint::make('invoice_origin')
                            ->label(__('accounts::filament/resources/invoice.table.filters.invoice-origin')),
                        TextConstraint::make('reference')
                            ->label(__('accounts::filament/resources/invoice.table.filters.reference')),
                        TextConstraint::make('invoice_partner_display_name')
                            ->label(__('accounts::filament/resources/invoice.table.filters.invoice-partner-display-name')),
                        DateConstraint::make('invoice_date')
                            ->label(__('accounts::filament/resources/invoice.table.filters.invoice-date')),
                        DateConstraint::make('invoice_due_date')
                            ->label(__('accounts::filament/resources/invoice.table.filters.invoice-due-date')),
                        DateConstraint::make('created_at')
                            ->label(__('accounts::filament/resources/invoice.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('accounts::filament/resources/invoice.table.filters.updated-at')),
                    ]),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/invoice.table.actions.delete.notification.title'))
                                ->body(__('accounts::filament/resources/invoice.table.actions.delete.notification.body'))
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/invoice.table.bulk-actions.delete.notification.title'))
                                ->body(__('accounts::filament/resources/invoice.table.bulk-actions.delete.notification.body'))
                        ),
                ]),
            ]);
    }
}
