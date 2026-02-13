<?php

namespace Webkul\Support\Http\Controllers\API\V1;

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
use Webkul\Support\Http\Requests\StateRequest;
use Webkul\Support\Http\Resources\V1\StateResource;
use Webkul\Support\Models\State;

#[Group('Support API Management')]
#[Subgroup('States', 'Manage states/provinces')]
#[Authenticated]
class StateController extends Controller
{
    #[Endpoint('List states', 'Retrieve a paginated list of states with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> country', required: false, example: 'country')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[name]', 'string', 'Filter by state name (partial match)', required: false, example: 'No-example')]
    #[QueryParam('filter[code]', 'string', 'Filter by state code (partial match)', required: false, example: 'No-example')]
    #[QueryParam('filter[country_id]', 'int', 'Filter by country ID', required: false, example: 'No-example')]
    #[QueryParam('sort', 'string', 'Sort field', example: 'name')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(StateResource::class, State::class, collection: true, paginate: 10)]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index()
    {
        $states = QueryBuilder::for(State::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::partial('name'),
                AllowedFilter::partial('code'),
                AllowedFilter::exact('country_id'),
            ])
            ->allowedSorts(['id', 'name', 'code', 'created_at'])
            ->allowedIncludes([
                'country',
            ])
            ->paginate();

        return StateResource::collection($states);
    }

    #[Endpoint('Create state', 'Create a new state')]
    #[ResponseFromApiResource(StateResource::class, State::class, status: 201, additional: ['message' => 'State created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"name": ["The name field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(StateRequest $request)
    {
        $state = State::create($request->validated());

        return (new StateResource($state))
            ->additional(['message' => 'State created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show state', 'Retrieve a specific state by its ID')]
    #[UrlParam('id', 'integer', 'The state ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> country', required: false, example: 'country')]
    #[ResponseFromApiResource(StateResource::class, State::class)]
    #[Response(status: 404, description: 'State not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $id)
    {
        $state = QueryBuilder::for(State::where('id', $id))
            ->allowedIncludes([
                'country',
            ])
            ->firstOrFail();

        return new StateResource($state);
    }

    #[Endpoint('Update state', 'Update an existing state')]
    #[UrlParam('id', 'integer', 'The state ID', required: true, example: 1)]
    #[ResponseFromApiResource(StateResource::class, State::class, additional: ['message' => 'State updated successfully.'])]
    #[Response(status: 404, description: 'State not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(StateRequest $request, string $id)
    {
        $state = State::findOrFail($id);
        $state->update($request->validated());

        return (new StateResource($state))
            ->additional(['message' => 'State updated successfully.']);
    }

    #[Endpoint('Delete state', 'Delete a state')]
    #[UrlParam('id', 'integer', 'The state ID', required: true, example: 1)]
    #[Response(status: 200, description: 'State deleted', content: '{"message": "State deleted successfully."}')]
    #[Response(status: 404, description: 'State not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $id)
    {
        $state = State::findOrFail($id);
        $state->delete();

        return response()->json([
            'message' => 'State deleted successfully.',
        ]);
    }
}
