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
use Webkul\Sale\Http\Requests\TagRequest;
use Webkul\Sale\Http\Resources\V1\TagResource;
use Webkul\Sale\Models\Tag;

#[Group('Sales API Management')]
#[Subgroup('Tags', 'Manage sales tags')]
#[Authenticated]
class TagController extends Controller
{
    #[Endpoint('List tags', 'Retrieve a paginated list of sales tags with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> creator', required: false, example: 'creator')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[name]', 'string', 'Filter by tag name (partial match)', required: false, example: 'No-example')]
    #[QueryParam('sort', 'string', 'Sort field', example: 'name')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(TagResource::class, Tag::class, collection: true, paginate: 10)]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index()
    {
        Gate::authorize('viewAny', Tag::class);

        $tags = QueryBuilder::for(Tag::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::partial('name'),
            ])
            ->allowedSorts(['id', 'name', 'created_at'])
            ->allowedIncludes(['creator'])
            ->paginate();

        return TagResource::collection($tags);
    }

    #[Endpoint('Create tag', 'Create a new sales tag')]
    #[ResponseFromApiResource(TagResource::class, Tag::class, status: 201, additional: ['message' => 'Tag created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"name": ["The name field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(TagRequest $request)
    {
        Gate::authorize('create', Tag::class);

        $tag = Tag::create($request->validated());

        return (new TagResource($tag))
            ->additional(['message' => 'Tag created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show tag', 'Retrieve a specific sales tag by its ID')]
    #[UrlParam('id', 'integer', 'The tag ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> creator', required: false, example: 'creator')]
    #[ResponseFromApiResource(TagResource::class, Tag::class)]
    #[Response(status: 404, description: 'Tag not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $id)
    {
        $tag = QueryBuilder::for(Tag::where('id', $id))
            ->allowedIncludes(['creator'])
            ->firstOrFail();

        Gate::authorize('view', $tag);

        return new TagResource($tag);
    }

    #[Endpoint('Update tag', 'Update an existing sales tag')]
    #[UrlParam('id', 'integer', 'The tag ID', required: true, example: 1)]
    #[ResponseFromApiResource(TagResource::class, Tag::class, additional: ['message' => 'Tag updated successfully.'])]
    #[Response(status: 404, description: 'Tag not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"name": ["The name field must be a string."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(TagRequest $request, string $id)
    {
        $tag = Tag::findOrFail($id);

        Gate::authorize('update', $tag);

        $tag->update($request->validated());

        return (new TagResource($tag))
            ->additional(['message' => 'Tag updated successfully.']);
    }

    #[Endpoint('Delete tag', 'Delete a sales tag')]
    #[UrlParam('id', 'integer', 'The tag ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Tag deleted successfully', content: '{"message": "Tag deleted successfully."}')]
    #[Response(status: 404, description: 'Tag not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $id)
    {
        $tag = Tag::findOrFail($id);

        Gate::authorize('delete', $tag);

        $tag->delete();

        return response()->json([
            'message' => 'Tag deleted successfully.',
        ]);
    }
}
