<?php

namespace Webkul\Account\Http\Controllers\API\V1;

use Illuminate\Support\Facades\DB;
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
use Webkul\Account\Http\Requests\FiscalPositionRequest;
use Webkul\Account\Http\Resources\V1\FiscalPositionResource;
use Webkul\Account\Models\FiscalPosition;

#[Group('Account API Management')]
#[Subgroup('Fiscal Positions', 'Manage fiscal positions')]
#[Authenticated]
class FiscalPositionController extends Controller
{
    #[Endpoint('List fiscal positions', 'Retrieve a paginated list of fiscal positions with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> company, country, countryGroup, createdBy, taxes, accounts', required: false, example: 'company')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[name]', 'string', 'Filter by fiscal position name (partial match)', required: false, example: 'No-example')]
    #[QueryParam('filter[company_id]', 'int', 'Filter by company ID', required: false, example: 'No-example')]
    #[QueryParam('filter[country_id]', 'int', 'Filter by country ID', required: false, example: 'No-example')]
    #[QueryParam('filter[vat_required]', 'boolean', 'Filter by VAT required flag', required: false, example: 'No-example')]
    #[QueryParam('sort', 'string', 'Sort field', example: 'sort')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(FiscalPositionResource::class, FiscalPosition::class, collection: true, paginate: 10)]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index()
    {
        Gate::authorize('viewAny', FiscalPosition::class);

        $fiscalPositions = QueryBuilder::for(FiscalPosition::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::partial('name'),
                AllowedFilter::exact('company_id'),
                AllowedFilter::exact('country_id'),
                AllowedFilter::exact('vat_required'),
            ])
            ->allowedSorts(['id', 'name', 'sort', 'created_at'])
            ->allowedIncludes([
                'company',
                'country',
                'countryGroup',
                'createdBy',
                'taxes',
                'accounts',
            ])
            ->paginate();

        return FiscalPositionResource::collection($fiscalPositions);
    }

    #[Endpoint('Create fiscal position', 'Create a new fiscal position')]
    #[ResponseFromApiResource(FiscalPositionResource::class, FiscalPosition::class, status: 201, additional: ['message' => 'Fiscal position created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"name": ["The name field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(FiscalPositionRequest $request)
    {
        Gate::authorize('create', FiscalPosition::class);

        $data = $request->validated();

        $taxes = $data['taxes'] ?? [];
        $accounts = $data['accounts'] ?? [];
        unset($data['taxes'], $data['accounts']);

        $fiscalPosition = DB::transaction(function () use ($data, $taxes, $accounts) {
            $fiscalPosition = FiscalPosition::create($data);

            foreach ($taxes as $taxData) {
                $fiscalPosition->taxes()->create([
                    'tax_source_id'      => $taxData['tax_source_id'],
                    'tax_destination_id' => $taxData['tax_destination_id'] ?? null,
                ]);
            }

            foreach ($accounts as $accountData) {
                $fiscalPosition->accounts()->create([
                    'account_source_id'      => $accountData['account_source_id'],
                    'account_destination_id' => $accountData['account_destination_id'],
                ]);
            }

            return $fiscalPosition;
        });

        return (new FiscalPositionResource($fiscalPosition))
            ->additional(['message' => 'Fiscal position created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show fiscal position', 'Retrieve a specific fiscal position by its ID')]
    #[UrlParam('id', 'integer', 'The fiscal position ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> company, country, countryGroup, createdBy, taxes, accounts', required: false, example: 'company,taxes')]
    #[ResponseFromApiResource(FiscalPositionResource::class, FiscalPosition::class)]
    #[Response(status: 404, description: 'Fiscal position not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $id)
    {
        $fiscalPosition = QueryBuilder::for(FiscalPosition::where('id', $id))
            ->allowedIncludes([
                'company',
                'country',
                'countryGroup',
                'createdBy',
                'taxes',
                'accounts',
            ])
            ->firstOrFail();

        Gate::authorize('view', $fiscalPosition);

        return new FiscalPositionResource($fiscalPosition);
    }

    #[Endpoint('Update fiscal position', 'Update an existing fiscal position')]
    #[UrlParam('id', 'integer', 'The fiscal position ID', required: true, example: 1)]
    #[ResponseFromApiResource(FiscalPositionResource::class, FiscalPosition::class, additional: ['message' => 'Fiscal position updated successfully.'])]
    #[Response(status: 404, description: 'Fiscal position not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(FiscalPositionRequest $request, string $id)
    {
        $fiscalPosition = FiscalPosition::findOrFail($id);

        Gate::authorize('update', $fiscalPosition);

        $data = $request->validated();

        $taxes = $data['taxes'] ?? null;
        $accounts = $data['accounts'] ?? null;
        unset($data['taxes'], $data['accounts']);

        DB::transaction(function () use ($fiscalPosition, $data, $taxes, $accounts) {
            $fiscalPosition->update($data);

            if ($taxes !== null) {
                $this->syncTaxes($fiscalPosition, $taxes);
            }

            if ($accounts !== null) {
                $this->syncAccounts($fiscalPosition, $accounts);
            }
        });

        return (new FiscalPositionResource($fiscalPosition))
            ->additional(['message' => 'Fiscal position updated successfully.']);
    }

    #[Endpoint('Delete fiscal position', 'Delete a fiscal position')]
    #[UrlParam('id', 'integer', 'The fiscal position ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Fiscal position deleted', content: '{"message": "Fiscal position deleted successfully."}')]
    #[Response(status: 404, description: 'Fiscal position not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $id)
    {
        $fiscalPosition = FiscalPosition::findOrFail($id);

        Gate::authorize('delete', $fiscalPosition);

        $fiscalPosition->delete();

        return response()->json([
            'message' => 'Fiscal position deleted successfully.',
        ]);
    }

    /**
     * Sync tax mappings (update existing, create new, delete missing)
     */
    private function syncTaxes(FiscalPosition $fiscalPosition, array $taxes): void
    {
        $providedIds = [];

        foreach ($taxes as $taxData) {
            if (isset($taxData['id'])) {
                // Update existing tax mapping
                $fiscalPosition->taxes()->where('id', $taxData['id'])->update([
                    'tax_source_id'      => $taxData['tax_source_id'],
                    'tax_destination_id' => $taxData['tax_destination_id'] ?? null,
                ]);
                $providedIds[] = $taxData['id'];
            } else {
                // Create new tax mapping
                $newTax = $fiscalPosition->taxes()->create([
                    'tax_source_id'      => $taxData['tax_source_id'],
                    'tax_destination_id' => $taxData['tax_destination_id'] ?? null,
                ]);
                $providedIds[] = $newTax->id;
            }
        }

        // Delete tax mappings that were not in the payload
        $fiscalPosition->taxes()->whereNotIn('id', $providedIds)->delete();
    }

    /**
     * Sync account mappings (update existing, create new, delete missing)
     */
    private function syncAccounts(FiscalPosition $fiscalPosition, array $accounts): void
    {
        $providedIds = [];

        foreach ($accounts as $accountData) {
            if (isset($accountData['id'])) {
                $fiscalPosition->accounts()->where('id', $accountData['id'])->update([
                    'account_source_id'      => $accountData['account_source_id'],
                    'account_destination_id' => $accountData['account_destination_id'],
                ]);
                $providedIds[] = $accountData['id'];
            } else {
                $newAccount = $fiscalPosition->accounts()->create([
                    'account_source_id'      => $accountData['account_source_id'],
                    'account_destination_id' => $accountData['account_destination_id'],
                ]);
                $providedIds[] = $newAccount->id;
            }
        }

        $fiscalPosition->accounts()->whereNotIn('id', $providedIds)->delete();
    }
}
