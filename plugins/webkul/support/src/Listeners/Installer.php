<?php

namespace Webkul\Support\Listeners;

use GuzzleHttp\Client;
use Webkul\Security\Models\User;

class Installer
{
    /**
     * Api endpoint
     *
     * @var string
     */
    protected const API_ENDPOINT = 'https://updates.aureuserp.com/api/updates';

    /**
     * After Krayin is successfully installed
     *
     * @return void
     */
    public function installed()
    {
        $user = User::first();

        $httpClient = new Client;

        try {
            $httpClient->request('POST', self::API_ENDPOINT, [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'json'    => [
                    'domain' => config('app.url'),
                    'email' => $user?->email,
                    'name' => $user?->name,
                ],
            ]);
        } catch (\Exception $e) {
            /**
             * Skip the error
             */
        }
    }
}
