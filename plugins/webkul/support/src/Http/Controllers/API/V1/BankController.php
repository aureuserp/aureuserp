<?php

namespace Webkul\Support\Http\Controllers\API\V1;

use Illuminate\Support\Facades\Auth;
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
use Webkul\Support\Http\Requests\BankRequest;
use Webkul\Support\Http\Resources\V1\BankResource;
use Webkul\Support\Models\Bank;

#[Group('Support API Management')]
#[Subgroup('Banks', 'Manage banks')]
#[Authenticated]
class BankController extends Controller
{
    #[Endpoint('List banks', 'Retrieve a paginated list of banks with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> country, state, creator', required: false, example: 'country,state')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[name]', 'string', 'Filter by bank name (partial match)', required: false, example: 'No-example')]
    #[QueryParam('filter[code]', 'string', 'Filter by bank code (partial match)', required: false, example: 'No-example')]
    #[QueryParam('filter[city]', 'string', 'Filter by city (partial match)', required: false, example: 'No-example')]
    #[QueryParam('filter[country_id]', 'int', 'Filter by country ID', required: false, example: 'No-example')]
    #[QueryParam('filter[state_id]', 'int', 'Filter by state ID', required: false, example: 'No-example')]
    #[QueryParam('filter[trashed]', 'string', 'Filter by trashed status. Options: with, only', required: false, example: 'No-example')]
    #[QueryParam('sort', 'string', 'Sort field', example: 'name')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(BankResource::class, Bank::class, collection: true, paginate: 10)]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index()
    {
        $banks = QueryBuilder::for(Bank::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::partial('name'),
                AllowedFilter::partial('code'),
                AllowedFilter::partial('city'),
                AllowedFilter::exact('country_id'),
                AllowedFilter::exact('state_id'),
                AllowedFilter::trashed(),
            ])
            ->allowedSorts(['id', 'name', 'code', 'city', 'created_at'])
            ->allowedIncludes([
                'country',
                'state',
                'creator',
            ])
            ->paginate();

        return BankResource::collection($banks);
    }

    #[Endpoint('Create bank', 'Create a new bank')]
    #[ResponseFromApiResource(BankResource::class, Bank::class, status: 201, additional: ['message' => 'Bank created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"name": ["The name field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(BankRequest $request)
    {
        $data = $request->validated();
        $data['creator_id'] = Auth::id();

        $bank = Bank::create($data);

        return (new BankResource($bank))
            ->additional(['message' => 'Bank created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show bank', 'Retrieve a specific bank by its ID')]
    #[UrlParam('bank', 'integer', 'The bank ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> country, state, creator', required: false, example: 'country,state')]
    #[ResponseFromApiResource(BankResource::class, Bank::class)]
    #[Response(status: 404, description: 'Bank not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $id)
    {
        $bank = QueryBuilder::for(Bank::where('id', $id))
            ->allowedIncludes([
                'country',
                'state',
                'creator',
            ])
            ->firstOrFail();

        return new BankResource($bank);
    }

    #[Endpoint('Update bank', 'Update an existing bank')]
    #[UrlParam('bank', 'integer', 'The bank ID', required: true, example: 1)]
    #[ResponseFromApiResource(BankResource::class, Bank::class, additional: ['message' => 'Bank updated successfully.'])]
    #[Response(status: 404, description: 'Bank not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(BankRequest $request, string $id)
    {
        $bank = Bank::findOrFail($id);
        $bank->update($request->validated());

        return (new BankResource($bank))
            ->additional(['message' => 'Bank updated successfully.']);
    }

    #[Endpoint('Delete bank', 'Soft delete a bank')]
    #[UrlParam('bank', 'integer', 'The bank ID', required: true, example: 1)]
    #[Response(status: 204, description: 'Bank deleted successfully')]
    #[Response(status: 404, description: 'Bank not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $id)
    {
        $bank = Bank::findOrFail($id);
        $bank->delete();

        return response()->noContent();
    }

    #[Endpoint('Restore bank', 'Restore a soft-deleted bank')]
    #[UrlParam('bank', 'integer', 'The bank ID', required: true, example: 1)]
    #[ResponseFromApiResource(BankResource::class, Bank::class, additional: ['message' => 'Bank restored successfully.'])]
    #[Response(status: 404, description: 'Bank not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function restore(string $id)
    {
        $bank = Bank::withTrashed()->findOrFail($id);
        $bank->restore();

        return (new BankResource($bank))
            ->additional(['message' => 'Bank restored successfully.']);
    }

    #[Endpoint('Force delete bank', 'Permanently delete a bank')]
    #[UrlParam('bank', 'integer', 'The bank ID', required: true, example: 1)]
    #[Response(status: 204, description: 'Bank permanently deleted')]
    #[Response(status: 404, description: 'Bank not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function forceDestroy(string $id)
    {
        $bank = Bank::withTrashed()->findOrFail($id);
        $bank->forceDelete();

        return response()->noContent();
    }
}
