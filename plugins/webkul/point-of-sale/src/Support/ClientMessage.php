<?php

namespace Webkul\PointOfSale\Support;

use Error;
use Illuminate\Database\QueryException;
use PDOException;
use Throwable;

class ClientMessage
{
    public static function for(Throwable $exception): string
    {
        if ($exception instanceof QueryException || $exception instanceof PDOException || $exception instanceof Error) {
            report($exception);

            return __('point-of-sale::system.unexpected-error');
        }

        return $exception->getMessage();
    }
}
