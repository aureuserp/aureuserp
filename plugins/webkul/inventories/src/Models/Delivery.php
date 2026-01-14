<?php

namespace Webkul\Inventory\Models;

class Delivery extends Operation
{
    public function getModelTitle(): string
    {
        return __('inventories::models/delivery.title');
    }
}
