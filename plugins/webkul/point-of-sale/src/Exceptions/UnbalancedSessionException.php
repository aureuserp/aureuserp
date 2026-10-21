<?php

namespace Webkul\PointOfSale\Exceptions;

use Exception;

class UnbalancedSessionException extends Exception
{
    public function __construct(
        public readonly float $delta,
        public readonly ?int $sessionId = null,
        string $message = '',
    ) {
        parent::__construct($message ?: __('point-of-sale::system.session-closer.unbalanced', ['delta' => $delta]));
    }

    public function getStatusCode(): int
    {
        return 422;
    }
}
