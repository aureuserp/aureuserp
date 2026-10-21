<?php

namespace Webkul\PointOfSale\Exceptions;

use Exception;

class RefundExceedsSoldQuantityException extends Exception
{
    public function getStatusCode(): int
    {
        return 422;
    }
}
