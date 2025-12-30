<?php

namespace Webkul\Purchase\Models;

class PurchaseOrder extends Order
{
    public function getModelTitle(): string
    {
        return __('purchases::models/purchase-order.title');
    }
}
