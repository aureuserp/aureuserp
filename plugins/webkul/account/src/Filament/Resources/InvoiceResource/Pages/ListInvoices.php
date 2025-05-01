<?php

namespace Webkul\Account\Filament\Resources\InvoiceResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Filament\Resources\InvoiceResource;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListInvoices extends ListRecords
{
    use HasTableViews;

    protected static string $resource = InvoiceResource::class;

    public function getPresetTableViews(): array
    {
        return [
            'invoice' => PresetView::make(__('accounts::filament/resources/invoice/pages/list-invoice.tabs.invoices'))
                ->favorite()
                ->default()
                ->icon('heroicon-s-receipt-percent')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('move_type', MoveType::OUT_INVOICE)),
            'draft' => PresetView::make(__('accounts::filament/resources/invoice/pages/list-invoice.tabs.draft'))
                ->favorite()
                ->icon('heroicon-s-stop')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', MoveState::DRAFT)),
            'posted' => PresetView::make(__('accounts::filament/resources/invoice/pages/list-invoice.tabs.posted'))
                ->favorite()
                ->icon('heroicon-s-play')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', MoveState::POSTED)),
            'cancelled' => PresetView::make(__('accounts::filament/resources/invoice/pages/list-invoice.tabs.cancelled'))
                ->favorite()
                ->icon('heroicon-s-x-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', MoveState::CANCEL)),
            'not_secured' => PresetView::make(__('accounts::filament/resources/invoice/pages/list-invoice.tabs.not-secured'))
                ->favorite()
                ->icon('heroicon-s-shield-exclamation')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('inalterable_hash')),
            'in_refund' => PresetView::make(__('accounts::filament/resources/invoice/pages/list-invoice.tabs.refund'))
                ->icon('heroicon-s-receipt-refund')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('move_type', MoveType::IN_REFUND)),
            'to_check' => PresetView::make(__('accounts::filament/resources/invoice/pages/list-invoice.tabs.to-check'))
                ->icon('heroicon-s-check-badge')
                ->modifyQueryUsing(function (Builder $query) {
                    $query->whereNot('state', MoveState::DRAFT)
                        ->where('checked', false);
                }),
            'to_pay' => PresetView::make(__('accounts::filament/resources/invoice/pages/list-invoice.tabs.to-pay'))
                ->icon('heroicon-s-banknotes')
                ->modifyQueryUsing(function (Builder $query) {
                    $query->whereNot('state', MoveState::POSTED)
                        ->whereIn('payment_state', [
                            PaymentState::NOT_PAID,
                            PaymentState::PARTIAL,
                        ]);
                }),
            'in_payment' => PresetView::make(__('accounts::filament/resources/invoice/pages/list-invoice.tabs.in-payment'))
                ->icon('heroicon-s-banknotes')
                ->modifyQueryUsing(function (Builder $query) {
                    $query->whereNot('state', MoveState::POSTED)
                        ->where('payment_state', PaymentState::IN_PAYMENT);
                }),
            'overdue' => PresetView::make(__('accounts::filament/resources/invoice/pages/list-invoice.tabs.overdue'))
                ->icon('heroicon-s-banknotes')
                ->modifyQueryUsing(function (Builder $query) {
                    $query->whereNot('state', MoveState::POSTED)
                        ->where('payment_state', PaymentState::NOT_PAID)
                        ->where('invoice_date_due', '<', now());
                }),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
