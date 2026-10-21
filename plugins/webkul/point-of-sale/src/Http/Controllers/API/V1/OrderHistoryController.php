<?php

namespace Webkul\PointOfSale\Http\Controllers\API\V1;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Webkul\PointOfSale\Enums\OrderState;
use Webkul\PointOfSale\Http\Resources\V1\OrderHistoryResource;
use Webkul\PointOfSale\Models\Order;
use Webkul\PointOfSale\Models\Session;
use Webkul\PointOfSale\Support\PosAccess;

class OrderHistoryController extends Controller
{
    public function index(Request $request, Session $session): LengthAwarePaginator
    {
        abort_unless(PosAccess::reachesSession($session), 403);

        $search = trim((string) $request->string('search'));

        $status = (string) $request->string('status', 'active');

        $perPage = min(max((int) $request->integer('per_page', 20), 1), 100);

        $isRestaurant = (bool) $session->config?->is_restaurant;

        return Order::withoutGlobalScopes()
            ->where('session_id', $session->getKey())
            ->with(['partner', 'user', 'currency', 'lines.product', 'lines.taxes', 'payments.paymentMethod'])
            ->when($isRestaurant, fn (Builder $query) => $query->with('table.floor'))
            ->when(
                $status === 'active',
                fn (Builder $query) => $query->whereNot('state', OrderState::CANCELED),
                fn (Builder $query) => $query->where('state', $status),
            )
            ->when($search !== '', fn (Builder $query) => $query->where(
                fn (Builder $matches) => $matches
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%")
                    ->orWhere('receipt_code', 'like', "%{$search}%")
                    ->orWhereHas('partner', fn (Builder $partner) => $partner->where('name', 'like', "%{$search}%"))
                    ->when($isRestaurant, fn (Builder $matches) => $matches->orWhereHas(
                        'table',
                        fn (Builder $table) => $table
                            ->where('table_number', 'like', "%{$search}%")
                            ->orWhereHas('floor', fn (Builder $floor) => $floor->where('name', 'like', "%{$search}%")),
                    )),
            ))
            ->orderByDesc('id')
            ->paginate($perPage)
            ->through(fn (Order $order): OrderHistoryResource => new OrderHistoryResource($order));
    }
}
