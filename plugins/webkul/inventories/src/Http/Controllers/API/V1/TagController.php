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
use Webkul\Inventory\Http\Requests\TagRequest;
use Webkul\Inventory\Http\Resources\V1\TagResource;
use Webkul\Inventory\Models\Tag;

#[Group('Inventory API Management')]
#[Subgroup('Tags', 'Manage inventory tags')]
#[Authenticated]
class TagController extends Controller
{
    protected array $allowedIncludes = [
        'creator',
    ];

    #[Endpoint('List tags', 'Retrieve a paginated list of tags with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> creator', required: false, example: 'creator')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false)]
    #[QueryParam('filter[name]', 'string', 'Filter by tag name', required: false, example: 'Fragile')]
    #[QueryParam('filter[color]', 'string', 'Filter by color', required: false, example: '#f97316')]
    #[QueryParam('sort', 'string', 'Sort field', required: false, example: '-created_at')]
    #[ResponseFromApiResource(TagResource::class, Tag::class, collection: true, paginate: 10)]
    public function index()
    {
        Gate::authorize('viewAny', Tag::class);

        $tags = QueryBuilder::for(Tag::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::partial('name'),
                AllowedFilter::partial('color'),
            ])
            ->allowedSorts(['id', 'name', 'color', 'sort', 'created_at', 'updated_at'])
            ->allowedIncludes($this->allowedIncludes)
            ->paginate();

        return TagResource::collection($tags);
    }

    #[Endpoint('Create tag', 'Create a new tag')]
    #[ResponseFromApiResource(TagResource::class, Tag::class, status: 201, additional: ['message' => 'Tag created successfully.'])]
    #[Response(status: 422, description: 'Validation error')]
    public function store(TagRequest $request)
    {
        Gate::authorize('create', Tag::class);

        $tag = Tag::create($request->validated());

        return (new TagResource($tag->load($this->allowedIncludes)))
            ->additional(['message' => 'Tag created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show tag', 'Retrieve a specific tag by ID')]
    #[UrlParam('id', 'integer', 'The tag ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> creator', required: false, example: 'creator')]
    #[ResponseFromApiResource(TagResource::class, Tag::class)]
    #[Response(status: 404, description: 'Tag not found')]
    public function show(string $id)
    {
        $tag = QueryBuilder::for(Tag::where('id', $id))
            ->allowedIncludes($this->allowedIncludes)
            ->firstOrFail();

        Gate::authorize('view', $tag);

        return new TagResource($tag);
    }

    #[Endpoint('Update tag', 'Update an existing tag')]
    #[UrlParam('id', 'integer', 'The tag ID', required: true, example: 1)]
    #[ResponseFromApiResource(TagResource::class, Tag::class, additional: ['message' => 'Tag updated successfully.'])]
    #[Response(status: 404, description: 'Tag not found')]
    #[Response(status: 422, description: 'Validation error')]
    public function update(TagRequest $request, string $id)
    {
        $tag = Tag::findOrFail($id);

        Gate::authorize('update', $tag);

        $tag->update($request->validated());

        return (new TagResource($tag->load($this->allowedIncludes)))
            ->additional(['message' => 'Tag updated successfully.']);
    }

    #[Endpoint('Delete tag', 'Soft delete a tag')]
    #[UrlParam('id', 'integer', 'The tag ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Tag deleted successfully', content: '{"message":"Tag deleted successfully."}')]
    #[Response(status: 404, description: 'Tag not found')]
    public function destroy(string $id)
    {
        $tag = Tag::findOrFail($id);

        Gate::authorize('delete', $tag);

        $tag->delete();

        return response()->json([
            'message' => 'Tag deleted successfully.',
        ]);
    }

    #[Endpoint('Restore tag', 'Restore a soft deleted tag')]
    #[UrlParam('id', 'integer', 'The tag ID', required: true, example: 1)]
    #[ResponseFromApiResource(TagResource::class, Tag::class, additional: ['message' => 'Tag restored successfully.'])]
    #[Response(status: 404, description: 'Tag not found')]
    public function restore(string $id)
    {
        $tag = Tag::withTrashed()->findOrFail($id);

        Gate::authorize('restore', $tag);

        $tag->restore();

        return (new TagResource($tag->fresh()->load($this->allowedIncludes)))
            ->additional(['message' => 'Tag restored successfully.']);
    }

    #[Endpoint('Force delete tag', 'Permanently delete a tag')]
    #[UrlParam('id', 'integer', 'The tag ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Tag permanently deleted successfully', content: '{"message":"Tag permanently deleted successfully."}')]
    #[Response(status: 404, description: 'Tag not found')]
    public function forceDestroy(string $id)
    {
        $tag = Tag::withTrashed()->findOrFail($id);

        Gate::authorize('forceDelete', $tag);

        $tag->forceDelete();

        return response()->json([
            'message' => 'Tag permanently deleted successfully.',
        ]);
    }
}
