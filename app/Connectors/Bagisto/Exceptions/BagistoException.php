<?php

namespace App\Connectors\Bagisto\Exceptions;

use Exception;

class BagistoException extends Exception
{
    protected array $context = [];

    public function __construct(string $message = "", int $code = 0, array $context = [], ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
    }

    /**
     * Get exception context data.
     *
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Set exception context data.
     *
     * @param array $context
     * @return self
     */
    public function setContext(array $context): self
    {
        $this->context = $context;
        return $this;
    }
}
