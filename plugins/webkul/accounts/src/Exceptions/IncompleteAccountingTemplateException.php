<?php

namespace Webkul\Account\Exceptions;

use Exception;

class IncompleteAccountingTemplateException extends Exception
{
    public function __construct(public readonly array $missing)
    {
        parent::__construct('The accounting template company is incomplete.');
    }
}
