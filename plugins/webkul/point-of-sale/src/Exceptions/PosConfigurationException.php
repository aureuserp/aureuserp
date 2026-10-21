<?php

namespace Webkul\PointOfSale\Exceptions;

use Exception;

class PosConfigurationException extends Exception
{
    public function getStatusCode(): int
    {
        return 422;
    }
}
