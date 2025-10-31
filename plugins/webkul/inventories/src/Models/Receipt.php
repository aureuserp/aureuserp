<?php

namespace Webkul\Inventory\Models;

class Receipt extends Operation
{
    public function getModelTitle(): string
    {
        return __('inventories::models/receipt.title');
    }
}
