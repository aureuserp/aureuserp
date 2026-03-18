<?php

namespace Webkul\Inventory\Http\Controllers\API\V1;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource as ProductFilamentResource;
use Webkul\Inventory\Http\Requests\ProductQuantityCountRequest;
use Webkul\Inventory\Http\Requests\ProductQuantityRequest;
use Webkul\Inventory\Http\Resources\V1\ProductQuantityResource;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Product;
use Webkul\Inventory\Models\ProductQuantity;
use Webkul\Inventory\Models\Warehouse;

#[Group('Inventory API Management')]
#[Subgroup('Quantities', 'Manage inventory product quantities')]
#[Authenticated]
class QuantityController extends Controller
{
    protected array $allowedIncludes = [
        'product',
        'location',
        'storageCategory',
        'lot',
        'package',
        'partner',
        'user',
        'company',
        'creator',
    ];

    #[Endpoint('List quantities', 'Retrieve a paginated list of quantities with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> product, location, storageCategory, lot, package, partner, user, company, creator', required: false, example: 'product,location')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false)]
    #[QueryParam('filter[product_id]', 'string', 'Filter by product IDs', required: false)]
    #[QueryParam('filter[location_id]', 'string', 'Filter by location IDs', required: false)]
    #[QueryParam('filter[lot_id]', 'string', 'Filter by lot IDs', required: false)]
    #[QueryParam('filter[package_id]', 'string', 'Filter by package IDs', required: false)]
    #[QueryParam('filter[inventory_quantity_set]', 'string', 'Filter by inventory quantity set (0 or 1)', required: false, example: '1')]
    #[QueryParam('sort', 'string', 'Sort field', required: false, example: '-updated_at')]
    #[ResponseFromApiResource(ProductQuantityResource::class, ProductQuantity::class, collection: true, paginate: 10)]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index()
    {
        Gate::authorize('viewAny', ProductQuantity::class);

        $quantities = QueryBuilder::for(ProductQuantity::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('product_id'),
                AllowedFilter::exact('location_id'),
                AllowedFilter::exact('storage_category_id'),
                AllowedFilter::exact('lot_id'),
                AllowedFilter::exact('package_id'),
                AllowedFilter::exact('partner_id'),
                AllowedFilter::exact('user_id'),
                AllowedFilter::exact('company_id'),
                AllowedFilter::exact('inventory_quantity_set'),
            ])
            ->allowedSorts([
                'id',
                'quantity',
                'reserved_quantity',
                'counted_quantity',
                'difference_quantity',
                'inventory_diff_quantity',
                'incoming_at',
                'scheduled_at',
                'created_at',
                'updated_at',
            ])
            ->allowedIncludes($this->allowedIncludes)
            ->paginate();

        return ProductQuantityResource::collection($quantities);
    }

    #[Endpoint('Create quantity', 'Create a new product quantity count')]
    #[ResponseFromApiResource(ProductQuantityResource::class, ProductQuantity::class, status: 201, additional: ['message' => 'Quantity created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"location_id": ["The location id field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(ProductQuantityRequest $request)
    {
        Gate::authorize('create', ProductQuantity::class);

        $data = $this->preparePayload($request->validated());

        $existingQuantity = ProductQuantity::query()
            ->where('location_id', $data['location_id'])
            ->where('product_id', $data['product_id'])
            ->where('package_id', $data['package_id'] ?? null)
            ->where('lot_id', $data['lot_id'] ?? null)
            ->exists();

        if ($existingQuantity) {
            return response()->json([
                'message' => 'A quantity already exists for this product, location, lot, and package combination.',
            ], 422);
        }

        $quantity = ProductQuantity::create($data);

        return (new ProductQuantityResource($quantity->load($this->allowedIncludes)))
            ->additional(['message' => 'Quantity created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show quantity', 'Retrieve a specific quantity by ID')]
    #[UrlParam('id', 'integer', 'The quantity ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> product, location, storageCategory, lot, package, partner, user, company, creator', required: false, example: 'product,location')]
    #[ResponseFromApiResource(ProductQuantityResource::class, ProductQuantity::class)]
    #[Response(status: 404, description: 'Quantity not found')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $id)
    {
        Gate::authorize('viewAny', ProductQuantity::class);

        $quantity = QueryBuilder::for(ProductQuantity::query()->where('id', $id))
            ->allowedIncludes($this->allowedIncludes)
            ->firstOrFail();

        return new ProductQuantityResource($quantity);
    }

    #[Endpoint('Update quantity', 'Update a quantity count entry')]
    #[UrlParam('id', 'integer', 'The quantity ID', required: true, example: 1)]
    #[ResponseFromApiResource(ProductQuantityResource::class, ProductQuantity::class, additional: ['message' => 'Quantity updated successfully.'])]
    #[Response(status: 404, description: 'Quantity not found')]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"counted_quantity": ["The counted quantity field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(ProductQuantityCountRequest $request, string $id)
    {
        Gate::authorize('create', ProductQuantity::class);

        $quantity = ProductQuantity::findOrFail($id);
        $data = $request->validated();
        $data['inventory_quantity_set'] = true;
        $data['inventory_diff_quantity'] = $data['counted_quantity'] - $quantity->quantity;

        $quantity->update($data);

        return (new ProductQuantityResource($quantity->fresh()->load($this->allowedIncludes)))
            ->additional(['message' => 'Quantity updated successfully.']);
    }

    #[Endpoint('Apply quantity', 'Apply counted quantity and generate adjustment move')]
    #[UrlParam('id', 'integer', 'The quantity ID', required: true, example: 1)]
    #[ResponseFromApiResource(ProductQuantityResource::class, ProductQuantity::class, additional: ['message' => 'Quantity applied successfully.'])]
    #[Response(status: 404, description: 'Quantity not found')]
    #[Response(status: 422, description: 'Quantity is not marked for inventory adjustment.')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function apply(string $id)
    {
        Gate::authorize('create', ProductQuantity::class);

        $record = ProductQuantity::findOrFail($id);

        if (! $record->inventory_quantity_set) {
            return response()->json([
                'message' => 'Quantity is not marked for inventory adjustment.',
            ], 422);
        }

        return DB::transaction(function () use ($record) {
            $adjustmentLocation = Location::query()
                ->where('type', LocationType::INVENTORY)
                ->where('is_scrap', false)
                ->first();

            if (! $adjustmentLocation) {
                return response()->json([
                    'message' => 'Inventory adjustment location is not configured.',
                ], 422);
            }

            $countedQuantity = $record->counted_quantity;
            $diffQuantity = $record->inventory_diff_quantity;

            $record->update([
                'quantity'                => $countedQuantity,
                'counted_quantity'        => 0,
                'inventory_diff_quantity' => 0,
                'inventory_quantity_set'  => false,
            ]);

            ProductQuantity::updateOrCreate(
                [
                    'location_id' => $adjustmentLocation->id,
                    'product_id'  => $record->product_id,
                    'lot_id'      => $record->lot_id,
                ],
                [
                    'quantity'               => -$record->product->on_hand_quantity,
                    'company_id'             => $record->company_id,
                    'creator_id'             => Auth::id(),
                    'incoming_at'            => now(),
                    'inventory_quantity_set' => false,
                ]
            );

            $sourceLocationId = $diffQuantity < 0 ? $record->location_id : $adjustmentLocation->id;
            $destinationLocationId = $diffQuantity < 0 ? $adjustmentLocation->id : $record->location_id;

            ProductFilamentResource::createMove($record, abs($diffQuantity), $sourceLocationId, $destinationLocationId);

            return (new ProductQuantityResource($record->fresh()->load($this->allowedIncludes)))
                ->additional(['message' => 'Quantity applied successfully.']);
        });
    }

    #[Endpoint('Clear quantity', 'Clear pending counted quantity without applying adjustments')]
    #[UrlParam('id', 'integer', 'The quantity ID', required: true, example: 1)]
    #[ResponseFromApiResource(ProductQuantityResource::class, ProductQuantity::class, additional: ['message' => 'Quantity cleared successfully.'])]
    #[Response(status: 404, description: 'Quantity not found')]
    #[Response(status: 422, description: 'Quantity is not marked for inventory adjustment.')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function clear(string $id)
    {
        Gate::authorize('create', ProductQuantity::class);

        $record = ProductQuantity::findOrFail($id);

        if (! $record->inventory_quantity_set) {
            return response()->json([
                'message' => 'Quantity is not marked for inventory adjustment.',
            ], 422);
        }

        $record->update([
            'inventory_quantity_set'  => false,
            'counted_quantity'        => 0,
            'inventory_diff_quantity' => 0,
        ]);

        return (new ProductQuantityResource($record->fresh()->load($this->allowedIncludes)))
            ->additional(['message' => 'Quantity cleared successfully.']);
    }

    #[Endpoint('Delete quantity', 'Delete a quantity')]
    #[UrlParam('id', 'integer', 'The quantity ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Quantity deleted successfully', content: '{"message":"Quantity deleted successfully."}')]
    #[Response(status: 404, description: 'Quantity not found')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $id)
    {
        $quantity = ProductQuantity::findOrFail($id);

        Gate::authorize('delete', $quantity);

        $quantity->delete();

        return response()->json([
            'message' => 'Quantity deleted successfully.',
        ]);
    }

    protected function preparePayload(array $data): array
    {
        $product = Product::query()->findOrFail($data['product_id']);

        $data['location_id'] = $data['location_id'] ?? Warehouse::query()->first()?->lot_stock_location_id;
        $data['creator_id'] = Auth::id();
        $data['company_id'] = $data['company_id'] ?? $product->company_id;
        $data['inventory_quantity_set'] = true;
        $data['inventory_diff_quantity'] = $data['counted_quantity'];
        $data['incoming_at'] = now();
        $data['scheduled_at'] = $data['scheduled_at'] ?? now();

        return $data;
    }
}
