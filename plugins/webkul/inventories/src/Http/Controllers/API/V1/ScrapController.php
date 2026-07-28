<?php

namespace Webkul\Inventory\Http\Controllers\API\V1;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
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
use Webkul\Inventory\Enums\ScrapState;
use Webkul\Inventory\Http\Requests\ScrapRequest;
use Webkul\Inventory\Http\Resources\V1\ScrapResource;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Product;
use Webkul\Inventory\Models\Scrap;
use Webkul\Inventory\Models\Warehouse;

#[Group('Inventory API Management')]
#[Subgroup('Scraps', 'Manage inventory scraps')]
#[Authenticated]
class ScrapController extends Controller
{
    protected array $allowedIncludes = [
        'product',
        'uom',
        'lot',
        'package',
        'partner',
        'operation',
        'sourceLocation',
        'destinationLocation',
        'company',
        'creator',
        'tags',
        'moves',
        'moveLines',
    ];

    #[Endpoint('List scraps', 'Retrieve a paginated list of scraps with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> product, uom, lot, package, partner, operation, sourceLocation, destinationLocation, company, creator, tags, moves, moveLines', required: false, example: 'product,sourceLocation')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false)]
    #[QueryParam('filter[name]', 'string', 'Filter by scrap name', required: false, example: 'SP/1')]
    #[QueryParam('filter[state]', 'string', 'Filter by scrap state', required: false, example: 'draft')]
    #[QueryParam('filter[product_id]', 'string', 'Filter by product IDs', required: false)]
    #[QueryParam('filter[source_location_id]', 'string', 'Filter by source location IDs', required: false)]
    #[QueryParam('filter[destination_location_id]', 'string', 'Filter by destination location IDs', required: false)]
    #[QueryParam('sort', 'string', 'Sort field', required: false, example: '-created_at')]
    #[ResponseFromApiResource(ScrapResource::class, Scrap::class, collection: true, paginate: 10)]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index()
    {
        Gate::authorize('viewAny', Scrap::class);

        $scraps = QueryBuilder::for(Scrap::class)
            ->allowedFilters(
                AllowedFilter::exact('id'),
                AllowedFilter::partial('name'),
                AllowedFilter::exact('state'),
                AllowedFilter::exact('product_id'),
                AllowedFilter::exact('uom_id'),
                AllowedFilter::exact('lot_id'),
                AllowedFilter::exact('package_id'),
                AllowedFilter::exact('partner_id'),
                AllowedFilter::exact('operation_id'),
                AllowedFilter::exact('source_location_id'),
                AllowedFilter::exact('destination_location_id'),
                AllowedFilter::exact('company_id'),
                AllowedFilter::exact('should_replenish'),
            )
            ->allowedSorts('id', 'name', 'state', 'qty', 'closed_at', 'created_at', 'updated_at')
            ->allowedIncludes(...$this->allowedIncludes)
            ->paginate();

        return ScrapResource::collection($scraps);
    }

    #[Endpoint('Create scrap', 'Create a new scrap')]
    #[ResponseFromApiResource(ScrapResource::class, Scrap::class, status: 201, additional: ['message' => 'Scrap created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"qty": ["The qty field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(ScrapRequest $request)
    {
        Gate::authorize('create', Scrap::class);

        $data = $request->validated();
        $data = $this->prepareScrapPayload($data);

        $scrap = Scrap::create($data);
        $scrap->tags()->sync($data['tags'] ?? []);

        return (new ScrapResource($scrap->load($this->allowedIncludes)))
            ->additional(['message' => 'Scrap created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show scrap', 'Retrieve a specific scrap by ID')]
    #[UrlParam('id', 'integer', 'The scrap ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> product, uom, lot, package, partner, operation, sourceLocation, destinationLocation, company, creator, tags, moves, moveLines', required: false, example: 'product,company')]
    #[ResponseFromApiResource(ScrapResource::class, Scrap::class)]
    #[Response(status: 404, description: 'Scrap not found')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $id)
    {
        $scrap = QueryBuilder::for(Scrap::where('id', $id))
            ->allowedIncludes(...$this->allowedIncludes)
            ->firstOrFail();

        Gate::authorize('view', $scrap);

        return new ScrapResource($scrap);
    }

    #[Endpoint('Update scrap', 'Update an existing scrap')]
    #[UrlParam('id', 'integer', 'The scrap ID', required: true, example: 1)]
    #[ResponseFromApiResource(ScrapResource::class, Scrap::class, additional: ['message' => 'Scrap updated successfully.'])]
    #[Response(status: 404, description: 'Scrap not found')]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"qty": ["The qty field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(ScrapRequest $request, string $id)
    {
        $scrap = Scrap::findOrFail($id);

        Gate::authorize('update', $scrap);

        if ($scrap->state === ScrapState::DONE) {
            return response()->json([
                'message' => 'Done scraps cannot be updated.',
            ], 422);
        }

        $data = $this->prepareScrapPayload($request->validated(), $scrap);

        $scrap->update($data);

        if (array_key_exists('tags', $data)) {
            $scrap->tags()->sync($data['tags'] ?? []);
        }

        return (new ScrapResource($scrap->fresh()->load($this->allowedIncludes)))
            ->additional(['message' => 'Scrap updated successfully.']);
    }

    #[Endpoint('Delete scrap', 'Delete a scrap')]
    #[UrlParam('id', 'integer', 'The scrap ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Scrap deleted successfully', content: '{"message":"Scrap deleted successfully."}')]
    #[Response(status: 404, description: 'Scrap not found')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $id)
    {
        $scrap = Scrap::findOrFail($id);

        Gate::authorize('delete', $scrap);

        if ($scrap->state === ScrapState::DONE) {
            return response()->json([
                'message' => 'Done scraps cannot be deleted.',
            ], 422);
        }

        $scrap->delete();

        return response()->json([
            'message' => 'Scrap deleted successfully.',
        ]);
    }

    #[Endpoint('Validate scrap', 'Validate a draft scrap and update stock quantities')]
    #[UrlParam('id', 'integer', 'The scrap ID', required: true, example: 1)]
    #[ResponseFromApiResource(ScrapResource::class, Scrap::class, additional: ['message' => 'Scrap validated successfully.'])]
    #[Response(status: 404, description: 'Scrap not found')]
    #[Response(status: 422, description: 'Only draft scraps can be validated.')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function validateScrap(string $id)
    {
        $scrap = Scrap::findOrFail($id);

        Gate::authorize('update', $scrap);

        if ($scrap->state === ScrapState::DONE) {
            return response()->json([
                'message' => 'Only draft scraps can be validated.',
            ], 422);
        }

        return DB::transaction(function () use ($scrap) {
            if (! $scrap->validate()) {
                return response()->json([
                    'message' => 'Insufficient source quantity for this scrap.',
                ], 422);
            }

            return (new ScrapResource($scrap->fresh()->load($this->allowedIncludes)))
                ->additional(['message' => 'Scrap validated successfully.']);
        });
    }

    protected function prepareScrapPayload(array $data, ?Scrap $existing = null): array
    {
        if (! isset($data['uom_id']) && isset($data['product_id'])) {
            $data['uom_id'] = Product::query()->find($data['product_id'])?->uom_id;
        }

        if (! isset($data['source_location_id'])) {
            $data['source_location_id'] = $existing?->source_location_id
                ?? Warehouse::query()->first()?->lot_stock_location_id;
        }

        if (! isset($data['destination_location_id'])) {
            $data['destination_location_id'] = $existing?->destination_location_id
                ?? Location::query()->where('is_scrap', true)->value('id');
        }

        if (! isset($data['company_id'])) {
            $data['company_id'] = $existing?->company_id ?? Auth::user()?->default_company_id;
        }

        if (! isset($data['state']) && ! $existing) {
            $data['state'] = ScrapState::DRAFT;
        }

        $data['creator_id'] = $existing?->creator_id ?? Auth::id();

        foreach (['uom_id', 'source_location_id', 'destination_location_id', 'company_id'] as $field) {
            if (empty($data[$field])) {
                throw ValidationException::withMessages([
                    $field => ["The {$field} field could not be resolved automatically."],
                ]);
            }
        }

        return $data;
    }
}
