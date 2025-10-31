<?php

namespace Webkul\Invoice\Models;

use Webkul\Account\Models\Move as BaseMove;

class Refund extends BaseMove
{
    public function getModelTitle(): string
    {
        return __('invoices::models/refund.title');
    }
}
