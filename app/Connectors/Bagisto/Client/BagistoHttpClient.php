<?php

namespace App\Connectors\Bagisto\Client;

use App\Connectors\Bagisto\Contracts\HttpClientInterface;
use App\Connectors\Bagisto\Exceptions\BagistoApiException;
use App\Connectors\Bagisto\Exceptions\BagistoAuthenticationException;
use App\Connectors\Bagisto\Exceptions\BagistoConnectionException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BagistoHttpClient implements HttpClientInterface
{
    protected string $baseUrl;
    protected string $apiToken;
    protected int $timeout;
    protected int $retryTimes;
    protected int $retryDelay;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('bagisto-connector.api_url'), '/');
        $this->apiToken = config('bagisto-connector.api_token');
        $this->timeout = config('bagisto-connector.timeout', 30);
        $this->retryTimes = config('bagisto-connector.retry_times', 3);
        $this->retryDelay = config('bagisto-connector.retry_delay', 1000);
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $endpoint, array $params = []): array
    {
        return $this->request('GET', $endpoint, [
            'query' => $params,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function post(string $endpoint, array $data = []): array
    {
        return $this->request('POST', $endpoint, [
            'json' => $data,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function put(string $endpoint, array $data = []): array
    {
        return $this->request('PUT', $endpoint, [
            'json' => $data,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(string $endpoint): array
    {
        return $this->request('DELETE', $endpoint);
    }

    /**
     * {@inheritDoc}
     */
    public function isConnected(): bool
    {
        try {
            $response = $this->get('/api/health-check');
            return true;
        } catch (\Exception $e) {
            Log::error('Bagisto connection check failed', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function setToken(string $token): self
    {
        $this->apiToken = $token;
        return $this;
    }

    /**
     * Make HTTP request to Bagisto API.
     *
     * @param string $method
     * @param string $endpoint
     * @param array $options
     * @return array
     * @throws BagistoApiException
     * @throws BagistoAuthenticationException
     * @throws BagistoConnectionException
     */
    protected function request(string $method, string $endpoint, array $options = []): array
    {
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');

        try {
            $response = Http::withToken($this->apiToken)
                ->timeout($this->timeout)
                ->retry($this->retryTimes, $this->retryDelay, function ($exception) {
                    return $exception instanceof ConnectionException;
                })
                ->$method($url, $options);

            // Log request for debugging
            if (config('bagisto-connector.debug')) {
                Log::debug('Bagisto API Request', [
                    'method' => $method,
                    'url' => $url,
                    'options' => $options,
                    'status' => $response->status(),
                ]);
            }

            if ($response->successful()) {
                return $response->json() ?? [];
            }

            // Handle authentication errors
            if ($response->status() === 401) {
                throw new BagistoAuthenticationException(
                    'Authentication failed. Invalid or expired API token.',
                    401
                );
            }

            // Handle other API errors
            throw new BagistoApiException(
                $response->json('message') ?? 'API request failed',
                $response->status(),
                $response->json()
            );

        } catch (ConnectionException $e) {
            Log::error('Bagisto connection error', [
                'method' => $method,
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            throw new BagistoConnectionException(
                'Failed to connect to Bagisto API: ' . $e->getMessage(),
                0,
                $e
            );

        } catch (RequestException $e) {
            Log::error('Bagisto request error', [
                'method' => $method,
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            throw new BagistoApiException(
                'Request failed: ' . $e->getMessage(),
                $e->getCode(),
                [],
                $e
            );
        }
    }
}
