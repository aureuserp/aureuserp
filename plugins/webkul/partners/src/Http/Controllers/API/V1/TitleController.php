<?php

namespace Webkul\Partner\Http\Controllers\API\V1;

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
use Webkul\Partner\Http\Requests\TitleRequest;
use Webkul\Partner\Http\Resources\V1\TitleResource;
use Webkul\Partner\Models\Title;

#[Group('Partner API Management')]
#[Subgroup('Titles', 'Manage partner titles')]
#[Authenticated]
class TitleController extends Controller
{
    #[Endpoint('List titles', 'Retrieve a paginated list of titles with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> creator', required: false, example: 'creator')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[name]', 'string', 'Filter by title name (partial match)', required: false, example: 'No-example')]
    #[QueryParam('sort', 'string', 'Sort field', example: 'name')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(TitleResource::class, Title::class, collection: true, paginate: 10)]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index()
    {
        Gate::authorize('viewAny', Title::class);

        $titles = QueryBuilder::for(Title::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::partial('name'),
            ])
            ->allowedSorts(['id', 'name', 'created_at'])
            ->allowedIncludes([
                'creator',
            ])
            ->paginate();

        return TitleResource::collection($titles);
    }

    #[Endpoint('Create title', 'Create a new title')]
    #[ResponseFromApiResource(TitleResource::class, Title::class, status: 201, additional: ['message' => 'Title created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"name": ["The name field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(TitleRequest $request)
    {
        Gate::authorize('create', Title::class);

        $title = Title::create($request->validated());

        return (new TitleResource($title))
            ->additional(['message' => 'Title created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show title', 'Retrieve a specific title by its ID')]
    #[UrlParam('id', 'integer', 'The title ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> creator', required: false, example: 'creator')]
    #[ResponseFromApiResource(TitleResource::class, Title::class)]
    #[Response(status: 404, description: 'Title not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $id)
    {
        $title = QueryBuilder::for(Title::where('id', $id))
            ->allowedIncludes([
                'creator',
            ])
            ->firstOrFail();

        Gate::authorize('view', $title);

        return new TitleResource($title);
    }

    #[Endpoint('Update title', 'Update an existing title')]
    #[UrlParam('id', 'integer', 'The title ID', required: true, example: 1)]
    #[ResponseFromApiResource(TitleResource::class, Title::class, additional: ['message' => 'Title updated successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"name": ["The name field must not exceed 255 characters."]}}')]
    #[Response(status: 404, description: 'Title not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(TitleRequest $request, string $id)
    {
        $title = Title::findOrFail($id);

        Gate::authorize('update', $title);

        $title->update($request->validated());

        return (new TitleResource($title))
            ->additional(['message' => 'Title updated successfully.']);
    }

    #[Endpoint('Delete title', 'Delete a title')]
    #[UrlParam('id', 'integer', 'The title ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Title deleted successfully', content: '{"message": "Title deleted successfully."}')]
    #[Response(status: 404, description: 'Title not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $id)
    {
        $title = Title::findOrFail($id);

        Gate::authorize('delete', $title);

        $title->delete();

        return response()->json(['message' => 'Title deleted successfully.']);
    }
}
