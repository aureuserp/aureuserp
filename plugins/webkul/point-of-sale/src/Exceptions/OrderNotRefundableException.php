<?php

namespace Webkul\PointOfSale\Exceptions;

use Exception;

class OrderNotRefundableException extends Exception
{
    public function getStatusCode(): int
    {
        return 422;
    }
}
