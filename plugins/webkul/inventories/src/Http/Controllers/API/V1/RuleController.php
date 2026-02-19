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
use Webkul\Inventory\Http\Requests\RuleRequest;
use Webkul\Inventory\Http\Resources\V1\RuleResource;
use Webkul\Inventory\Models\Rule;

#[Group('Inventory API Management')]
#[Subgroup('Rules', 'Manage inventory route rules')]
#[Authenticated]
class RuleController extends Controller
{
    protected array $allowedIncludes = [
        'sourceLocation',
        'destinationLocation',
        'route',
        'operationType',
        'partnerAddress',
        'warehouse',
        'propagateWarehouse',
        'company',
        'creator',
    ];

    #[Endpoint('List rules', 'Retrieve a paginated list of rules with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> sourceLocation, destinationLocation, route, operationType, partnerAddress, warehouse, propagateWarehouse, company, creator', required: false, example: 'route,operationType')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false)]
    #[QueryParam('filter[name]', 'string', 'Filter by name', required: false, example: 'Replenish')]
    #[QueryParam('filter[action]', 'string', 'Filter by action values', required: false, example: 'pull')]
    #[QueryParam('filter[source_location_id]', 'string', 'Filter by source location IDs', required: false)]
    #[QueryParam('filter[destination_location_id]', 'string', 'Filter by destination location IDs', required: false)]
    #[QueryParam('filter[route_id]', 'string', 'Filter by route IDs', required: false)]
    #[QueryParam('filter[operation_type_id]', 'string', 'Filter by operation type IDs', required: false)]
    #[QueryParam('sort', 'string', 'Sort field', required: false, example: '-created_at')]
    #[ResponseFromApiResource(RuleResource::class, Rule::class, collection: true, paginate: 10)]
    public function index()
    {
        Gate::authorize('viewAny', Rule::class);

        $rules = QueryBuilder::for(Rule::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::partial('name'),
                AllowedFilter::exact('action'),
                AllowedFilter::exact('source_location_id'),
                AllowedFilter::exact('destination_location_id'),
                AllowedFilter::exact('route_id'),
                AllowedFilter::exact('operation_type_id'),
                AllowedFilter::exact('company_id'),
            ])
            ->allowedSorts(['id', 'name', 'action', 'sort', 'route_sort', 'delay', 'created_at', 'updated_at'])
            ->allowedIncludes($this->allowedIncludes)
            ->paginate();

        return RuleResource::collection($rules);
    }

    #[Endpoint('Create rule', 'Create a new rule')]
    #[ResponseFromApiResource(RuleResource::class, Rule::class, status: 201, additional: ['message' => 'Rule created successfully.'])]
    #[Response(status: 422, description: 'Validation error')]
    public function store(RuleRequest $request)
    {
        Gate::authorize('create', Rule::class);

        $rule = Rule::create($request->validated());

        return (new RuleResource($rule->load($this->allowedIncludes)))
            ->additional(['message' => 'Rule created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show rule', 'Retrieve a specific rule by ID')]
    #[UrlParam('id', 'integer', 'The rule ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> sourceLocation, destinationLocation, route, operationType, partnerAddress, warehouse, propagateWarehouse, company, creator', required: false, example: 'route,sourceLocation')]
    #[ResponseFromApiResource(RuleResource::class, Rule::class)]
    #[Response(status: 404, description: 'Rule not found')]
    public function show(string $id)
    {
        $rule = QueryBuilder::for(Rule::where('id', $id))
            ->allowedIncludes($this->allowedIncludes)
            ->firstOrFail();

        Gate::authorize('view', $rule);

        return new RuleResource($rule);
    }

    #[Endpoint('Update rule', 'Update an existing rule')]
    #[UrlParam('id', 'integer', 'The rule ID', required: true, example: 1)]
    #[ResponseFromApiResource(RuleResource::class, Rule::class, additional: ['message' => 'Rule updated successfully.'])]
    #[Response(status: 404, description: 'Rule not found')]
    #[Response(status: 422, description: 'Validation error')]
    public function update(RuleRequest $request, string $id)
    {
        $rule = Rule::findOrFail($id);

        Gate::authorize('update', $rule);

        $rule->update($request->validated());

        return (new RuleResource($rule->load($this->allowedIncludes)))
            ->additional(['message' => 'Rule updated successfully.']);
    }

    #[Endpoint('Delete rule', 'Soft delete a rule')]
    #[UrlParam('id', 'integer', 'The rule ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Rule deleted successfully', content: '{"message":"Rule deleted successfully."}')]
    #[Response(status: 404, description: 'Rule not found')]
    public function destroy(string $id)
    {
        $rule = Rule::findOrFail($id);

        Gate::authorize('delete', $rule);

        $rule->delete();

        return response()->json([
            'message' => 'Rule deleted successfully.',
        ]);
    }

    #[Endpoint('Restore rule', 'Restore a soft deleted rule')]
    #[UrlParam('id', 'integer', 'The rule ID', required: true, example: 1)]
    #[ResponseFromApiResource(RuleResource::class, Rule::class, additional: ['message' => 'Rule restored successfully.'])]
    #[Response(status: 404, description: 'Rule not found')]
    public function restore(string $id)
    {
        $rule = Rule::withTrashed()->findOrFail($id);

        Gate::authorize('restore', $rule);

        $rule->restore();

        return (new RuleResource($rule->fresh()->load($this->allowedIncludes)))
            ->additional(['message' => 'Rule restored successfully.']);
    }

    #[Endpoint('Force delete rule', 'Permanently delete a rule')]
    #[UrlParam('id', 'integer', 'The rule ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Rule permanently deleted successfully', content: '{"message":"Rule permanently deleted successfully."}')]
    #[Response(status: 404, description: 'Rule not found')]
    public function forceDestroy(string $id)
    {
        $rule = Rule::withTrashed()->findOrFail($id);

        Gate::authorize('forceDelete', $rule);

        $rule->forceDelete();

        return response()->json([
            'message' => 'Rule permanently deleted successfully.',
        ]);
    }
}
