<?php

namespace Webkul\PointOfSale\Http\Controllers\API\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Webkul\PointOfSale\Facades\PointOfSale;
use Webkul\PointOfSale\Http\Requests\OrderSyncRequest;
use Webkul\PointOfSale\Http\Resources\V1\OrderResource;

class OrderSyncController extends Controller
{
    public function store(OrderSyncRequest $request): JsonResponse
    {
        $result = PointOfSale::syncOrders($request->validated('orders'));

        return response()->json([
            'data'   => OrderResource::collection(collect($result['data'])),
            'errors' => $result['errors'],
        ]);
    }
}
