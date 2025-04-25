<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Actions;

use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Illuminate\Support\Arr;
use Webkul\Sale\Enums\AdvancedPayment;
use Webkul\Sale\Enums\InvoiceStatus;
use Webkul\Sale\Facades\SaleOrder as SalesFacade;
use Webkul\Sale\Models\Order;

class CreateInvoiceAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'orders.sales.create-invoice';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->color(function (Order $record): string {
                if ($record->qty_to_invoice == 0) {
                    return 'gray';
                }

                return 'primary';
            })
            ->label(__('sales::filament/clusters/orders/resources/quotation/actions/create-invoice.title'))
            ->form([
                Forms\Components\Radio::make('advance_payment_method')
                    ->inline(false)
                    ->label(__('sales::filament/clusters/orders/resources/quotation/actions/create-invoice.form.fields.create-invoice'))
                    ->options(function () {
                        $options = AdvancedPayment::options();

                        return Arr::only($options, [
                            AdvancedPayment::DELIVERED->value,
                        ]);
                    })
                    ->default(AdvancedPayment::DELIVERED->value)
                    ->live(),
                Forms\Components\Group::make()
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('amount')
                            ->visible(fn (Get $get) => $get('advance_payment_method') == AdvancedPayment::PERCENTAGE->value)
                            ->rules('required', 'numeric')
                            ->default(0.00)
                            ->suffix('%'),
                        Forms\Components\TextInput::make('amount')
                            ->visible(fn (Get $get) => $get('advance_payment_method') == AdvancedPayment::FIXED->value)
                            ->rules('required', 'numeric')
                            ->default(0.00)
                            ->prefix(fn ($record) => $record->currency->symbol),
                    ]),
            ])
            ->hidden(fn ($record) => $record->invoice_status != InvoiceStatus::TO_INVOICE)
            ->action(function (Order $record, $data) {
                if ($record->qty_to_invoice == 0) {
                    Notification::make()
                        ->title(__('sales::filament/clusters/orders/resources/quotation/actions/create-invoice.notification.no-invoiceable-lines.title'))
                        ->body(__('sales::filament/clusters/orders/resources/quotation/actions/create-invoice.notification.no-invoiceable-lines.body'))
                        ->warning()
                        ->send();

                    return;
                }

                SalesFacade::createInvoice($record, $data);

                Notification::make()
                    ->title(__('sales::filament/clusters/orders/resources/quotation/actions/create-invoice.notification.invoice-created.title'))
                    ->body(__('sales::filament/clusters/orders/resources/quotation/actions/create-invoice.notification.invoice-created.body'))
                    ->success()
                    ->send();
            });
    }
}
