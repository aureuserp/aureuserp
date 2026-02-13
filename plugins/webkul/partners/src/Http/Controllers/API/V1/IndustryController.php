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
use Webkul\Partner\Http\Requests\IndustryRequest;
use Webkul\Partner\Http\Resources\V1\IndustryResource;
use Webkul\Partner\Models\Industry;

#[Group('Partner API Management')]
#[Subgroup('Industries', 'Manage industries')]
#[Authenticated]
class IndustryController extends Controller
{
    #[Endpoint('List industries', 'Retrieve a paginated list of industries with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> creator', required: false, example: 'creator')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[name]', 'string', 'Filter by industry name (partial match)', required: false, example: 'No-example')]
    #[QueryParam('filter[trashed]', 'string', 'Filter by trashed status. </br></br><b>Available options:</b> with, without, only', required: false, example: 'No-example')]
    #[QueryParam('sort', 'string', 'Sort field', example: 'name')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(IndustryResource::class, Industry::class, collection: true, paginate: 10)]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index()
    {
        Gate::authorize('viewAny', Industry::class);

        $industries = QueryBuilder::for(Industry::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::partial('name'),
                AllowedFilter::trashed(),
            ])
            ->allowedSorts(['id', 'name', 'created_at'])
            ->allowedIncludes([
                'creator',
            ])
            ->paginate();

        return IndustryResource::collection($industries);
    }

    #[Endpoint('Create industry', 'Create a new industry')]
    #[ResponseFromApiResource(IndustryResource::class, Industry::class, status: 201, additional: ['message' => 'Industry created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"name": ["The name field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(IndustryRequest $request)
    {
        Gate::authorize('create', Industry::class);

        $industry = Industry::create($request->validated());

        return (new IndustryResource($industry))
            ->additional(['message' => 'Industry created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show industry', 'Retrieve a specific industry by its ID')]
    #[UrlParam('id', 'integer', 'The industry ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> creator', required: false, example: 'creator')]
    #[ResponseFromApiResource(IndustryResource::class, Industry::class)]
    #[Response(status: 404, description: 'Industry not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $id)
    {
        $industry = QueryBuilder::for(Industry::where('id', $id))
            ->allowedIncludes([
                'creator',
            ])
            ->firstOrFail();

        Gate::authorize('view', $industry);

        return new IndustryResource($industry);
    }

    #[Endpoint('Update industry', 'Update an existing industry')]
    #[UrlParam('id', 'integer', 'The industry ID', required: true, example: 1)]
    #[ResponseFromApiResource(IndustryResource::class, Industry::class, additional: ['message' => 'Industry updated successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"name": ["The name field must not exceed 255 characters."]}}')]
    #[Response(status: 404, description: 'Industry not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(IndustryRequest $request, string $id)
    {
        $industry = Industry::findOrFail($id);

        Gate::authorize('update', $industry);

        $industry->update($request->validated());

        return (new IndustryResource($industry))
            ->additional(['message' => 'Industry updated successfully.']);
    }

    #[Endpoint('Delete industry', 'Soft delete an industry')]
    #[UrlParam('id', 'integer', 'The industry ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Industry deleted successfully', content: '{"message": "Industry deleted successfully."}')]
    #[Response(status: 404, description: 'Industry not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $id)
    {
        $industry = Industry::findOrFail($id);

        Gate::authorize('delete', $industry);

        $industry->delete();

        return response()->json(['message' => 'Industry deleted successfully.']);
    }

    #[Endpoint('Restore industry', 'Restore a soft-deleted industry')]
    #[UrlParam('id', 'integer', 'The industry ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Industry restored successfully', content: '{"message": "Industry restored successfully."}')]
    #[Response(status: 404, description: 'Industry not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function restore(string $id)
    {
        $industry = Industry::onlyTrashed()->findOrFail($id);

        Gate::authorize('restore', $industry);

        $industry->restore();

        return response()->json(['message' => 'Industry restored successfully.']);
    }

    #[Endpoint('Force delete industry', 'Permanently delete an industry')]
    #[UrlParam('id', 'integer', 'The industry ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Industry permanently deleted', content: '{"message": "Industry permanently deleted."}')]
    #[Response(status: 404, description: 'Industry not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function forceDestroy(string $id)
    {
        $industry = Industry::withTrashed()->findOrFail($id);

        Gate::authorize('forceDelete', $industry);

        $industry->forceDelete();

        return response()->json(['message' => 'Industry permanently deleted.']);
    }
}
