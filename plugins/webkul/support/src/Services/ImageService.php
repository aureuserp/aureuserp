<?php

namespace Webkul\Support\Services;

use Illuminate\Support\Facades\Storage;
use League\Glide\Server;
use League\Glide\ServerFactory;
use League\Glide\Signatures\SignatureFactory;
use League\Glide\Urls\UrlBuilderFactory;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImageService
{
    public const BASE_PATH = 'img/';

    public function url(string $path, array $params): string
    {
        $signed = UrlBuilderFactory::create('/'.self::BASE_PATH, config('app.key'))
            ->getUrl($path, $params);

        return url($signed);
    }

    public function validate(string $path, array $params): void
    {
        SignatureFactory::create(config('app.key'))->validateRequest(self::BASE_PATH.$path, $params);
    }

    public function response(string $path, array $params): StreamedResponse
    {
        $server = $this->server();

        $cachePath = $server->makeImage($path, $params);

        $cache = $server->getCache();

        return new StreamedResponse(function () use ($cache, $cachePath): void {
            fpassthru($cache->readStream($cachePath));
        }, 200, [
            'Content-Type'   => $cache->mimeType($cachePath),
            'Content-Length' => (string) $cache->fileSize($cachePath),
            'Cache-Control'  => 'public, max-age=31536000',
        ]);
    }

    public function server(): Server
    {
        return ServerFactory::create([
            'source'            => Storage::disk('public')->getDriver(),
            'cache'             => Storage::disk('local')->getDriver(),
            'cache_path_prefix' => 'glide-cache',
            'driver'            => extension_loaded('imagick') ? 'imagick' : 'gd',
        ]);
    }
}
