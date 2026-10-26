<?php

namespace Webkul\PointOfSale\Http\Controllers\API\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Webkul\PointOfSale\Models\Config;
use Webkul\PointOfSale\Services\BootLoader;
use Webkul\PointOfSale\Support\PosAccess;

class ProductLotController extends Controller
{
    public function __construct(
        protected BootLoader $boot,
    ) {}

    public function index(Config $config, int $product): JsonResponse
    {
        abort_unless(PosAccess::reachesConfig($config), 403);

        return response()->json([
            'data' => $this->boot->lotsFor($config, [$product]),
        ]);
    }
}
