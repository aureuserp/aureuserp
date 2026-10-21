<?php

namespace Webkul\PointOfSale\Exceptions;

use Exception;

class InsufficientPaymentException extends Exception
{
    public function getStatusCode(): int
    {
        return 422;
    }
}
