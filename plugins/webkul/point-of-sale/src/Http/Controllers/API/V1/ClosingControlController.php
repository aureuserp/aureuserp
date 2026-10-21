<?php

namespace Webkul\PointOfSale\Http\Controllers\API\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Webkul\PointOfSale\Models\Session;
use Webkul\PointOfSale\Services\ClosingControlReport;
use Webkul\PointOfSale\Support\PosAccess;

class ClosingControlController extends Controller
{
    public function __construct(
        protected ClosingControlReport $report,
    ) {}

    public function show(Session $session): JsonResponse
    {
        abort_unless(PosAccess::reachesSession($session), 403);

        return response()->json([
            'data' => $this->report->build($session->refresh()),
        ]);
    }
}
