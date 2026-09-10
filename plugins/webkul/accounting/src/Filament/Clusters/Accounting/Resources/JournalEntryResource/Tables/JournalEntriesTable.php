<?php

namespace Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalEntryResource\Tables;

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
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Account\Enums\MoveType;
use Webkul\Accounting\Filament\Clusters\Customers\Resources\InvoiceResource;
use Webkul\Accounting\Filament\Clusters\Vendors\Resources\BillResource;
use Webkul\Accounting\Filament\Exports\JournalEntryExporter;

class JournalEntriesTable
{
    public static function configure(Table $table, string $resource, array $customColumns = [], array $customFilters = []): Table
    {
        return $table
            ->reorderableColumns()
            ->columns(array_merge([
                TextColumn::make('invoice_date')
                    ->date()
                    ->placeholder('-')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.columns.invoice-date'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('date')
                    ->date()
                    ->placeholder('-')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.columns.date'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->placeholder('-')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.columns.number'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('invoice_partner_display_name')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.columns.partner'))
                    ->placeholder('-')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('reference')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.columns.reference'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('journal.name')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.columns.journal'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('company.name')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.columns.company'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('amount_total_in_currency_signed')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.columns.total'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->summarize(Sum::make()->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.total')))
                    ->money(fn ($record) => $record->company->currency?->name),
                TextColumn::make('state')
                    ->placeholder('-')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.columns.state'))
                    ->sortable(),
                IconColumn::make('checked')
                    ->boolean()
                    ->placeholder('-')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.columns.checked'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ], $customColumns))
            ->groups([
                Tables\Grouping\Group::make('invoice_partner_display_name')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.groups.partner'))
                    ->collapsible(),
                Tables\Grouping\Group::make('journal.name')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.groups.journal'))
                    ->collapsible(),
                Tables\Grouping\Group::make('state')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.groups.state'))
                    ->collapsible(),
                Tables\Grouping\Group::make('paymentMethodLine.name')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.groups.payment-method'))
                    ->collapsible(),
                Tables\Grouping\Group::make('date')
                    ->date()
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.groups.date'))
                    ->collapsible(),
                Tables\Grouping\Group::make('invoice_date')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.groups.invoice-date'))
                    ->collapsible(),
                Tables\Grouping\Group::make('company.name')
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.groups.company'))
                    ->collapsible(),
            ])
            ->filtersFormColumns(2)
            ->filters(array_merge([
                QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        TextConstraint::make('name')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.filters.number')),
                        TextConstraint::make('invoice_origin')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.filters.invoice-origin')),
                        TextConstraint::make('reference')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.filters.reference')),
                        TextConstraint::make('invoice_partner_display_name')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.filters.invoice-partner-display-name')),
                        DateConstraint::make('invoice_date')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.filters.invoice-date')),
                        DateConstraint::make('invoice_date_due')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.filters.invoice-due-date')),
                        DateConstraint::make('created_at')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.filters.updated-at')),
                    ]),
            ], $customFilters))
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->url(function (Model $record) use ($resource): string {
                            if (in_array($record->move_type, [MoveType::OUT_INVOICE, MoveType::OUT_REFUND])) {
                                return InvoiceResource::getUrl('view', ['record' => $record]);
                            }

                            if (in_array($record->move_type, [MoveType::IN_INVOICE, MoveType::IN_REFUND])) {
                                return BillResource::getUrl('view', ['record' => $record]);
                            }

                            return $resource::getUrl('view', ['record' => $record]);
                        }),
                    EditAction::make()
                        ->url(function (Model $record) use ($resource): string {
                            if (in_array($record->move_type, [MoveType::OUT_INVOICE, MoveType::OUT_REFUND])) {
                                return InvoiceResource::getUrl('edit', ['record' => $record]);
                            }

                            if (in_array($record->move_type, [MoveType::IN_INVOICE, MoveType::IN_REFUND])) {
                                return BillResource::getUrl('edit', ['record' => $record]);
                            }

                            return $resource::getUrl('edit', ['record' => $record]);
                        }),
                    DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounting::filament/clusters/accounting/resources/journal-entry.table.actions.delete.notification.title'))
                                ->body(__('accounting::filament/clusters/accounting/resources/journal-entry.table.actions.delete.notification.body'))
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounting::filament/clusters/accounting/resources/journal-entry.table.bulk-actions.delete.notification.title'))
                                ->body(__('accounting::filament/clusters/accounting/resources/journal-entry.table.bulk-actions.delete.notification.body'))
                        ),
                ]),
                ExportAction::make()
                    ->label(__('accounting::filament/clusters/accounting/resources/journal-entry.table.toolbar-actions.export.label'))
                    ->icon('heroicon-o-arrow-up-tray')
                    ->exporter(JournalEntryExporter::class),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $query->with('currency');
            });
    }
}
