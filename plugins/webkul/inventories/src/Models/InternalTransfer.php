<?php

namespace Webkul\Inventory\Models;

class InternalTransfer extends Operation
{
    public function getModelTitle(): string
    {
        return __('inventories::models/internal-transfer.title');
    }
}
