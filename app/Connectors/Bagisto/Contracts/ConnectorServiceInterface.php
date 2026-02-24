<?php

namespace App\Connectors\Bagisto\Contracts;

interface ConnectorServiceInterface
{
    /**
     * Sync data from Aureus ERP to Bagisto.
     *
     * @param mixed $data
     * @return array
     */
    public function syncToBagisto($data): array;

    /**
     * Sync data from Bagisto to Aureus ERP.
     *
     * @param string|int $externalId
     * @return mixed
     */
    public function syncFromBagisto($externalId);

    /**
     * Fetch data from Bagisto by ID.
     *
     * @param string|int $externalId
     * @return array
     */
    public function fetch($externalId): array;

    /**
     * Fetch all data from Bagisto with pagination.
     *
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function fetchAll(int $page = 1, int $perPage = 50): array;

    /**
     * Delete data from Bagisto.
     *
     * @param string|int $externalId
     * @return bool
     */
    public function delete($externalId): bool;
}
