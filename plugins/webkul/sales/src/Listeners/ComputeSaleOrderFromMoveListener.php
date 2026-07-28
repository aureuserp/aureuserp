<?php

namespace Webkul\Sale\Listeners;

use Illuminate\Support\Facades\DB;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Events\MoveCancelled;
use Webkul\Account\Events\MoveConfirmed;
use Webkul\Account\Events\MoveDrafted;
use Webkul\Account\Events\MoveReversed;
use Webkul\Account\Models\Move;
use Webkul\PluginManager\Package;
use Webkul\Sale\Facades\SaleOrder as SaleOrderFacade;
use Webkul\Sale\Models\Order;

class ComputeSaleOrderFromMoveListener
{
    public function handle(MoveConfirmed|MoveCancelled|MoveDrafted|MoveReversed $event): void
    {
        if (! Package::isPluginInstalled('sales')) {
            return;
        }

        if ($event instanceof MoveReversed) {
            $this->copyInvoiceLinksToReversal($event->move);
        }

        Order::query()
            ->whereHas('accountMoves', fn ($query) => $query->whereKey($event->move->id))
            ->get()
            ->each(fn (Order $order) => SaleOrderFacade::computeSaleOrder($order));
    }

    protected function copyInvoiceLinksToReversal(Move $origin): void
    {
        $reversal = Move::query()
            ->where('reversed_entry_id', $origin->id)
            ->orderByDesc('id')
            ->first();

        if (! $reversal) {
            return;
        }

        $originLines = $origin->lines()->where('display_type', DisplayType::PRODUCT)->orderBy('id')->get();
        $reversalLines = $reversal->lines()->where('display_type', DisplayType::PRODUCT)->orderBy('id')->get();

        foreach ($originLines as $index => $originLine) {
            $reversalLine = $reversalLines[$index] ?? null;

            if (! $reversalLine) {
                continue;
            }

            $orderLineIds = DB::table('sales_order_line_invoices')
                ->where('invoice_line_id', $originLine->id)
                ->pluck('order_line_id');

            foreach ($orderLineIds as $orderLineId) {
                DB::table('sales_order_line_invoices')->updateOrInsert([
                    'order_line_id'   => $orderLineId,
                    'invoice_line_id' => $reversalLine->id,
                ]);
            }
        }
    }
}
