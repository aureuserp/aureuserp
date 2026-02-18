<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\OrderResource\Actions;

use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Livewire\Component;
use Webkul\Purchase\Enums\OrderState;
use Webkul\Purchase\Facades\PurchaseOrder;
use Webkul\Purchase\Models\Order;

class PrintRFQAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'purchases.orders.print-rfq';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('purchases::filament/admin/clusters/orders/resources/order/actions/print-rfq.label'))
            ->action(function (Order $record, Component $livewire) {
                $pdf = PDF::loadView('purchases::filament.admin.clusters.orders.orders.actions.print-quotation', [
                    'records'  => [$record],
                ]);

                $pdf->setPaper('a4', 'portrait');

                $record->update([
                    'state' => OrderState::SENT,
                ]);

                $record = PurchaseOrder::computePurchaseOrder($record);

                $livewire->updateForm();

                return response()->streamDownload(function () use ($pdf) {
                    echo $pdf->output();
                }, 'Request for Quotation-'.str_replace('/', '_', $record->name).'.pdf');
            })
            ->color(fn (): string => $this->getRecord()->state === OrderState::DRAFT ? 'primary' : 'gray')
            ->visible(fn () => in_array($this->getRecord()->state, [
                OrderState::DRAFT,
                OrderState::SENT,
            ]));
    }
}
