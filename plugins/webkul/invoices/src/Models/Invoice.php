<?php

namespace Webkul\Invoice\Models;

use Webkul\Account\Models\Move as BaseMove;

class Invoice extends BaseMove
{
    public function getModelTitle(): string
    {
        return __('invoices::models/invoice.title');
    }
}
