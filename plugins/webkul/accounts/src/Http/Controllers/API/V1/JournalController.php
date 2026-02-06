<?php

namespace Webkul\Account\Http\Controllers\API\V1;

use Illuminate\Support\Facades\Auth;
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
use Webkul\Account\Http\Requests\JournalRequest;
use Webkul\Account\Http\Resources\V1\JournalResource;
use Webkul\Account\Models\Journal;
use Webkul\Account\Enums\JournalType;

#[Group('Account API Management')]
#[Subgroup('Journals', 'Manage accounting journals')]
#[Authenticated]
class JournalController extends Controller
{
    #[Endpoint('List journals', 'Retrieve a paginated list of journals with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> company, currency, defaultAccount, suspenseAccount, profitAccount, lossAccount, bankAccount, creator', required: false, example: 'company,currency')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[name]', 'string', 'Filter by journal name (partial match)', required: false, example: 'No-example')]
    #[QueryParam('filter[code]', 'string', 'Filter by journal code (partial match)', required: false, example: 'No-example')]
    #[QueryParam('filter[type]', 'string', 'Filter by journal type', enum: JournalType::class, required: false, example: 'No-example')]
    #[QueryParam('filter[company_id]', 'int', 'Filter by company ID', required: false, example: 'No-example')]
    #[QueryParam('filter[show_on_dashboard]', 'boolean', 'Filter by dashboard visibility', required: false, example: 'No-example')]
    #[QueryParam('sort', 'string', 'Sort field', example: 'name')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(JournalResource::class, Journal::class, collection: true, paginate: 10)]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index()
    {
        Gate::authorize('viewAny', Journal::class);

        $journals = QueryBuilder::for(Journal::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::partial('name'),
                AllowedFilter::partial('code'),
                AllowedFilter::exact('type'),
                AllowedFilter::exact('company_id'),
                AllowedFilter::exact('show_on_dashboard'),
            ])
            ->allowedSorts(['id', 'name', 'code', 'type', 'sort', 'created_at'])
            ->allowedIncludes([
                'company',
                'currency',
                'defaultAccount',
                'suspenseAccount',
                'profitAccount',
                'lossAccount',
                'bankAccount',
                'creator',
            ])
            ->paginate();

        return JournalResource::collection($journals);
    }

    #[Endpoint('Create journal', 'Create a new journal')]
    #[ResponseFromApiResource(JournalResource::class, Journal::class, status: 201, additional: ['message' => 'Journal created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"name": ["The name field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(JournalRequest $request)
    {
        Gate::authorize('create', Journal::class);

        $data = $request->validated();
        $data['creator_id'] = Auth::id();

        $journal = Journal::create($data);

        return (new JournalResource($journal))
            ->additional(['message' => 'Journal created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show journal', 'Retrieve a specific journal by its ID')]
    #[UrlParam('journal', 'integer', 'The journal ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> company, currency, defaultAccount, suspenseAccount, profitAccount, lossAccount, bankAccount, creator', required: false, example: 'company,currency')]
    #[ResponseFromApiResource(JournalResource::class, Journal::class)]
    #[Response(status: 404, description: 'Journal not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $id)
    {
        $journal = QueryBuilder::for(Journal::where('id', $id))
            ->allowedIncludes([
                'company',
                'currency',
                'defaultAccount',
                'suspenseAccount',
                'profitAccount',
                'lossAccount',
                'bankAccount',
                'creator',
            ])
            ->firstOrFail();

        Gate::authorize('view', $journal);

        return new JournalResource($journal);
    }

    #[Endpoint('Update journal', 'Update an existing journal')]
    #[UrlParam('journal', 'integer', 'The journal ID', required: true, example: 1)]
    #[ResponseFromApiResource(JournalResource::class, Journal::class, additional: ['message' => 'Journal updated successfully.'])]
    #[Response(status: 404, description: 'Journal not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(JournalRequest $request, string $id)
    {
        $journal = Journal::findOrFail($id);

        Gate::authorize('update', $journal);

        $journal->update($request->validated());

        return (new JournalResource($journal))
            ->additional(['message' => 'Journal updated successfully.']);
    }

    #[Endpoint('Delete journal', 'Delete a journal')]
    #[UrlParam('journal', 'integer', 'The journal ID', required: true, example: 1)]
    #[Response(status: 204, description: 'Journal deleted successfully')]
    #[Response(status: 404, description: 'Journal not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $id)
    {
        $journal = Journal::findOrFail($id);

        Gate::authorize('delete', $journal);

        $journal->delete();

        return response()->noContent();
    }
}
