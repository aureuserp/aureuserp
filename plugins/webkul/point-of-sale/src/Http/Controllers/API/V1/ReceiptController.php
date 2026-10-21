<?php

namespace Webkul\PointOfSale\Http\Controllers\API\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Webkul\PointOfSale\Models\Order;
use Webkul\PointOfSale\Services\ReceiptBuilder;
use Webkul\PointOfSale\Support\PosAccess;

class ReceiptController extends Controller
{
    public function __construct(
        protected ReceiptBuilder $receipts,
    ) {}

    public function show(string $order): JsonResponse
    {
        $model = Order::withoutGlobalScopes()->findOrFail($order);

        abort_unless(PosAccess::reachesOrder($model), 403);

        return response()->json([
            'data' => $this->receipts->build($model),
        ]);
    }
}
