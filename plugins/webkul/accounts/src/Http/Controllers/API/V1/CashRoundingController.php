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
use Webkul\Account\Enums\RoundingMethod;
use Webkul\Account\Enums\RoundingStrategy;
use Webkul\Account\Http\Requests\CashRoundingRequest;
use Webkul\Account\Http\Resources\V1\CashRoundingResource;
use Webkul\Account\Models\CashRounding;

#[Group('Account API Management')]
#[Subgroup('Cash Roundings', 'Manage cash roundings')]
#[Authenticated]
class CashRoundingController extends Controller
{
    #[Endpoint('List cash roundings', 'Retrieve a paginated list of cash roundings with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> profitAccount, lossAccount, creator', required: false, example: 'profitAccount')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[name]', 'string', 'Filter by cash rounding name (partial match)', required: false, example: 'No-example')]
    #[QueryParam('filter[strategy]', 'string', 'Filter by rounding strategy', enum: RoundingStrategy::class, required: false, example: 'No-example')]
    #[QueryParam('filter[rounding_method]', 'string', 'Filter by rounding method', enum: RoundingMethod::class, required: false, example: 'No-example')]
    #[QueryParam('sort', 'string', 'Sort field', example: 'name')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(CashRoundingResource::class, CashRounding::class, collection: true, paginate: 10)]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index()
    {
        Gate::authorize('viewAny', CashRounding::class);

        $cashRoundings = QueryBuilder::for(CashRounding::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::partial('name'),
                AllowedFilter::exact('strategy'),
                AllowedFilter::exact('rounding_method'),
            ])
            ->allowedSorts(['id', 'name', 'rounding', 'created_at'])
            ->allowedIncludes([
                'profitAccount',
                'lossAccount',
                'creator',
            ])
            ->paginate();

        return CashRoundingResource::collection($cashRoundings);
    }

    #[Endpoint('Create cash rounding', 'Create a new cash rounding')]
    #[ResponseFromApiResource(CashRoundingResource::class, CashRounding::class, status: 201, additional: ['message' => 'Cash rounding created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"name": ["The name field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(CashRoundingRequest $request)
    {
        Gate::authorize('create', CashRounding::class);

        $data = $request->validated();

        $cashRounding = CashRounding::create($data);

        return (new CashRoundingResource($cashRounding))
            ->additional(['message' => 'Cash rounding created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show cash rounding', 'Retrieve a specific cash rounding by its ID')]
    #[UrlParam('id', 'integer', 'The cash rounding ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> profitAccount, lossAccount, creator', required: false, example: 'profitAccount,lossAccount')]
    #[ResponseFromApiResource(CashRoundingResource::class, CashRounding::class)]
    #[Response(status: 404, description: 'Cash rounding not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $id)
    {
        $cashRounding = QueryBuilder::for(CashRounding::where('id', $id))
            ->allowedIncludes([
                'profitAccount',
                'lossAccount',
                'creator',
            ])
            ->firstOrFail();

        Gate::authorize('view', $cashRounding);

        return new CashRoundingResource($cashRounding);
    }

    #[Endpoint('Update cash rounding', 'Update an existing cash rounding')]
    #[UrlParam('id', 'integer', 'The cash rounding ID', required: true, example: 1)]
    #[ResponseFromApiResource(CashRoundingResource::class, CashRounding::class, additional: ['message' => 'Cash rounding updated successfully.'])]
    #[Response(status: 404, description: 'Cash rounding not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(CashRoundingRequest $request, string $id)
    {
        $cashRounding = CashRounding::findOrFail($id);

        Gate::authorize('update', $cashRounding);

        $cashRounding->update($request->validated());

        return (new CashRoundingResource($cashRounding))
            ->additional(['message' => 'Cash rounding updated successfully.']);
    }

    #[Endpoint('Delete cash rounding', 'Delete a cash rounding')]
    #[UrlParam('id', 'integer', 'The cash rounding ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Cash rounding deleted', content: '{"message": "Cash rounding deleted successfully."}')]
    #[Response(status: 404, description: 'Cash rounding not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $id)
    {
        $cashRounding = CashRounding::findOrFail($id);

        Gate::authorize('delete', $cashRounding);

        $cashRounding->delete();

        return response()->json([
            'message' => 'Cash rounding deleted successfully.',
        ]);
    }
}
