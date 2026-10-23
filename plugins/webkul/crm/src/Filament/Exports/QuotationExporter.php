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
        $label = fn (string $key): string => __('crm::filament/transfers.columns.'.$key);

        $enumLabel = fn (mixed $state): string => $state instanceof HasLabel ? (string) $state->getLabel() : (string) ($state->value ?? $state);

        return [
            ExportColumn::make('name')->label($label('number')),
            ExportColumn::make('partner.name')->label($label('customer')),
            ExportColumn::make('date_order')->label($label('order-date')),
            ExportColumn::make('validity_date')->label($label('expiration'))->enabledByDefault(false),
            ExportColumn::make('commitment_date')->label($label('delivery-date'))->enabledByDefault(false),
            ExportColumn::make('user.name')->label($label('salesperson')),
            ExportColumn::make('team.name')->label($label('team')),
            ExportColumn::make('client_order_ref')->label($label('customer-reference'))->enabledByDefault(false),
            ExportColumn::make('origin')->label($label('source-document'))->enabledByDefault(false),
            ExportColumn::make('amount_untaxed')->label($label('untaxed-amount')),
            ExportColumn::make('amount_tax')->label($label('taxes')),
            ExportColumn::make('amount_total')->label($label('total')),
            ExportColumn::make('currency.name')->label($label('currency')),
            ExportColumn::make('state')->label($label('status'))->formatStateUsing($enumLabel),
            ExportColumn::make('invoice_status')->label($label('invoice-status'))->formatStateUsing($enumLabel),
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
