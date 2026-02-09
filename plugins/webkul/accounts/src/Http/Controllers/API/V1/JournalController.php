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
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Http\Requests\JournalRequest;
use Webkul\Account\Http\Resources\V1\JournalResource;
use Webkul\Account\Models\Journal;

#[Group('Account API Management')]
#[Subgroup('Journals', 'Manage accounting journals')]
#[Authenticated]
class JournalController extends Controller
{
    #[Endpoint('List journals', 'Retrieve a paginated list of journals with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> company, currency, defaultAccount, suspenseAccount, profitAccount, lossAccount, bankAccount, creator, inboundPaymentMethodLines, outboundPaymentMethodLines', required: false, example: 'company,currency')]
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
                'inboundPaymentMethodLines',
                'outboundPaymentMethodLines',
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

        $inboundLines = $data['inbound_payment_method_lines'] ?? [];
        $outboundLines = $data['outbound_payment_method_lines'] ?? [];
        unset($data['inbound_payment_method_lines'], $data['outbound_payment_method_lines']);

        $journal = DB::transaction(function () use ($data, $inboundLines, $outboundLines) {
            $journal = Journal::create($data);

            foreach ($inboundLines as $index => $line) {
                $journal->inboundPaymentMethodLines()->create([
                    'payment_method_id'  => $line['payment_method_id'],
                    'name'               => $line['name'],
                    'payment_account_id' => $line['payment_account_id'] ?? null,
                    'sort'               => $index,
                ]);
            }

            foreach ($outboundLines as $index => $line) {
                $journal->outboundPaymentMethodLines()->create([
                    'payment_method_id'  => $line['payment_method_id'],
                    'name'               => $line['name'],
                    'payment_account_id' => $line['payment_account_id'] ?? null,
                    'sort'               => $index,
                ]);
            }

            return $journal;
        });

        return (new JournalResource($journal))
            ->additional(['message' => 'Journal created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show journal', 'Retrieve a specific journal by its ID')]
    #[UrlParam('id', 'integer', 'The journal ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> company, currency, defaultAccount, suspenseAccount, profitAccount, lossAccount, bankAccount, creator, inboundPaymentMethodLines, outboundPaymentMethodLines', required: false, example: 'company,currency')]
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
                'inboundPaymentMethodLines',
                'outboundPaymentMethodLines',
            ])
            ->firstOrFail();

        Gate::authorize('view', $journal);

        return new JournalResource($journal);
    }

    #[Endpoint('Update journal', 'Update an existing journal')]
    #[UrlParam('id', 'integer', 'The journal ID', required: true, example: 1)]
    #[ResponseFromApiResource(JournalResource::class, Journal::class, additional: ['message' => 'Journal updated successfully.'])]
    #[Response(status: 404, description: 'Journal not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(JournalRequest $request, string $id)
    {
        $journal = Journal::findOrFail($id);

        Gate::authorize('update', $journal);

        $data = $request->validated();

        $inboundLines = $data['inbound_payment_method_lines'] ?? null;
        $outboundLines = $data['outbound_payment_method_lines'] ?? null;
        unset($data['inbound_payment_method_lines'], $data['outbound_payment_method_lines']);

        DB::transaction(function () use ($journal, $data, $inboundLines, $outboundLines) {
            $journal->update($data);

            if ($inboundLines !== null) {
                $this->syncPaymentMethodLines($journal, $inboundLines, 'inboundPaymentMethodLines');
            }

            if ($outboundLines !== null) {
                $this->syncPaymentMethodLines($journal, $outboundLines, 'outboundPaymentMethodLines');
            }
        });

        return (new JournalResource($journal))
            ->additional(['message' => 'Journal updated successfully.']);
    }

    #[Endpoint('Delete journal', 'Delete a journal')]
    #[UrlParam('id', 'integer', 'The journal ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Journal deleted', content: '{"message": "Journal deleted successfully."}')]
    #[Response(status: 404, description: 'Journal not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $id)
    {
        $journal = Journal::findOrFail($id);

        Gate::authorize('delete', $journal);

        $journal->delete();

        return response()->json([
            'message' => 'Journal deleted successfully.',
        ]);
    }

    /**
     * Sync payment method lines (update existing, create new, delete missing)
     */
    private function syncPaymentMethodLines(Journal $journal, array $lines, string $relationMethod): void
    {
        $providedIds = [];

        foreach ($lines as $index => $lineData) {
            if (isset($lineData['id'])) {
                $journal->{$relationMethod}()->where('id', $lineData['id'])->update([
                    'payment_method_id'  => $lineData['payment_method_id'],
                    'name'               => $lineData['name'],
                    'payment_account_id' => $lineData['payment_account_id'] ?? null,
                    'sort'               => $index,
                ]);
                
                $providedIds[] = $lineData['id'];
            } else {
                $newLine = $journal->{$relationMethod}()->create([
                    'payment_method_id'  => $lineData['payment_method_id'],
                    'name'               => $lineData['name'],
                    'payment_account_id' => $lineData['payment_account_id'] ?? null,
                    'sort'               => $index,
                ]);

                $providedIds[] = $newLine->id;
            }
        }

        $journal->{$relationMethod}()->whereNotIn('id', $providedIds)->delete();
    }
}
