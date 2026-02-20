<?php

namespace Webkul\Sale\Http\Controllers\API\V1;

use Illuminate\Support\Facades\Gate;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;
use Knuckles\Scribe\Attributes\Subgroup;
use Knuckles\Scribe\Attributes\UrlParam;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Webkul\Inventory\Http\Resources\V1\OperationResource;
use Webkul\Inventory\Models\Operation;
use Webkul\Sale\Models\Order;

#[Group('Sales API Management')]
#[Subgroup('Order Deliveries', 'Manage sales order deliveries')]
#[Authenticated]
class OrderDeliveryController extends Controller
{
    protected array $allowedIncludes = [
        'user',
        'owner',
        'operationType',
        'sourceLocation',
        'destinationLocation',
        'backOrder',
        'return',
        'partner',
        'company',
        'creator',
        'moves',
        'moveLines',
    ];

    #[Endpoint('List order deliveries', 'Retrieve a paginated list of deliveries for a specific order')]
    #[UrlParam('order_id', 'integer', 'The order ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> user, owner, operationType, sourceLocation, destinationLocation, backOrder, return, partner, company, creator, moves, moveLines', required: false, example: 'partner,moves')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of delivery IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[state]', 'string', 'Filter by delivery state', required: false, example: 'assigned')]
    #[QueryParam('filter[move_type]', 'string', 'Filter by move type', required: false, example: 'incoming')]
    #[QueryParam('sort', 'string', 'Sort field', example: '-created_at')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(OperationResource::class, Operation::class, collection: true, paginate: 10)]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index(string $order)
    {
        $orderModel = Order::findOrFail($order);

        Gate::authorize('view', $orderModel);

        $deliveries = QueryBuilder::for($orderModel->operations())
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('state'),
                AllowedFilter::exact('move_type'),
            ])
            ->allowedSorts(['id', 'name', 'state', 'scheduled_at', 'closed_at', 'created_at'])
            ->allowedIncludes($this->allowedIncludes)
            ->paginate();

        return OperationResource::collection($deliveries);
    }
}
