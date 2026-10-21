<?php

namespace Webkul\PointOfSale\Http\Controllers\API\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Webkul\PointOfSale\Http\Requests\TerminalProductRequest;
use Webkul\PointOfSale\Models\Config;
use Webkul\PointOfSale\Services\BootLoader;
use Webkul\PointOfSale\Services\TerminalProductCreator;

class TerminalProductController extends Controller
{
    public function __construct(
        protected TerminalProductCreator $creator,
        protected BootLoader $boot,
    ) {}

    public function store(TerminalProductRequest $request, Config $config): JsonResponse
    {
        $product = $this->creator->create($config, $request->validated());

        return response()->json([
            'data' => $this->boot->productPayload($config, $product->id),
        ], 201);
    }
}
