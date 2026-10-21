<?php

namespace Webkul\PointOfSale\Exceptions;

use Exception;

class SessionAlreadyOpenException extends Exception
{
    public function getStatusCode(): int
    {
        return 422;
    }
}
