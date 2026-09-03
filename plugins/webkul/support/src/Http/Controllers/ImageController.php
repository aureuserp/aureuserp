<?php

namespace Webkul\Support\Http\Controllers;

use Illuminate\Http\Request;
use League\Glide\Filesystem\FileNotFoundException;
use League\Glide\Signatures\SignatureException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Webkul\Support\Services\ImageService;

class ImageController
{
    public function __invoke(Request $request, string $path, ImageService $service): StreamedResponse
    {
        try {
            $service->validate($path, $request->query());
        } catch (SignatureException $e) {
            abort(403);
        }

        try {
            return $service->response($path, $request->query());
        } catch (FileNotFoundException $e) {
            abort(404);
        }
    }
}
