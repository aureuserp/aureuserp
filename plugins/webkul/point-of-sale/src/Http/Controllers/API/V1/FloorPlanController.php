<?php

namespace Webkul\PointOfSale\Http\Controllers\API\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Throwable;
use Webkul\PointOfSale\Facades\PointOfSale;
use Webkul\PointOfSale\Http\Requests\FloorPlanRequest;
use Webkul\PointOfSale\Models\Config;
use Webkul\PointOfSale\Models\Floor;
use Webkul\PointOfSale\Services\BootLoader;
use Webkul\PointOfSale\Support\ClientMessage;
use Webkul\PointOfSale\Support\PosAccess;

class FloorPlanController extends Controller
{
    public function __construct(
        protected BootLoader $boot,
    ) {}

    public function store(FloorPlanRequest $request, Config $config): JsonResponse
    {
        abort_unless(PosAccess::reachesConfig($config) && Gate::allows('create', Floor::class), 403);

        $name = $request->validate(['name' => ['required', 'string', 'max:255']])['name'];

        $floor = PointOfSale::createFloor($config, $name);

        return response()->json(['data' => $this->boot->floorPayload($floor)], 201);
    }

    public function update(FloorPlanRequest $request, Floor $floor): JsonResponse
    {
        abort_unless($this->reachesFloor($floor) && Gate::allows('update', $floor), 403);

        try {
            $floor = PointOfSale::saveFloor($floor, $request->validated());
        } catch (Throwable $exception) {
            return response()->json(['message' => ClientMessage::for($exception)], 422);
        }

        return response()->json(['data' => $this->boot->floorPayload($floor)]);
    }

    public function storeBackground(Request $request, Floor $floor): JsonResponse
    {
        abort_unless($this->reachesFloor($floor) && Gate::allows('update', $floor), 403);

        $image = $request->validate([
            'image' => ['required', 'image', 'max:5120'],
        ])['image'];

        $floor = PointOfSale::setFloorBackgroundImage($floor, $image);

        return response()->json(['data' => $this->boot->floorPayload($floor)]);
    }

    public function destroyBackground(Floor $floor): JsonResponse
    {
        abort_unless($this->reachesFloor($floor) && Gate::allows('update', $floor), 403);

        $floor = PointOfSale::removeFloorBackgroundImage($floor);

        return response()->json(['data' => $this->boot->floorPayload($floor)]);
    }

    public function destroy(Floor $floor): JsonResponse
    {
        abort_unless($this->reachesFloor($floor) && Gate::allows('delete', $floor), 403);

        try {
            PointOfSale::deleteFloor($floor);
        } catch (Throwable $exception) {
            return response()->json(['message' => ClientMessage::for($exception)], 422);
        }

        return response()->json(['data' => ['id' => $floor->id]]);
    }

    protected function reachesFloor(Floor $floor): bool
    {
        return $floor->configs()->get()->contains(fn (Config $config): bool => PosAccess::reachesConfig($config));
    }
}
