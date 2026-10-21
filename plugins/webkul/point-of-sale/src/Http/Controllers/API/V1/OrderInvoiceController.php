<?php

namespace Webkul\PointOfSale\Http\Controllers\API\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Throwable;
use Webkul\PointOfSale\Facades\PointOfSale;
use Webkul\PointOfSale\Http\Resources\V1\OrderResource;
use Webkul\PointOfSale\Models\Order;
use Webkul\PointOfSale\Support\PosAccess;

class OrderInvoiceController extends Controller
{
    public function store(Request $request, string $order): JsonResponse
    {
        $model = Order::withoutGlobalScopes()->findOrFail($order);

        abort_unless(PosAccess::reachesOrder($model), 403);

        $data = $request->validate([
            'partner_id' => ['nullable', 'integer', 'exists:partners_partners,id'],
        ]);

        if (! $model->partner_id && isset($data['partner_id'])) {
            $model->partner_id = (int) $data['partner_id'];

            $model->save();
        }

        try {
            PointOfSale::invoiceOrder($model);
        } catch (Throwable $exception) {
            $status = method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 422;

            return response()->json(['message' => $exception->getMessage()], $status);
        }

        return response()->json([
            'data' => new OrderResource($model->refresh()),
        ]);
    }
}
