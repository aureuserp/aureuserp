<?php

namespace Webkul\Invoice\Models;

use Webkul\Account\Models\Move as BaseMove;
use Webkul\Account\Models\MoveLine;

class Invoice extends BaseMove
{
    public function getModelTitle(): string
    {
        return __('invoices::models/invoice.title');
    }

    public function paymentTermLine()
    {
        return $this->hasOne(MoveLine::class, 'move_id')
            ->where('display_type', 'payment_term');
    }
}
