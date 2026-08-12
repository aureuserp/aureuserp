<?php

namespace Webkul\Purchase\Listeners;

use Webkul\Inventory\Events\OperationBackOrdered;
use Webkul\Inventory\Events\OperationDone;
use Webkul\PluginManager\Package;
use Webkul\Purchase\Facades\PurchaseOrder as PurchaseOrderFacade;

class ComputePurchaseOrderListener
{
    public function handle(OperationDone|OperationBackOrdered $event): void
    {
        if (! Package::isPluginInstalled('purchases')) {
            return;
        }

        foreach ($event->operation->purchaseOrders as $purchaseOrder) {
            PurchaseOrderFacade::computePurchaseOrder($purchaseOrder);
        }
    }
}
