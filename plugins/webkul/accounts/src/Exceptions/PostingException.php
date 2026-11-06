<?php

namespace Webkul\Account\Exceptions;

use Exception;
use Throwable;
use Webkul\Account\Enums\PostingError;

class PostingException extends Exception
{
    public function __construct(
        public readonly PostingError $error,
        public readonly array $details = [],
        ?string $message = null,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message ?? __('accounts::system.posting.'.str_replace('_', '-', $error->value), $details), 0, $previous);
    }
}
