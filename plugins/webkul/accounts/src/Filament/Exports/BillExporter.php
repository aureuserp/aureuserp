<?php

namespace Webkul\Account\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Webkul\Account\Models\Move;

class BillExporter extends Exporter
{
    protected static ?string $model = Move::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')
                ->label(__('accounts::filament/exports/bill.columns.number')),
            ExportColumn::make('state')
                ->label(__('accounts::filament/exports/bill.columns.state'))
                ->formatStateUsing(fn ($state) => is_object($state) ? ($state->getLabel() ?? $state->value ?? '') : (string) $state),
            ExportColumn::make('invoice_partner_display_name')
                ->label(__('accounts::filament/exports/bill.columns.customer')),
            ExportColumn::make('invoice_date')
                ->label(__('accounts::filament/exports/bill.columns.bill-date')),
            ExportColumn::make('invoice_date_due')
                ->label(__('accounts::filament/exports/bill.columns.due-date')),
            ExportColumn::make('amount_untaxed_in_currency_signed')
                ->label(__('accounts::filament/exports/bill.columns.tax-excluded')),
            ExportColumn::make('amount_tax_signed')
                ->label(__('accounts::filament/exports/bill.columns.tax')),
            ExportColumn::make('amount_total_in_currency_signed')
                ->label(__('accounts::filament/exports/bill.columns.total')),
            ExportColumn::make('amount_residual_signed')
                ->label(__('accounts::filament/exports/bill.columns.amount-due')),
            ExportColumn::make('payment_state')
                ->label(__('accounts::filament/exports/bill.columns.payment-state'))
                ->formatStateUsing(fn ($state) => is_object($state) ? ($state->getLabel() ?? $state->value ?? '') : (string) $state),
            ExportColumn::make('checked')
                ->label(__('accounts::filament/exports/bill.columns.checked'))
                ->formatStateUsing(fn ($state) => $state ? __('accounts::filament/exports/bill.values.yes') : __('accounts::filament/exports/bill.values.no'))
                ->enabledByDefault(false),
            ExportColumn::make('date')
                ->label(__('accounts::filament/exports/bill.columns.accounting-date'))
                ->enabledByDefault(false),
            ExportColumn::make('invoice_origin')
                ->label(__('accounts::filament/exports/bill.columns.source-document'))
                ->enabledByDefault(false),
            ExportColumn::make('reference')
                ->label(__('accounts::filament/exports/bill.columns.reference'))
                ->enabledByDefault(false),
            ExportColumn::make('invoiceUser.name')
                ->label(__('accounts::filament/exports/bill.columns.sales-person'))
                ->enabledByDefault(false),
            ExportColumn::make('currency.name')
                ->label(__('accounts::filament/exports/bill.columns.bill-currency'))
                ->enabledByDefault(false),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = __('accounts::filament/exports/bill.notification.completed', [
            'count' => number_format($export->successful_rows),
        ]);

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.__('accounts::filament/exports/bill.notification.failed', [
                'count' => number_format($failedRowsCount),
            ]);
        }

        return $body;
    }
}
