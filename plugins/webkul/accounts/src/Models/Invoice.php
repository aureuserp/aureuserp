<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Account\Database\Factories\InvoiceFactory;

class Invoice extends Move
{
    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return InvoiceFactory::new();
    }
}
