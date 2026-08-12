<?php

namespace Webkul\Manufacturing\Facades;

use Illuminate\Support\Facades\Facade;

class Manufacturing extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'manufacturing';
    }
}
