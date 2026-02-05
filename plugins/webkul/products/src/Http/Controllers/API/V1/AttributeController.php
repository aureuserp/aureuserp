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
use Webkul\Product\Enums\AttributeType;
use Webkul\Product\Http\Requests\AttributeRequest;
use Webkul\Product\Http\Resources\V1\AttributeResource;
use Webkul\Product\Models\Attribute;

#[Group('Product API Management')]
#[Subgroup('Attributes', 'Manage product attributes')]
#[Authenticated]
class AttributeController extends Controller
{
    #[Endpoint('List attributes', 'Retrieve a paginated list of attributes with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> options, creator', required: false, example: 'options')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[name]', 'string', 'Filter by attribute name (partial match)', required: false, example: 'No-example')]
    #[QueryParam('filter[type]', 'string', 'Filter by attribute type', enum: AttributeType::class, required: false, example: 'No-example')]
    #[QueryParam('filter[trashed]', 'string', 'Filter by trashed status: "with" (include trashed), "only" (only trashed), or any other value for non-trashed only', required: false, example: 'with')]
    #[QueryParam('sort', 'string', 'Sort field', example: 'sort')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(AttributeResource::class, Attribute::class, collection: true, paginate: 10, with: ['options'])]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index()
    {
        Gate::authorize('viewAny', Attribute::class);

        $attributes = QueryBuilder::for(Attribute::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::partial('name'),
                AllowedFilter::exact('type'),
                AllowedFilter::trashed(),
            ])
            ->allowedSorts(['id', 'name', 'type', 'sort', 'created_at'])
            ->allowedIncludes([
                'options',
                'creator',
            ])
            ->paginate();

        return AttributeResource::collection($attributes);
    }

    #[Endpoint('Create attribute', 'Create a new product attribute')]
    #[ResponseFromApiResource(AttributeResource::class, Attribute::class, status: 201, with: ['options'], additional: ['message' => 'Attribute created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"name": ["The name field is required."], "type": ["The type field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(AttributeRequest $request)
    {
        Gate::authorize('create', Attribute::class);

        $attribute = Attribute::create($request->validated());

        return (new AttributeResource($attribute->load(['options'])))
            ->additional(['message' => 'Attribute created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show attribute', 'Retrieve a specific attribute by its ID')]
    #[UrlParam('id', 'integer', 'The attribute ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> options, creator', required: false, example: 'options')]
    #[ResponseFromApiResource(AttributeResource::class, Attribute::class, with: ['options'])]
    #[Response(status: 404, description: 'Attribute not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $id)
    {
        $attribute = QueryBuilder::for(Attribute::where('id', $id))
            ->allowedIncludes([
                'options',
                'creator',
            ])
            ->firstOrFail();

        Gate::authorize('view', $attribute);

        return new AttributeResource($attribute);
    }

    #[Endpoint('Update attribute', 'Update an existing attribute')]
    #[UrlParam('id', 'integer', 'The attribute ID', required: true, example: 1)]
    #[ResponseFromApiResource(AttributeResource::class, Attribute::class, with: ['options'], additional: ['message' => 'Attribute updated successfully.'])]
    #[Response(status: 404, description: 'Attribute not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"name": ["The name field must be a string."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(AttributeRequest $request, string $id)
    {
        $attribute = Attribute::findOrFail($id);

        Gate::authorize('update', $attribute);

        $attribute->update($request->validated());

        return (new AttributeResource($attribute->load(['options'])))
            ->additional(['message' => 'Attribute updated successfully.']);
    }

    #[Endpoint('Delete attribute', 'Soft delete an attribute')]
    #[UrlParam('id', 'integer', 'The attribute ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Attribute deleted', content: '{"message": "Attribute deleted successfully."}')]
    #[Response(status: 404, description: 'Attribute not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $id)
    {
        $attribute = Attribute::findOrFail($id);

        Gate::authorize('delete', $attribute);

        $attribute->delete();

        return response()->json([
            'message' => 'Attribute deleted successfully.',
        ]);
    }

    #[Endpoint('Restore attribute', 'Restore a soft-deleted attribute')]
    #[UrlParam('id', 'integer', 'The attribute ID', required: true, example: 1)]
    #[ResponseFromApiResource(AttributeResource::class, Attribute::class, with: ['options'], additional: ['message' => 'Attribute restored successfully.'])]
    #[Response(status: 404, description: 'Attribute not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function restore(string $id)
    {
        $attribute = Attribute::withTrashed()->findOrFail($id);

        Gate::authorize('restore', $attribute);

        $attribute->restore();

        return (new AttributeResource($attribute->load(['options'])))
            ->additional(['message' => 'Attribute restored successfully.']);
    }

    #[Endpoint('Force delete attribute', 'Permanently delete an attribute (cannot be restored)')]
    #[UrlParam('id', 'integer', 'The attribute ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Attribute permanently deleted', content: '{"message": "Attribute permanently deleted."}')]
    #[Response(status: 404, description: 'Attribute not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function forceDestroy(string $id)
    {
        $attribute = Attribute::withTrashed()->findOrFail($id);

        Gate::authorize('forceDelete', $attribute);

        $attribute->forceDelete();

        return response()->json([
            'message' => 'Attribute permanently deleted.',
        ]);
    }
}
