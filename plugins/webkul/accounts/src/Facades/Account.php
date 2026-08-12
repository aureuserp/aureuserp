<?php

namespace Webkul\Account\Facades;

use Illuminate\Support\Facades\Facade;
use Webkul\Account\Models\Move;

/**
 * @method static Move computeAccountMove(Move $record)
 */
class Account extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'account';
    }
}
