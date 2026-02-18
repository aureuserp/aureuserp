<?php

namespace Webkul\Purchase\Http\Controllers\API\V1;

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
use Webkul\Purchase\Models\PurchaseOrder;

#[Group('Purchase API Management')]
#[Subgroup('Purchase Order Receipts', 'Manage purchase order receipts')]
#[Authenticated]
class PurchaseOrderReceiptController extends Controller
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

    #[Endpoint('List purchase order receipts', 'Retrieve a paginated list of receipts for a specific purchase order')]
    #[UrlParam('purchase_order_id', 'integer', 'The purchase order ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> user, owner, operationType, sourceLocation, destinationLocation, backOrder, return, partner, company, creator, moves, moveLines', required: false, example: 'partner,moves')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of receipt IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[state]', 'string', 'Filter by receipt state', required: false, example: 'assigned')]
    #[QueryParam('filter[move_type]', 'string', 'Filter by move type', required: false, example: 'incoming')]
    #[QueryParam('sort', 'string', 'Sort field', example: '-created_at')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(OperationResource::class, Operation::class, collection: true, paginate: 10)]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index(string $purchaseOrder)
    {
        $purchaseOrderModel = PurchaseOrder::findOrFail($purchaseOrder);

        Gate::authorize('view', $purchaseOrderModel);

        $receipts = QueryBuilder::for($purchaseOrderModel->operations())
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('state'),
                AllowedFilter::exact('move_type'),
            ])
            ->allowedSorts(['id', 'name', 'state', 'scheduled_at', 'closed_at', 'created_at'])
            ->allowedIncludes($this->allowedIncludes)
            ->paginate();

        return OperationResource::collection($receipts);
    }
}
