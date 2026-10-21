<?php

namespace Webkul\PointOfSale\Http\Controllers\API\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Webkul\PointOfSale\Models\Config;
use Webkul\PointOfSale\Services\CatalogLoader;

class CatalogController extends Controller
{
    public function __construct(
        protected CatalogLoader $catalog,
    ) {}

    public function show(Request $request, Config $config): JsonResponse
    {
        return response()->json([
            'data' => $this->catalog->load($config, $request->string('search')->toString()),
        ]);
    }
}
