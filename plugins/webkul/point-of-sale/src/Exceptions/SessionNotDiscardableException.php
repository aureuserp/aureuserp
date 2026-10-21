<?php

namespace Webkul\PointOfSale\Exceptions;

use Exception;

class SessionNotDiscardableException extends Exception
{
    public function getStatusCode(): int
    {
        return 422;
    }
}
