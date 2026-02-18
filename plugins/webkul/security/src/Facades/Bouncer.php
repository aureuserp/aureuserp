<?php

namespace Webkul\Security\Facades;

use Illuminate\Support\Facades\Facade;

class Bouncer extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'bouncer';
    }
}
