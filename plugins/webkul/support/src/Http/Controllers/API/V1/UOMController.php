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
use Webkul\Support\Http\Requests\UOMRequest;
use Webkul\Support\Http\Resources\V1\UOMResource;
use Webkul\Support\Models\UOM;
use Webkul\Support\Models\UOMCategory;

#[Group('Support API Management')]
#[Subgroup('Units of Measure', 'Manage units of measure within a category')]
#[Authenticated]
class UOMController extends Controller
{
    #[Endpoint('List UOMs', 'Retrieve a paginated list of UOMs for a specific category with filtering and sorting')]
    #[UrlParam('uom_category_id', 'integer', 'The UOM category ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> category, creator', required: false, example: 'category')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[type]', 'string', 'Filter by UOM type (reference, bigger, smaller)', required: false, example: 'No-example')]
    #[QueryParam('filter[name]', 'string', 'Filter by name (partial match)', required: false, example: 'No-example')]
    #[QueryParam('filter[trashed]', 'string', 'Filter by trashed status. </br></br><b>Available options:</b> with, without, only', required: false, example: 'No-example')]
    #[QueryParam('sort', 'string', 'Sort field', example: 'name')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(UOMResource::class, UOM::class, collection: true, paginate: 10, with: ['category'])]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index(string $uomCategory)
    {
        $uomCategoryModel = UOMCategory::findOrFail($uomCategory);

        Gate::authorize('view', $uomCategoryModel);

        $uoms = QueryBuilder::for(UOM::where('category_id', $uomCategory))
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('type'),
                AllowedFilter::partial('name'),
                AllowedFilter::trashed(),
            ])
            ->allowedSorts(['id', 'name', 'type', 'factor', 'created_at'])
            ->allowedIncludes([
                'category',
                'creator',
            ])
            ->paginate();

        return UOMResource::collection($uoms);
    }

    #[Endpoint('Create UOM', 'Create a new UOM for a specific category')]
    #[UrlParam('uom_category_id', 'integer', 'The UOM category ID', required: true, example: 1)]
    #[ResponseFromApiResource(UOMResource::class, UOM::class, status: 201, with: ['category'], additional: ['message' => 'UOM created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"name": ["The name field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(UOMRequest $request, string $uomCategory)
    {
        $uomCategoryModel = UOMCategory::findOrFail($uomCategory);

        Gate::authorize('update', $uomCategoryModel);

        $data = $request->validated();
        $data['category_id'] = $uomCategory;

        $uom = UOM::create($data);

        return (new UOMResource($uom->load(['category'])))
            ->additional(['message' => 'UOM created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show UOM', 'Retrieve a specific UOM by its ID')]
    #[UrlParam('uom_category_id', 'integer', 'The UOM category ID', required: true, example: 1)]
    #[UrlParam('id', 'integer', 'The UOM ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> category, creator', required: false, example: 'category')]
    #[ResponseFromApiResource(UOMResource::class, UOM::class, with: ['category'])]
    #[Response(status: 404, description: 'UOM not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $uomCategory, string $uom)
    {
        $uomCategoryModel = UOMCategory::findOrFail($uomCategory);

        Gate::authorize('view', $uomCategoryModel);

        $uomModel = QueryBuilder::for(UOM::where('id', $uom)->where('category_id', $uomCategory))
            ->allowedIncludes([
                'category',
                'creator',
            ])
            ->firstOrFail();

        return new UOMResource($uomModel);
    }

    #[Endpoint('Update UOM', 'Update an existing UOM')]
    #[UrlParam('uom_category_id', 'integer', 'The UOM category ID', required: true, example: 1)]
    #[UrlParam('id', 'integer', 'The UOM ID', required: true, example: 1)]
    #[ResponseFromApiResource(UOMResource::class, UOM::class, with: ['category'], additional: ['message' => 'UOM updated successfully.'])]
    #[Response(status: 404, description: 'UOM not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(UOMRequest $request, string $uomCategory, string $uom)
    {
        $uomCategoryModel = UOMCategory::findOrFail($uomCategory);

        Gate::authorize('update', $uomCategoryModel);

        $uomModel = UOM::where('id', $uom)->where('category_id', $uomCategory)->firstOrFail();

        $uomModel->update($request->validated());

        return (new UOMResource($uomModel->load(['category'])))
            ->additional(['message' => 'UOM updated successfully.']);
    }

    #[Endpoint('Delete UOM', 'Soft delete a UOM')]
    #[UrlParam('uom_category_id', 'integer', 'The UOM category ID', required: true, example: 1)]
    #[UrlParam('id', 'integer', 'The UOM ID', required: true, example: 1)]
    #[Response(status: 200, description: 'UOM deleted', content: '{"message": "UOM deleted successfully."}')]
    #[Response(status: 404, description: 'UOM not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $uomCategory, string $uom)
    {
        $uomCategoryModel = UOMCategory::findOrFail($uomCategory);

        Gate::authorize('update', $uomCategoryModel);

        $uomModel = UOM::where('id', $uom)->where('category_id', $uomCategory)->firstOrFail();

        $uomModel->delete();

        return response()->json([
            'message' => 'UOM deleted successfully.',
        ]);
    }

    #[Endpoint('Restore UOM', 'Restore a soft-deleted UOM')]
    #[UrlParam('uom_category_id', 'integer', 'The UOM category ID', required: true, example: 1)]
    #[UrlParam('id', 'integer', 'The UOM ID', required: true, example: 1)]
    #[ResponseFromApiResource(UOMResource::class, UOM::class, with: ['category'], additional: ['message' => 'UOM restored successfully.'])]
    #[Response(status: 404, description: 'UOM not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function restore(string $uomCategory, string $uom)
    {
        $uomCategoryModel = UOMCategory::findOrFail($uomCategory);

        Gate::authorize('update', $uomCategoryModel);

        $uomModel = UOM::withTrashed()->where('id', $uom)->where('category_id', $uomCategory)->firstOrFail();

        $uomModel->restore();

        return (new UOMResource($uomModel->load(['category'])))
            ->additional(['message' => 'UOM restored successfully.']);
    }

    #[Endpoint('Force delete UOM', 'Permanently delete a UOM')]
    #[UrlParam('uom_category_id', 'integer', 'The UOM category ID', required: true, example: 1)]
    #[UrlParam('id', 'integer', 'The UOM ID', required: true, example: 1)]
    #[Response(status: 200, description: 'UOM permanently deleted', content: '{"message": "UOM permanently deleted."}')]
    #[Response(status: 404, description: 'UOM not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function forceDestroy(string $uomCategory, string $uom)
    {
        $uomCategoryModel = UOMCategory::findOrFail($uomCategory);

        Gate::authorize('update', $uomCategoryModel);

        $uomModel = UOM::withTrashed()->where('id', $uom)->where('category_id', $uomCategory)->firstOrFail();

        $uomModel->forceDelete();

        return response()->json([
            'message' => 'UOM permanently deleted.',
        ]);
    }
}
