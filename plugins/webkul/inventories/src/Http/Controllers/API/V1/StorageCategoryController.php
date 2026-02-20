<?php

namespace Webkul\Inventory\Http\Controllers\API\V1;

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
use Webkul\Inventory\Http\Requests\StorageCategoryRequest;
use Webkul\Inventory\Http\Resources\V1\StorageCategoryResource;
use Webkul\Inventory\Models\StorageCategory;

#[Group('Inventory API Management')]
#[Subgroup('Storage Categories', 'Manage storage category configurations')]
#[Authenticated]
class StorageCategoryController extends Controller
{
    protected array $allowedIncludes = [
        'company',
        'creator',
        'locations',
    ];

    #[Endpoint('List storage categories', 'Retrieve a paginated list of storage categories with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> company, creator, locations', required: false, example: 'company,locations')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false)]
    #[QueryParam('filter[name]', 'string', 'Filter by category name', required: false, example: 'Heavy')]
    #[QueryParam('filter[allow_new_products]', 'string', 'Filter by allow_new_products enum values', required: false, example: 'mixed')]
    #[QueryParam('filter[company_id]', 'string', 'Filter by company IDs', required: false)]
    #[QueryParam('sort', 'string', 'Sort field', required: false, example: '-created_at')]
    #[ResponseFromApiResource(StorageCategoryResource::class, StorageCategory::class, collection: true, paginate: 10)]
    public function index()
    {
        Gate::authorize('viewAny', StorageCategory::class);

        $storageCategories = QueryBuilder::for(StorageCategory::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::partial('name'),
                AllowedFilter::exact('allow_new_products'),
                AllowedFilter::exact('company_id'),
            ])
            ->allowedSorts(['id', 'name', 'sort', 'max_weight', 'created_at', 'updated_at'])
            ->allowedIncludes($this->allowedIncludes)
            ->paginate();

        return StorageCategoryResource::collection($storageCategories);
    }

    #[Endpoint('Create storage category', 'Create a new storage category')]
    #[ResponseFromApiResource(StorageCategoryResource::class, StorageCategory::class, status: 201, additional: ['message' => 'Storage category created successfully.'])]
    #[Response(status: 422, description: 'Validation error')]
    public function store(StorageCategoryRequest $request)
    {
        Gate::authorize('create', StorageCategory::class);

        $storageCategory = StorageCategory::create($request->validated());

        return (new StorageCategoryResource($storageCategory->load($this->allowedIncludes)))
            ->additional(['message' => 'Storage category created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show storage category', 'Retrieve a specific storage category by ID')]
    #[UrlParam('id', 'integer', 'The storage category ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> company, creator, locations', required: false, example: 'company')]
    #[ResponseFromApiResource(StorageCategoryResource::class, StorageCategory::class)]
    #[Response(status: 404, description: 'Storage category not found')]
    public function show(string $id)
    {
        $storageCategory = QueryBuilder::for(StorageCategory::where('id', $id))
            ->allowedIncludes($this->allowedIncludes)
            ->firstOrFail();

        Gate::authorize('view', $storageCategory);

        return new StorageCategoryResource($storageCategory);
    }

    #[Endpoint('Update storage category', 'Update an existing storage category')]
    #[UrlParam('id', 'integer', 'The storage category ID', required: true, example: 1)]
    #[ResponseFromApiResource(StorageCategoryResource::class, StorageCategory::class, additional: ['message' => 'Storage category updated successfully.'])]
    #[Response(status: 404, description: 'Storage category not found')]
    #[Response(status: 422, description: 'Validation error')]
    public function update(StorageCategoryRequest $request, string $id)
    {
        $storageCategory = StorageCategory::findOrFail($id);

        Gate::authorize('update', $storageCategory);

        $storageCategory->update($request->validated());

        return (new StorageCategoryResource($storageCategory->load($this->allowedIncludes)))
            ->additional(['message' => 'Storage category updated successfully.']);
    }

    #[Endpoint('Delete storage category', 'Delete a storage category')]
    #[UrlParam('id', 'integer', 'The storage category ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Storage category deleted successfully', content: '{"message":"Storage category deleted successfully."}')]
    #[Response(status: 404, description: 'Storage category not found')]
    public function destroy(string $id)
    {
        $storageCategory = StorageCategory::findOrFail($id);

        Gate::authorize('delete', $storageCategory);

        $storageCategory->delete();

        return response()->json([
            'message' => 'Storage category deleted successfully.',
        ]);
    }
}
