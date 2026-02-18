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
use Webkul\Partner\Http\Requests\BankAccountRequest;
use Webkul\Partner\Http\Resources\V1\BankAccountResource;
use Webkul\Partner\Models\BankAccount;
use Webkul\Partner\Models\Partner;

#[Group('Partner API Management')]
#[Subgroup('Bank Accounts', 'Manage partner bank accounts')]
#[Authenticated]
class BankAccountController extends Controller
{
    #[Endpoint('List bank accounts', 'Retrieve a paginated list of bank accounts for a specific partner with filtering and sorting')]
    #[UrlParam('partner_id', 'integer', 'The partner ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> bank, creator', required: false, example: 'bank')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[bank_id]', 'string', 'Comma-separated list of bank IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[trashed]', 'string', 'Filter by trashed status. </br></br><b>Available options:</b> with, without, only', required: false, example: 'No-example')]
    #[QueryParam('sort', 'string', 'Sort field', example: 'created_at')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(BankAccountResource::class, BankAccount::class, collection: true, paginate: 10, with: ['partner'])]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index(string $partner)
    {
        $partnerModel = Partner::findOrFail($partner);

        Gate::authorize('view', $partnerModel);

        $bankAccounts = QueryBuilder::for(BankAccount::where('partner_id', $partner))
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('bank_id'),
                AllowedFilter::trashed(),
            ])
            ->allowedSorts(['id', 'created_at'])
            ->allowedIncludes([
                'bank',
                'creator',
            ])
            ->paginate();

        return BankAccountResource::collection($bankAccounts);
    }

    #[Endpoint('Create bank account', 'Create a new bank account for a specific partner')]
    #[UrlParam('partner_id', 'integer', 'The partner ID', required: true, example: 1)]
    #[ResponseFromApiResource(BankAccountResource::class, BankAccount::class, status: 201, with: ['partner'], additional: ['message' => 'Bank account created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"account_number": ["The account number field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(BankAccountRequest $request, string $partner)
    {
        $partnerModel = Partner::findOrFail($partner);

        Gate::authorize('update', $partnerModel);

        $data = $request->validated();
        $data['partner_id'] = $partner;

        $bankAccount = BankAccount::create($data);

        return (new BankAccountResource($bankAccount->load(['partner'])))
            ->additional(['message' => 'Bank account created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show bank account', 'Retrieve a specific bank account by its ID')]
    #[UrlParam('partner_id', 'integer', 'The partner ID', required: true, example: 1)]
    #[UrlParam('id', 'integer', 'The bank account ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> bank, creator', required: false, example: 'bank')]
    #[ResponseFromApiResource(BankAccountResource::class, BankAccount::class, with: ['partner'])]
    #[Response(status: 404, description: 'Bank account not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $partner, string $bankAccount)
    {
        $partnerModel = Partner::findOrFail($partner);

        Gate::authorize('view', $partnerModel);

        $bankAccountModel = QueryBuilder::for(BankAccount::where('id', $bankAccount)->where('partner_id', $partner))
            ->allowedIncludes([
                'bank',
                'creator',
            ])
            ->firstOrFail();

        return new BankAccountResource($bankAccountModel);
    }

    #[Endpoint('Update bank account', 'Update an existing bank account')]
    #[UrlParam('partner_id', 'integer', 'The partner ID', required: true, example: 1)]
    #[UrlParam('id', 'integer', 'The bank account ID', required: true, example: 1)]
    #[ResponseFromApiResource(BankAccountResource::class, BankAccount::class, with: ['partner'], additional: ['message' => 'Bank account updated successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"account_number": ["The account number field must be a string."]}}')]
    #[Response(status: 404, description: 'Bank account not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(BankAccountRequest $request, string $partner, string $bankAccount)
    {
        $partnerModel = Partner::findOrFail($partner);

        Gate::authorize('update', $partnerModel);

        $bankAccountModel = BankAccount::where('id', $bankAccount)->where('partner_id', $partner)->firstOrFail();
        $bankAccountModel->update($request->validated());

        return (new BankAccountResource($bankAccountModel->load(['partner'])))
            ->additional(['message' => 'Bank account updated successfully.']);
    }

    #[Endpoint('Delete bank account', 'Soft delete a bank account')]
    #[UrlParam('partner_id', 'integer', 'The partner ID', required: true, example: 1)]
    #[UrlParam('id', 'integer', 'The bank account ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Bank account deleted successfully', content: '{"message": "Bank account deleted successfully."}')]
    #[Response(status: 404, description: 'Bank account not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $partner, string $bankAccount)
    {
        $partnerModel = Partner::findOrFail($partner);

        Gate::authorize('update', $partnerModel);

        $bankAccountModel = BankAccount::where('id', $bankAccount)->where('partner_id', $partner)->firstOrFail();
        $bankAccountModel->delete();

        return response()->json([
            'message' => 'Bank account deleted successfully.',
        ]);
    }

    #[Endpoint('Restore bank account', 'Restore a soft-deleted bank account')]
    #[UrlParam('partner_id', 'integer', 'The partner ID', required: true, example: 1)]
    #[UrlParam('id', 'integer', 'The bank account ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Bank account restored successfully', content: '{"message": "Bank account restored successfully."}')]
    #[Response(status: 404, description: 'Bank account not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function restore(string $partner, string $bankAccount)
    {
        $partnerModel = Partner::findOrFail($partner);

        Gate::authorize('update', $partnerModel);

        $bankAccountModel = BankAccount::onlyTrashed()
            ->where('id', $bankAccount)
            ->where('partner_id', $partner)
            ->firstOrFail();

        $bankAccountModel->restore();

        return response()->json([
            'message' => 'Bank account restored successfully.',
        ]);
    }

    #[Endpoint('Force delete bank account', 'Permanently delete a bank account')]
    #[UrlParam('partner_id', 'integer', 'The partner ID', required: true, example: 1)]
    #[UrlParam('id', 'integer', 'The bank account ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Bank account permanently deleted', content: '{"message": "Bank account permanently deleted."}')]
    #[Response(status: 404, description: 'Bank account not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function forceDestroy(string $partner, string $bankAccount)
    {
        $partnerModel = Partner::findOrFail($partner);
        
        Gate::authorize('update', $partnerModel);

        $bankAccountModel = BankAccount::withTrashed()
            ->where('id', $bankAccount)
            ->where('partner_id', $partner)
            ->firstOrFail();

        $bankAccountModel->forceDelete();

        return response()->json([
            'message' => 'Bank account permanently deleted.',
        ]);
    }
}
