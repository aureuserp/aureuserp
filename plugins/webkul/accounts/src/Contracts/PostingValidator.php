<?php

namespace Webkul\Account\Contracts;

use Webkul\Account\Support\Posting\PostingContext;

interface PostingValidator
{
    public function validate(PostingContext $context): void;
}
