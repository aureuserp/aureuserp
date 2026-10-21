<?php

namespace Webkul\PointOfSale\Facades;

use Illuminate\Support\Facades\Facade;

class PointOfSale extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'point-of-sale';
    }
}
