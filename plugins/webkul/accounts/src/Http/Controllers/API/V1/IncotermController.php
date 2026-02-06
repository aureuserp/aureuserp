<?php

namespace Webkul\Account\Http\Controllers\API\V1;

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
use Webkul\Account\Http\Requests\IncotermRequest;
use Webkul\Account\Http\Resources\V1\IncotermResource;
use Webkul\Account\Models\Incoterm;

#[Group('Account API Management')]
#[Subgroup('Incoterms', 'Manage international commercial terms')]
#[Authenticated]
class IncotermController extends Controller
{
    #[Endpoint('List incoterms', 'Retrieve a paginated list of incoterms with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> creator', required: false, example: 'creator')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[code]', 'string', 'Filter by incoterm code (partial match)', required: false, example: 'No-example')]
    #[QueryParam('filter[name]', 'string', 'Filter by incoterm name (partial match)', required: false, example: 'No-example')]
    #[QueryParam('filter[trashed]', 'string', 'Filter by trashed status. Options: with, only', required: false, example: 'No-example')]
    #[QueryParam('sort', 'string', 'Sort field', example: 'code')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(IncotermResource::class, Incoterm::class, collection: true, paginate: 10)]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index()
    {
        Gate::authorize('viewAny', Incoterm::class);

        $incoterms = QueryBuilder::for(Incoterm::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::partial('code'),
                AllowedFilter::partial('name'),
                AllowedFilter::trashed(),
            ])
            ->allowedSorts(['id', 'code', 'name', 'created_at'])
            ->allowedIncludes([
                'creator',
            ])
            ->paginate();

        return IncotermResource::collection($incoterms);
    }

    #[Endpoint('Create incoterm', 'Create a new incoterm')]
    #[ResponseFromApiResource(IncotermResource::class, Incoterm::class, status: 201, additional: ['message' => 'Incoterm created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"code": ["The code field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(IncotermRequest $request)
    {
        Gate::authorize('create', Incoterm::class);

        $data = $request->validated();

        $incoterm = Incoterm::create($data);

        return (new IncotermResource($incoterm))
            ->additional(['message' => 'Incoterm created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show incoterm', 'Retrieve a specific incoterm by its ID')]
    #[UrlParam('id', 'integer', 'The incoterm ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> creator', required: false, example: 'creator')]
    #[ResponseFromApiResource(IncotermResource::class, Incoterm::class)]
    #[Response(status: 404, description: 'Incoterm not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $id)
    {
        $incoterm = QueryBuilder::for(Incoterm::where('id', $id))
            ->allowedIncludes([
                'creator',
            ])
            ->firstOrFail();

        Gate::authorize('view', $incoterm);

        return new IncotermResource($incoterm);
    }

    #[Endpoint('Update incoterm', 'Update an existing incoterm')]
    #[UrlParam('id', 'integer', 'The incoterm ID', required: true, example: 1)]
    #[ResponseFromApiResource(IncotermResource::class, Incoterm::class, additional: ['message' => 'Incoterm updated successfully.'])]
    #[Response(status: 404, description: 'Incoterm not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(IncotermRequest $request, string $id)
    {
        $incoterm = Incoterm::findOrFail($id);

        Gate::authorize('update', $incoterm);

        $incoterm->update($request->validated());

        return (new IncotermResource($incoterm))
            ->additional(['message' => 'Incoterm updated successfully.']);
    }

    #[Endpoint('Delete incoterm', 'Soft delete an incoterm')]
    #[UrlParam('id', 'integer', 'The incoterm ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Incoterm deleted', content: '{"message": "Incoterm deleted successfully."}')]
    #[Response(status: 404, description: 'Incoterm not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $id)
    {
        $incoterm = Incoterm::findOrFail($id);

        Gate::authorize('delete', $incoterm);

        $incoterm->delete();

        return response()->json([
            'message' => 'Incoterm deleted successfully.',
        ]);
    }

    #[Endpoint('Restore incoterm', 'Restore a soft-deleted incoterm')]
    #[UrlParam('id', 'integer', 'The incoterm ID', required: true, example: 1)]
    #[ResponseFromApiResource(IncotermResource::class, Incoterm::class, additional: ['message' => 'Incoterm restored successfully.'])]
    #[Response(status: 404, description: 'Incoterm not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function restore(string $id)
    {
        $incoterm = Incoterm::withTrashed()->findOrFail($id);

        Gate::authorize('restore', $incoterm);

        $incoterm->restore();

        return (new IncotermResource($incoterm))
            ->additional(['message' => 'Incoterm restored successfully.']);
    }

    #[Endpoint('Force delete incoterm', 'Permanently delete an incoterm')]
    #[UrlParam('id', 'integer', 'The incoterm ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Incoterm permanently deleted', content: '{"message": "Incoterm permanently deleted."}')]
    #[Response(status: 404, description: 'Incoterm not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function forceDestroy(string $id)
    {
        $incoterm = Incoterm::withTrashed()->findOrFail($id);

        Gate::authorize('forceDelete', $incoterm);

        $incoterm->forceDelete();

        return response()->json([
            'message' => 'Incoterm permanently deleted.',
        ]);
    }
}
