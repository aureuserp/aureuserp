<?php

namespace Webkul\Invoice\Models;

use Webkul\Account\Models\Move as BaseMove;

class Bill extends BaseMove
{
    public function getModelTitle(): string
    {
        return __('invoices::models/bill.title');
    }
}
