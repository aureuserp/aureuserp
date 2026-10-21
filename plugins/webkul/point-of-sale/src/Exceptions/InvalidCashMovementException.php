<?php

namespace Webkul\PointOfSale\Exceptions;

use Exception;

class InvalidCashMovementException extends Exception
{
    public function getStatusCode(): int
    {
        return 422;
    }
}
