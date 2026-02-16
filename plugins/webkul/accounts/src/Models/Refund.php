<?php

namespace Webkul\Account\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Account\Database\Factories\RefundFactory;

class Refund extends Move
{
    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return RefundFactory::new();
    }
}
