<?php

namespace Webkul\Inventory\Http\Controllers\API\V1;

use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;
use Knuckles\Scribe\Attributes\Subgroup;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Webkul\Inventory\Http\Resources\V1\MoveLineResource;
use Webkul\Inventory\Models\MoveLine;

#[Group('Inventory API Management')]
#[Subgroup('Moves', 'Manage inventory move lines')]
#[Authenticated]
class MoveController extends Controller
{
    protected array $allowedIncludes = [
        'move',
        'move.operation',
        'move.operation.operationType',
        'operation',
        'product',
        'uom',
        'package',
        'resultPackage',
        'lot',
        'partner',
        'sourceLocation',
        'destinationLocation',
        'company',
        'creator',
    ];

    #[Endpoint('List moves', 'Retrieve a paginated list of move lines with reusable relation filters')]
    #[QueryParam('include', 'string', 'Comma-separated relationships to include', required: false, example: 'move,operation,product')]
    #[QueryParam('filter[id]', 'string', 'Filter by move line IDs', required: false, example: '1,2')]
    #[QueryParam('filter[move_id]', 'string', 'Filter by move IDs', required: false, example: '1,2')]
    #[QueryParam('filter[operation_id]', 'string', 'Filter by operation IDs', required: false, example: '1,2')]
    #[QueryParam('filter[product_id]', 'string', 'Filter by product IDs', required: false, example: '1,2')]
    #[QueryParam('filter[lot_id]', 'string', 'Filter by lot IDs', required: false, example: '1,2')]
    #[QueryParam('filter[package_id]', 'string', 'Filter by package IDs', required: false, example: '1,2')]
    #[QueryParam('filter[scrap_id]', 'string', 'Filter by scrap IDs (through move)', required: false, example: '1,2')]
    #[QueryParam('filter[warehouse_id]', 'string', 'Filter by warehouse IDs (through move)', required: false, example: '1,2')]
    #[QueryParam('filter[location_id]', 'string', 'Filter by source/destination location IDs', required: false, example: '1,2')]
    #[QueryParam('sort', 'string', 'Sort field', required: false, example: '-created_at')]
    #[ResponseFromApiResource(MoveLineResource::class, MoveLine::class, collection: true, paginate: 10)]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index()
    {
        abort_unless(auth()->user()?->can('view_any_inventory_move'), 403);

        $moves = QueryBuilder::for(MoveLine::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('move_id'),
                AllowedFilter::exact('operation_id'),
                AllowedFilter::exact('product_id'),
                AllowedFilter::exact('lot_id'),
                AllowedFilter::exact('package_id'),
                AllowedFilter::exact('result_package_id'),
                AllowedFilter::exact('source_location_id'),
                AllowedFilter::exact('destination_location_id'),
                AllowedFilter::exact('state'),
                AllowedFilter::partial('reference'),
                AllowedFilter::callback('location_id', function ($query, $value): void {
                    $locationIds = array_filter(explode(',', (string) $value));

                    $query->where(function ($query) use ($locationIds): void {
                        $query->whereIn('source_location_id', $locationIds)
                            ->orWhereIn('destination_location_id', $locationIds);
                    });
                }),
                AllowedFilter::callback('warehouse_id', function ($query, $value): void {
                    $warehouseIds = array_filter(explode(',', (string) $value));

                    $query->whereHas('move', fn ($query) => $query->whereIn('warehouse_id', $warehouseIds));
                }),
                AllowedFilter::callback('scrap_id', function ($query, $value): void {
                    $scrapIds = array_filter(explode(',', (string) $value));

                    $query->whereHas('move', fn ($query) => $query->whereIn('scrap_id', $scrapIds));
                }),
            ])
            ->allowedSorts([
                'id',
                'scheduled_at',
                'reference',
                'qty',
                'uom_qty',
                'state',
                'created_at',
                'updated_at',
            ])
            ->allowedIncludes($this->allowedIncludes)
            ->paginate();

        return MoveLineResource::collection($moves);
    }
}
