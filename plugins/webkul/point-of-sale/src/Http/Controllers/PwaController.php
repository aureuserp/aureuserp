<?php

namespace Webkul\PointOfSale\Http\Controllers;

use Filament\Facades\Filament;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Webkul\PointOfSale\Filament\Pos\Pages\Register;
use Webkul\PointOfSale\Filament\Pos\Pages\Registers;
use Webkul\PointOfSale\Models\Config;

class PwaController extends Controller
{
    public function manifest(?Config $config = null): JsonResponse
    {
        $config ??= Registers::ownSession()?->config;

        $start = $config
            ? Register::getUrl(['config' => $config->getKey()], panel: 'pos')
            : Registers::getUrl(panel: 'pos');

        return response()->json([
            'id'                          => $config ? 'pos-'.$config->getKey() : 'pos',
            'name'                        => $config?->name ?? __('point-of-sale::filament/pos/pages/registers.title'),
            'short_name'                  => $config?->name ?? __('point-of-sale::filament/pos/pages/registers.title'),
            'scope'                       => static::scope(),
            'start_url'                   => $start,
            'display'                     => 'standalone',
            'orientation'                 => 'any',
            'background_color'            => '#ffffff',
            'theme_color'                 => '#2563eb',
            'prefer_related_applications' => false,
            'icons'                       => $this->icons($config),
        ], 200, ['Content-Type' => 'application/manifest+json']);
    }

    public function serviceWorker(): Response
    {
        return response($this->serviceWorkerSource(), 200, [
            'Content-Type'           => 'text/javascript',
            'Service-Worker-Allowed' => static::scope(),
            'Cache-Control'          => 'no-cache',
        ]);
    }

    protected static function scope(): string
    {
        return '/'.trim(Filament::getPanel('pos')->getPath(), '/').'/';
    }

    /**
     * @return array<int, array<string, string>>
     */
    protected function icons(?Config $config): array
    {
        $logo = $config?->company?->logo;

        $source = $logo ? Storage::url($logo) : asset('images/logo.svg');

        $type = str_ends_with($source, '.svg') ? 'image/svg+xml' : 'image/png';

        return [
            ['src' => $source, 'sizes' => 'any', 'type' => $type, 'purpose' => 'any'],
            ['src' => $source, 'sizes' => 'any', 'type' => $type, 'purpose' => 'maskable'],
        ];
    }

    protected function serviceWorkerSource(): string
    {
        $cache = 'pos-shell-v1';

        $scope = static::scope();

        return <<<JS
        const CACHE = '{$cache}';
        const SCOPE = '{$scope}';

        self.addEventListener('install', () => self.skipWaiting());

        self.addEventListener('activate', (event) => {
            event.waitUntil(
                caches.keys()
                    .then((keys) => Promise.all(keys.filter((key) => key !== CACHE).map((key) => caches.delete(key))))
                    .then(() => self.clients.claim())
            );
        });

        const isAsset = (url) => /\\.(?:js|css|woff2?|png|jpe?g|svg|ico)\$/.test(url.pathname);

        self.addEventListener('fetch', (event) => {
            const request = event.request;

            if (request.method !== 'GET') {
                return;
            }

            const url = new URL(request.url);

            if (url.origin !== self.location.origin) {
                return;
            }

            if (isAsset(url)) {
                event.respondWith(
                    caches.match(request).then((hit) => hit ?? fetch(request).then((response) => {
                        const copy = response.clone();

                        caches.open(CACHE).then((cache) => cache.put(request, copy));

                        return response;
                    }))
                );

                return;
            }

            if (request.mode === 'navigate' && url.pathname.startsWith(SCOPE)) {
                event.respondWith(
                    fetch(request)
                        .then((response) => {
                            const copy = response.clone();

                            caches.open(CACHE).then((cache) => cache.put(request, copy));

                            return response;
                        })
                        .catch(() => caches.match(request).then((hit) => hit ?? caches.match(SCOPE)))
                );
            }
        });
        JS;
    }
}
