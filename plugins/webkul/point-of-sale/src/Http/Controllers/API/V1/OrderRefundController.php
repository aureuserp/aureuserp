<?php

namespace Webkul\PointOfSale\Http\Controllers\API\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Webkul\PointOfSale\Exceptions\OrderNotRefundableException;
use Webkul\PointOfSale\Exceptions\RefundExceedsSoldQuantityException;
use Webkul\PointOfSale\Http\Requests\OrderRefundRequest;
use Webkul\PointOfSale\Models\Order;
use Webkul\PointOfSale\Services\RefundProcessor;
use Webkul\PointOfSale\Support\PosAccess;

class OrderRefundController extends Controller
{
    public function __construct(
        protected RefundProcessor $refunds,
    ) {}

    public function refundable(string $order): JsonResponse
    {
        $model = $this->resolveOrder($order);

        return response()->json([
            'data' => $this->refunds->refundableLines($model),
        ]);
    }

    public function store(OrderRefundRequest $request, string $order): JsonResponse
    {
        $model = $this->resolveOrder($order);

        try {
            $pending = $this->refunds->pendingRefund($model, $request->validated('quantities'));
        } catch (OrderNotRefundableException|RefundExceedsSoldQuantityException $exception) {
            return response()->json(['message' => $exception->getMessage()], $exception->getStatusCode());
        }

        return response()->json([
            'data' => $pending,
        ]);
    }

    protected function resolveOrder(string $order): Order
    {
        $model = Order::withoutGlobalScopes()->findOrFail($order);

        abort_unless(PosAccess::reachesOrder($model), 403);

        return $model;
    }
}
