<?php

namespace App\Connectors\Bagisto\Contracts;

interface HttpClientInterface
{
    /**
     * Send a GET request to the Bagisto API.
     *
     * @param string $endpoint
     * @param array $params
     * @return array
     */
    public function get(string $endpoint, array $params = []): array;

    /**
     * Send a POST request to the Bagisto API.
     *
     * @param string $endpoint
     * @param array $data
     * @return array
     */
    public function post(string $endpoint, array $data = []): array;

    /**
     * Send a PUT request to the Bagisto API.
     *
     * @param string $endpoint
     * @param array $data
     * @return array
     */
    public function put(string $endpoint, array $data = []): array;

    /**
     * Send a DELETE request to the Bagisto API.
     *
     * @param string $endpoint
     * @return array
     */
    public function delete(string $endpoint): array;

    /**
     * Check if the connection to Bagisto API is valid.
     *
     * @return bool
     */
    public function isConnected(): bool;

    /**
     * Set authentication token for API requests.
     *
     * @param string $token
     * @return self
     */
    public function setToken(string $token): self;
}
