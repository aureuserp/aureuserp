<?php

namespace Webkul\Purchase\Listeners;

use Webkul\Account\Events\MoveCancelled;
use Webkul\Account\Events\MoveConfirmed;
use Webkul\Account\Events\MoveDrafted;
use Webkul\Account\Events\MoveReversed;
use Webkul\PluginManager\Package;
use Webkul\Purchase\Facades\PurchaseOrder as PurchaseOrderFacade;
use Webkul\Purchase\Models\Order;

class ComputePurchaseOrderFromMoveListener
{
    public function handle(MoveConfirmed|MoveCancelled|MoveDrafted|MoveReversed $event): void
    {
        if (! Package::isPluginInstalled('purchases')) {
            return;
        }

        Order::query()
            ->whereHas('accountMoves', fn ($query) => $query->whereKey($event->move->id))
            ->get()
            ->each(fn (Order $order) => PurchaseOrderFacade::computePurchaseOrder($order));
    }
}
