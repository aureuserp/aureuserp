<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\OrderResource\Actions;

use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Webkul\Purchase\Enums\OrderState;
use Webkul\Purchase\Models\Order;

class PrintPOAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'purchases.orders.print-po';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('purchases::filament/admin/clusters/orders/resources/order/actions/print-po.label'))
            ->action(function (Order $record) {
                $pdf = PDF::loadView('purchases::filament.admin.clusters.orders.orders.actions.print-purchase-order', [
                    'records'  => [$record],
                ]);

                $pdf->setPaper('a4', 'portrait');

                return response()->streamDownload(function () use ($pdf) {
                    echo $pdf->output();
                }, 'Purchase Order-'.str_replace('/', '_', $record->name).'.pdf');
            })
            ->color('primary')
            ->visible(fn (Order $record) => $record->state == OrderState::PURCHASE);
    }
}
