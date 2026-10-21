<?php

namespace Webkul\PointOfSale\Exceptions;

use Exception;

class OrderAlreadyPaidException extends Exception
{
    public function getStatusCode(): int
    {
        return 422;
    }
}
