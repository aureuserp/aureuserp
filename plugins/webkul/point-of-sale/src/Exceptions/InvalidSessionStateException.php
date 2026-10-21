<?php

namespace Webkul\PointOfSale\Exceptions;

use Exception;

class InvalidSessionStateException extends Exception
{
    public function getStatusCode(): int
    {
        return 422;
    }
}
