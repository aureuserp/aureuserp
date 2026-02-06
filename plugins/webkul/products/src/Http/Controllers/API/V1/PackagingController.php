<?php

namespace Webkul\Product\Http\Controllers\API\V1;

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
use Webkul\Product\Http\Requests\PackagingRequest;
use Webkul\Product\Http\Resources\V1\PackagingResource;
use Webkul\Product\Models\Packaging;

#[Group('Product API Management')]
#[Subgroup('Packaging', 'Manage product packaging')]
#[Authenticated]
class PackagingController extends Controller
{
    #[Endpoint('List packagings', 'Retrieve a paginated list of packagings with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> product, creator, company', required: false, example: 'product,creator')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[name]', 'string', 'Filter by packaging name (partial match)', required: false, example: 'No-example')]
    #[QueryParam('filter[product_id]', 'string', 'Comma-separated list of product IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('sort', 'string', 'Sort field', example: 'sort')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(PackagingResource::class, Packaging::class, collection: true, paginate: 10)]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index()
    {
        Gate::authorize('viewAny', Packaging::class);

        $packagings = QueryBuilder::for(Packaging::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::partial('name'),
                AllowedFilter::exact('product_id'),
            ])
            ->allowedSorts(['id', 'name', 'sort', 'created_at'])
            ->allowedIncludes([
                'product',
                'creator',
                'company',
            ])
            ->paginate();

        return PackagingResource::collection($packagings);
    }

    #[Endpoint('Create packaging', 'Create a new product packaging')]
    #[ResponseFromApiResource(PackagingResource::class, Packaging::class, status: 201, additional: ['message' => 'Packaging created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"name": ["The name field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(PackagingRequest $request)
    {
        Gate::authorize('create', Packaging::class);

        $packaging = Packaging::create($request->validated());

        return (new PackagingResource($packaging))
            ->additional(['message' => 'Packaging created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show packaging', 'Retrieve a specific packaging by its ID')]
    #[UrlParam('id', 'integer', 'The packaging ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> product, creator, company', required: false, example: 'product,creator')]
    #[ResponseFromApiResource(PackagingResource::class, Packaging::class)]
    #[Response(status: 404, description: 'Packaging not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $id)
    {
        $packaging = QueryBuilder::for(Packaging::where('id', $id))
            ->allowedIncludes([
                'product',
                'creator',
                'company',
            ])
            ->firstOrFail();

        Gate::authorize('view', $packaging);

        return new PackagingResource($packaging);
    }

    #[Endpoint('Update packaging', 'Update an existing packaging')]
    #[UrlParam('id', 'integer', 'The packaging ID', required: true, example: 1)]
    #[ResponseFromApiResource(PackagingResource::class, Packaging::class, additional: ['message' => 'Packaging updated successfully.'])]
    #[Response(status: 404, description: 'Packaging not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"name": ["The name field must be a string."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(PackagingRequest $request, string $id)
    {
        $packaging = Packaging::findOrFail($id);

        Gate::authorize('update', $packaging);

        $packaging->update($request->validated());

        return (new PackagingResource($packaging))
            ->additional(['message' => 'Packaging updated successfully.']);
    }

    #[Endpoint('Delete packaging', 'Delete a packaging')]
    #[UrlParam('id', 'integer', 'The packaging ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Packaging deleted', content: '{"message": "Packaging deleted successfully."}')]
    #[Response(status: 404, description: 'Packaging not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $id)
    {
        $packaging = Packaging::findOrFail($id);

        Gate::authorize('delete', $packaging);

        $packaging->delete();

        return response()->json([
            'message' => 'Packaging deleted successfully.',
        ]);
    }
}
