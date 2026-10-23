<?php

namespace Webkul\Account\Exceptions;

use Webkul\Account\Enums\PostingError;

class PostingRejectedException extends PostingException
{
    public function __construct(string $reason)
    {
        parent::__construct(
            PostingError::REJECTED_BY_VALIDATOR,
            ['reason' => $reason],
            __('accounts::system.posting.rejected-by-validator', ['reason' => $reason]),
        );
    }
}
