<?php

namespace Webkul\Sale\Listeners;

use Webkul\Inventory\Events\OperationDone;
use Webkul\PluginManager\Package;
use Webkul\Sale\Facades\SaleOrder as SaleOrderFacade;

class ComputeSaleOrderListener
{
    public function handle(OperationDone $event): void
    {
        if (! Package::isPluginInstalled('sales')) {
            return;
        }

        if ($event->operation->saleOrder) {
            SaleOrderFacade::computeSaleOrder($event->operation->saleOrder);
        }
    }
}
