<?php

namespace Webkul\PointOfSale\Exceptions;

use Exception;

class SessionHasDraftOrdersException extends Exception
{
    public function getStatusCode(): int
    {
        return 422;
    }
}
