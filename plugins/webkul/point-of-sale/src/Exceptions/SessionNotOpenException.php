<?php

namespace Webkul\PointOfSale\Exceptions;

use Exception;

class SessionNotOpenException extends Exception
{
    public function getStatusCode(): int
    {
        return 422;
    }
}
