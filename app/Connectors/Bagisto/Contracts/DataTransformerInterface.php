<?php

namespace App\Connectors\Bagisto\Contracts;

interface DataTransformerInterface
{
    /**
     * Transform Aureus ERP data to Bagisto format.
     *
     * @param mixed $data
     * @return array
     */
    public function toExternal($data): array;

    /**
     * Transform Bagisto data to Aureus ERP format.
     *
     * @param array $data
     * @return array
     */
    public function toInternal(array $data): array;

    /**
     * Validate data before transformation.
     *
     * @param mixed $data
     * @return bool
     */
    public function validate($data): bool;
}
