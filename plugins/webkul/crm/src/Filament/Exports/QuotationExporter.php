<?php

namespace Webkul\Crm\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Crm\Filament\Concerns\UsesAbsoluteDownloadUrls;
use Webkul\Sale\Models\Order;

class QuotationExporter extends Exporter
{
    use UsesAbsoluteDownloadUrls;

    protected static ?string $model = Order::class;

    public static function getColumns(): array
    {
        $enumLabel = fn (mixed $state): string => $state instanceof HasLabel ? (string) $state->getLabel() : (string) ($state->value ?? $state);

        return [
            ExportColumn::make('name')->label(__('crm::filament/transfers.columns.number')),
            ExportColumn::make('partner.name')->label(__('crm::filament/transfers.columns.customer')),
            ExportColumn::make('date_order')->label(__('crm::filament/transfers.columns.order-date')),
            ExportColumn::make('validity_date')->label(__('crm::filament/transfers.columns.expiration'))->enabledByDefault(false),
            ExportColumn::make('commitment_date')->label(__('crm::filament/transfers.columns.delivery-date'))->enabledByDefault(false),
            ExportColumn::make('user.name')->label(__('crm::filament/transfers.columns.salesperson')),
            ExportColumn::make('team.name')->label(__('crm::filament/transfers.columns.team')),
            ExportColumn::make('client_order_ref')->label(__('crm::filament/transfers.columns.customer-reference'))->enabledByDefault(false),
            ExportColumn::make('origin')->label(__('crm::filament/transfers.columns.source-document'))->enabledByDefault(false),
            ExportColumn::make('amount_untaxed')->label(__('crm::filament/transfers.columns.untaxed-amount')),
            ExportColumn::make('amount_tax')->label(__('crm::filament/transfers.columns.taxes')),
            ExportColumn::make('amount_total')->label(__('crm::filament/transfers.columns.total')),
            ExportColumn::make('currency.name')->label(__('crm::filament/transfers.columns.currency')),
            ExportColumn::make('state')->label(__('crm::filament/transfers.columns.status'))->formatStateUsing($enumLabel),
            ExportColumn::make('invoice_status')->label(__('crm::filament/transfers.columns.invoice-status'))->formatStateUsing($enumLabel),
        ];
    }

    public static function modifyQuery(Builder $query): Builder
    {
        return $query->with(['partner', 'user', 'team', 'currency']);
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = __('crm::filament/transfers.notifications.export.completed', ['count' => number_format($export->successful_rows)]);

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.__('crm::filament/transfers.notifications.export.failed', ['count' => number_format($failedRowsCount)]);
        }

        return $body;
    }

    public function getFileName(Export $export): string
    {
        return 'quotations-'.$export->getKey().'-'.now()->format('Y-m-d');
    }
}
