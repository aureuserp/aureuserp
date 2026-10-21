<?php

namespace Webkul\PointOfSale\Http\Controllers\API\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Webkul\PointOfSale\Facades\PointOfSale;
use Webkul\PointOfSale\Http\Requests\DraftSyncRequest;
use Webkul\PointOfSale\Models\Session;
use Webkul\PointOfSale\Services\BootLoader;
use Webkul\PointOfSale\Support\PosAccess;

class DraftController extends Controller
{
    public function __construct(
        protected BootLoader $boot,
    ) {}

    public function index(Session $session): JsonResponse
    {
        abort_unless(PosAccess::reachesSession($session), 403);

        if (! $session->isLive()) {
            return response()->json([
                'message' => __('point-of-sale::system.session-workflow.assert-open.not-open', ['session' => $session->name]),
                'closed'  => true,
            ], 409);
        }

        return response()->json([
            'data' => $this->boot->drafts($session),
        ]);
    }

    public function store(DraftSyncRequest $request): JsonResponse
    {
        $result = PointOfSale::saveDraftOrders($request->validated('orders'));

        return response()->json([
            'data'   => collect($result['data'])->map(fn ($order): array => [
                'id'   => $order->id,
                'uuid' => $order->uuid,
            ])->values()->all(),
            'errors' => $result['errors'],
        ]);
    }
}
