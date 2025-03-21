<?php

namespace Webkul\Sale\Facades;

use Illuminate\Support\Facades\Facade;

class SaleOrder extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'sale';
    }
}
