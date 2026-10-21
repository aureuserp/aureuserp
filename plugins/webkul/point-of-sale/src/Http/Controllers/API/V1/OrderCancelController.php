<?php

namespace Webkul\PointOfSale\Http\Controllers\API\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Webkul\PointOfSale\Enums\OrderState;
use Webkul\PointOfSale\Exceptions\OrderAlreadyPaidException;
use Webkul\PointOfSale\Facades\PointOfSale;
use Webkul\PointOfSale\Http\Requests\OrderCancelRequest;
use Webkul\PointOfSale\Http\Resources\V1\OrderResource;
use Webkul\PointOfSale\Models\Order;
use Webkul\PointOfSale\Support\PosAccess;

class OrderCancelController extends Controller
{
    public function store(OrderCancelRequest $request): JsonResponse
    {
        $order = Order::withoutGlobalScopes()->where('uuid', $request->validated('uuid'))->firstOrFail();

        abort_unless(PosAccess::reachesOrder($order), 403);

        if ($order->state !== OrderState::CANCELED) {
            try {
                $order = PointOfSale::cancelOrder($order);
            } catch (OrderAlreadyPaidException $exception) {
                return response()->json(['message' => $exception->getMessage()], $exception->getStatusCode());
            }
        }

        return response()->json([
            'data' => new OrderResource($order),
        ]);
    }
}
