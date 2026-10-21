<?php

namespace Webkul\PointOfSale\Exceptions;

use Exception;

class CustomerRequiredException extends Exception
{
    public function getStatusCode(): int
    {
        return 422;
    }
}
