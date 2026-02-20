<?php

namespace Webkul\Project\Http\Controllers\API\V1;

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
use Webkul\Project\Http\Requests\TagRequest;
use Webkul\Project\Http\Resources\V1\TagResource;
use Webkul\Project\Models\Tag;

#[Group('Project API Management')]
#[Subgroup('Tags', 'Manage project and task tags')]
#[Authenticated]
class TagController extends Controller
{
    protected array $allowedIncludes = [
        'creator',
    ];

    #[Endpoint('List tags', 'Retrieve a paginated list of tags')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> creator', required: false, example: 'creator')]
    #[QueryParam('filter[trashed]', 'string', 'Filter by trashed status. </br></br><b>Available options:</b> with, only', required: false, example: 'with')]
    #[ResponseFromApiResource(TagResource::class, Tag::class, collection: true, paginate: 10)]
    public function index()
    {
        Gate::authorize('viewAny', Tag::class);

        $tags = QueryBuilder::for(Tag::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('name'),
                AllowedFilter::exact('color'),
                AllowedFilter::exact('creator_id'),
                AllowedFilter::trashed(),
            ])
            ->allowedSorts(['id', 'name', 'color', 'created_at', 'updated_at'])
            ->allowedIncludes($this->allowedIncludes)
            ->paginate();

        return TagResource::collection($tags);
    }

    #[Endpoint('Create tag', 'Create a new tag')]
    #[ResponseFromApiResource(TagResource::class, Tag::class, status: 201, additional: ['message' => 'Tag created successfully.'])]
    public function store(TagRequest $request)
    {
        Gate::authorize('create', Tag::class);

        $data = $request->validated();
        $data['color'] = $data['color'] ?? '#808080';

        $tag = Tag::create($data);

        return (new TagResource($tag->load(['creator'])))
            ->additional(['message' => 'Tag created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show tag', 'Retrieve a specific tag by ID')]
    #[UrlParam('id', 'integer', 'The tag ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> creator', required: false, example: 'creator')]
    #[ResponseFromApiResource(TagResource::class, Tag::class)]
    #[Response(status: 404, description: 'Tag not found', content: '{"message":"Resource not found."}')]
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
    public function update(TagRequest $request, string $id)
    {
        $tag = Tag::findOrFail($id);

        Gate::authorize('update', $tag);

        $data = $request->validated();

        if (array_key_exists('color', $data) && empty($data['color'])) {
            $data['color'] = '#808080';
        }

        $tag->update($data);

        return (new TagResource($tag->load(['creator'])))
            ->additional(['message' => 'Tag updated successfully.']);
    }

    #[Endpoint('Delete tag', 'Soft delete a tag')]
    #[UrlParam('id', 'integer', 'The tag ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Tag deleted successfully', content: '{"message":"Tag deleted successfully."}')]
    public function destroy(string $id)
    {
        $tag = Tag::findOrFail($id);

        Gate::authorize('delete', $tag);

        $tag->delete();

        return response()->json([
            'message' => 'Tag deleted successfully.',
        ]);
    }

    #[Endpoint('Restore tag', 'Restore a soft-deleted tag')]
    #[UrlParam('id', 'integer', 'The tag ID', required: true, example: 1)]
    #[ResponseFromApiResource(TagResource::class, Tag::class, additional: ['message' => 'Tag restored successfully.'])]
    public function restore(string $id)
    {
        $tag = Tag::withTrashed()->findOrFail($id);

        Gate::authorize('restore', $tag);

        $tag->restore();

        return (new TagResource($tag->load(['creator'])))
            ->additional(['message' => 'Tag restored successfully.']);
    }

    #[Endpoint('Force delete tag', 'Permanently delete a tag')]
    #[UrlParam('id', 'integer', 'The tag ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Tag permanently deleted', content: '{"message":"Tag permanently deleted."}')]
    public function forceDestroy(string $id)
    {
        $tag = Tag::withTrashed()->findOrFail($id);

        Gate::authorize('forceDelete', $tag);

        $tag->forceDelete();

        return response()->json([
            'message' => 'Tag permanently deleted.',
        ]);
    }
}
