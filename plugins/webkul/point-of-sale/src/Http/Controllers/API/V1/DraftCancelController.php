<?php

namespace Webkul\PointOfSale\Http\Controllers\API\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Throwable;
use Webkul\PointOfSale\Enums\OrderState;
use Webkul\PointOfSale\Facades\PointOfSale;
use Webkul\PointOfSale\Models\Order;
use Webkul\PointOfSale\Models\Session;
use Webkul\PointOfSale\Support\PosAccess;

class DraftCancelController extends Controller
{
    public function store(Session $session): JsonResponse
    {
        abort_unless(PosAccess::reachesSession($session), 403);

        $orders = Order::withoutGlobalScopes()
            ->where('session_id', $session->getKey())
            ->where('state', OrderState::DRAFT)
            ->get();

        try {
            $orders->each(fn (Order $order) => PointOfSale::cancelOrder($order));
        } catch (Throwable $exception) {
            $status = method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 422;

            return response()->json(['message' => $exception->getMessage()], $status);
        }

        return response()->json([
            'data' => ['canceled' => $orders->count()],
        ]);
    }
}
