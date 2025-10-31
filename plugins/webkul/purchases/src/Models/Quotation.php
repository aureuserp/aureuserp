<?php

namespace Webkul\Purchase\Models;

class Quotation extends Order
{
    public function getModelTitle(): string
    {
        return __('purchases::models/quotation.title');
    }
}
