<?php

namespace Webkul\Support\Http\Controllers\API\V1;

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
use Webkul\Support\Http\Requests\UOMCategoryRequest;
use Webkul\Support\Http\Resources\V1\UOMCategoryResource;
use Webkul\Support\Models\UOMCategory;

#[Group('Support API Management')]
#[Subgroup('UOM Categories', 'Manage unit of measure categories')]
#[Authenticated]
class UOMCategoryController extends Controller
{
    #[Endpoint('List UOM categories', 'Retrieve a paginated list of UOM categories with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> uoms, creator', required: false, example: 'uoms')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[name]', 'string', 'Filter by category name (partial match)', required: false, example: 'No-example')]
    #[QueryParam('sort', 'string', 'Sort field', example: 'name')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(UOMCategoryResource::class, UOMCategory::class, collection: true, paginate: 10)]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index()
    {
        Gate::authorize('viewAny', UOMCategory::class);

        $uomCategories = QueryBuilder::for(UOMCategory::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::partial('name'),
            ])
            ->allowedSorts(['id', 'name', 'created_at'])
            ->allowedIncludes([
                'uoms',
                'creator',
            ])
            ->paginate();

        return UOMCategoryResource::collection($uomCategories);
    }

    #[Endpoint('Create UOM category', 'Create a new UOM category')]
    #[ResponseFromApiResource(UOMCategoryResource::class, UOMCategory::class, status: 201, additional: ['message' => 'UOM category created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"name": ["The name field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(UOMCategoryRequest $request)
    {
        Gate::authorize('create', UOMCategory::class);

        $data = $request->validated();

        $uomCategory = UOMCategory::create($data);

        return (new UOMCategoryResource($uomCategory))
            ->additional(['message' => 'UOM category created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show UOM category', 'Retrieve a specific UOM category by its ID')]
    #[UrlParam('id', 'integer', 'The UOM category ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> uoms, creator', required: false, example: 'uoms')]
    #[ResponseFromApiResource(UOMCategoryResource::class, UOMCategory::class)]
    #[Response(status: 404, description: 'UOM category not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $id)
    {
        $uomCategory = QueryBuilder::for(UOMCategory::where('id', $id))
            ->allowedIncludes([
                'uoms',
                'creator',
            ])
            ->firstOrFail();

        Gate::authorize('view', $uomCategory);

        return new UOMCategoryResource($uomCategory);
    }

    #[Endpoint('Update UOM category', 'Update an existing UOM category')]
    #[UrlParam('id', 'integer', 'The UOM category ID', required: true, example: 1)]
    #[ResponseFromApiResource(UOMCategoryResource::class, UOMCategory::class, additional: ['message' => 'UOM category updated successfully.'])]
    #[Response(status: 404, description: 'UOM category not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(UOMCategoryRequest $request, string $id)
    {
        $uomCategory = UOMCategory::findOrFail($id);

        Gate::authorize('update', $uomCategory);

        $uomCategory->update($request->validated());

        return (new UOMCategoryResource($uomCategory))
            ->additional(['message' => 'UOM category updated successfully.']);
    }

    #[Endpoint('Delete UOM category', 'Delete a UOM category')]
    #[UrlParam('id', 'integer', 'The UOM category ID', required: true, example: 1)]
    #[Response(status: 200, description: 'UOM category deleted', content: '{"message": "UOM category deleted successfully."}')]
    #[Response(status: 404, description: 'UOM category not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $id)
    {
        $uomCategory = UOMCategory::findOrFail($id);

        Gate::authorize('delete', $uomCategory);

        $uomCategory->delete();

        return response()->json([
            'message' => 'UOM category deleted successfully.',
        ]);
    }
}
