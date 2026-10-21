<?php

namespace Webkul\PointOfSale\Http\Controllers\API\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Webkul\PointOfSale\Facades\PointOfSale;
use Webkul\PointOfSale\Filament\Pos\Pages\Registers;
use Webkul\PointOfSale\Http\Resources\V1\RegisterResource;
use Webkul\PointOfSale\Models\Config;
use Webkul\PointOfSale\Support\PosAccess;

class RegisterListController extends Controller
{
    public function index(): JsonResponse
    {
        $configs = Config::query()
            ->where('is_active', true)
            ->orderBy('sort')
            ->get();

        return response()->json([
            'data'           => RegisterResource::collection($configs),
            'own_session_id' => Registers::ownSession()?->getKey(),
        ]);
    }

    public function discard(Config $config): JsonResponse
    {
        abort_unless(PosAccess::reachesConfig($config), 403);

        $session = PointOfSale::liveSessionFor($config);

        abort_unless($session !== null, 404);
        abort_unless(PointOfSale::isSessionDiscardable($session), 422);

        PointOfSale::discardSession($session);

        return response()->json(['data' => null]);
    }
}
