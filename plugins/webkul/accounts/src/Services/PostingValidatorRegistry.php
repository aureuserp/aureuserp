<?php

namespace Webkul\Account\Services;

use Webkul\Account\Contracts\PostingValidator;
use Webkul\Account\Support\Posting\PostingContext;

class PostingValidatorRegistry
{
    protected array $validators = [];

    public function register(PostingValidator|string $validator): void
    {
        $this->validators[] = $validator;
    }

    public function validate(PostingContext $context): void
    {
        foreach ($this->validators as $validator) {
            (is_string($validator) ? app($validator) : $validator)->validate($context);
        }
    }
}
