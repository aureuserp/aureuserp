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
use Webkul\Account\Enums\AmountType;
use Webkul\Account\Enums\DocumentType;
use Webkul\Account\Enums\TaxIncludeOverride;
use Webkul\Account\Enums\TaxScope;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Http\Requests\TaxRequest;
use Webkul\Account\Http\Resources\V1\TaxResource;
use Webkul\Account\Models\Tax;
use Webkul\Account\Models\TaxPartition;

#[Group('Account API Management')]
#[Subgroup('Taxes', 'Manage taxes')]
#[Authenticated]
class TaxController extends Controller
{
    #[Endpoint('List taxes', 'Retrieve a paginated list of taxes with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> company, taxGroup, cashBasisTransitionAccount, country, creator, childrenTaxes, invoiceRepartitionLines, refundRepartitionLines', required: false, example: 'invoiceRepartitionLines,refundRepartitionLines')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[name]', 'string', 'Filter by tax name (partial match)', required: false, example: 'No-example')]
    #[QueryParam('filter[company_id]', 'int', 'Filter by company ID', required: false, example: 'No-example')]
    #[QueryParam('filter[tax_group_id]', 'int', 'Filter by tax group ID', required: false, example: 'No-example')]
    #[QueryParam('filter[country_id]', 'int', 'Filter by country ID', required: false, example: 'No-example')]
    #[QueryParam('filter[type_tax_use]', 'string', 'Filter by tax type usage', enum: TypeTaxUse::class, required: false, example: 'No-example')]
    #[QueryParam('filter[amount_type]', 'string', 'Filter by amount type', enum: AmountType::class, required: false, example: 'No-example')]
    #[QueryParam('filter[tax_scope]', 'string', 'Filter by tax scope', enum: TaxScope::class, required: false, example: 'No-example')]
    #[QueryParam('filter[price_include_override]', 'string', 'Filter by price include override', enum: TaxIncludeOverride::class, required: false, example: 'No-example')]
    #[QueryParam('filter[is_active]', 'boolean', 'Filter by active status', required: false, example: 'No-example')]
    #[QueryParam('sort', 'string', 'Sort field', example: 'sort')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(TaxResource::class, Tax::class, collection: true, paginate: 10, with: ['invoiceRepartitionLines', 'refundRepartitionLines'])]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index()
    {
        Gate::authorize('viewAny', Tax::class);

        $taxes = QueryBuilder::for(Tax::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::partial('name'),
                AllowedFilter::exact('company_id'),
                AllowedFilter::exact('tax_group_id'),
                AllowedFilter::exact('country_id'),
                AllowedFilter::exact('type_tax_use'),
                AllowedFilter::exact('amount_type'),
                AllowedFilter::exact('tax_scope'),
                AllowedFilter::exact('price_include_override'),
                AllowedFilter::exact('is_active'),
            ])
            ->allowedSorts(['id', 'name', 'amount', 'sort', 'created_at'])
            ->allowedIncludes([
                'company',
                'taxGroup',
                'cashBasisTransitionAccount',
                'country',
                'creator',
                'childrenTaxes',
                'invoiceRepartitionLines',
                'refundRepartitionLines',
            ])
            ->paginate();

        return TaxResource::collection($taxes);
    }

    #[Endpoint('Create tax', 'Create a new tax')]
    #[ResponseFromApiResource(TaxResource::class, Tax::class, status: 201, additional: ['message' => 'Tax created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"name": ["The name field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(TaxRequest $request)
    {
        Gate::authorize('create', Tax::class);

        $data = $request->validated();

        $invoiceRepartitionLines = $data['invoice_repartition_lines'] ?? [];
        $refundRepartitionLines = $data['refund_repartition_lines'] ?? [];
        unset($data['invoice_repartition_lines'], $data['refund_repartition_lines']);

        $tax = DB::transaction(function () use ($data, $invoiceRepartitionLines, $refundRepartitionLines) {
            $tax = Tax::create($data);

            foreach ($invoiceRepartitionLines as $index => $line) {
                $tax->invoiceRepartitionLines()->create([
                    'repartition_type'    => $line['repartition_type'],
                    'factor_percent'      => $line['factor_percent'] ?? null,
                    'account_id'          => $line['account_id'] ?? null,
                    'use_in_tax_closing'  => $line['use_in_tax_closing'] ?? false,
                    'document_type'       => DocumentType::INVOICE,
                    'sort'                => $index,
                ]);
            }

            foreach ($refundRepartitionLines as $index => $line) {
                $tax->refundRepartitionLines()->create([
                    'repartition_type'    => $line['repartition_type'],
                    'factor_percent'      => $line['factor_percent'] ?? null,
                    'account_id'          => $line['account_id'] ?? null,
                    'use_in_tax_closing'  => $line['use_in_tax_closing'] ?? false,
                    'document_type'       => DocumentType::REFUND,
                    'sort'                => $index,
                ]);
            }

            TaxPartition::validateRepartitionLines($tax->id);

            return $tax;
        });

        $tax->load(['invoiceRepartitionLines', 'refundRepartitionLines']);

        return (new TaxResource($tax))
            ->additional(['message' => 'Tax created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show tax', 'Retrieve a specific tax by its ID')]
    #[UrlParam('id', 'integer', 'The tax ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> company, taxGroup, cashBasisTransitionAccount, country, creator, childrenTaxes, invoiceRepartitionLines, refundRepartitionLines', required: false, example: 'invoiceRepartitionLines,refundRepartitionLines')]
    #[ResponseFromApiResource(TaxResource::class, Tax::class, with: ['invoiceRepartitionLines', 'refundRepartitionLines'])]
    #[Response(status: 404, description: 'Tax not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $id)
    {
        $tax = QueryBuilder::for(Tax::where('id', $id))
            ->allowedIncludes([
                'company',
                'taxGroup',
                'cashBasisTransitionAccount',
                'country',
                'creator',
                'childrenTaxes',
                'invoiceRepartitionLines',
                'refundRepartitionLines',
            ])
            ->firstOrFail();

        Gate::authorize('view', $tax);

        return new TaxResource($tax);
    }

    #[Endpoint('Update tax', 'Update an existing tax')]
    #[UrlParam('id', 'integer', 'The tax ID', required: true, example: 1)]
    #[ResponseFromApiResource(TaxResource::class, Tax::class, additional: ['message' => 'Tax updated successfully.'])]
    #[Response(status: 404, description: 'Tax not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(TaxRequest $request, string $id)
    {
        $tax = Tax::findOrFail($id);

        Gate::authorize('update', $tax);

        $data = $request->validated();

        $invoiceRepartitionLines = $data['invoice_repartition_lines'] ?? [];
        $refundRepartitionLines = $data['refund_repartition_lines'] ?? [];
        unset($data['invoice_repartition_lines'], $data['refund_repartition_lines']);

        DB::transaction(function () use ($tax, $data, $invoiceRepartitionLines, $refundRepartitionLines) {
            $tax->update($data);

            $this->syncRepartitionLines(
                $tax->invoiceRepartitionLines()->orderBy('sort')->get(),
                $invoiceRepartitionLines,
                $tax,
                DocumentType::INVOICE
            );

            $this->syncRepartitionLines(
                $tax->refundRepartitionLines()->orderBy('sort')->get(),
                $refundRepartitionLines,
                $tax,
                DocumentType::REFUND
            );

            TaxPartition::validateRepartitionLines($tax->id);
        });

        $tax->load(['invoiceRepartitionLines', 'refundRepartitionLines']);

        return (new TaxResource($tax))
            ->additional(['message' => 'Tax updated successfully.']);
    }

    /**
     * Sync repartition lines - update existing, create new, delete removed.
     */
    protected function syncRepartitionLines($existingLines, array $newLines, Tax $tax, DocumentType $documentType): void
    {
        $relationMethod = $documentType === DocumentType::INVOICE
            ? 'invoiceRepartitionLines'
            : 'refundRepartitionLines';

        $processedIds = [];

        foreach ($newLines as $index => $lineData) {
            $attributes = [
                'repartition_type'   => $lineData['repartition_type'],
                'factor_percent'     => $lineData['factor_percent'] ?? null,
                'account_id'         => $lineData['account_id'] ?? null,
                'use_in_tax_closing' => $lineData['use_in_tax_closing'] ?? false,
                'sort'               => $index,
            ];

            if (isset($lineData['id'])) {
                $line = $existingLines->firstWhere('id', $lineData['id']);
                if ($line) {
                    $line->update($attributes);
                    $processedIds[] = $lineData['id'];
                }
            } else {
                $tax->$relationMethod()->create(array_merge($attributes, [
                    'document_type' => $documentType,
                ]));
            }
        }

        foreach ($existingLines as $line) {
            if (! in_array($line->id, $processedIds)) {
                $line->delete();
            }
        }
    }

    #[Endpoint('Delete tax', 'Delete a tax')]
    #[UrlParam('id', 'integer', 'The tax ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Tax deleted', content: '{"message": "Tax deleted successfully."}')]
    #[Response(status: 404, description: 'Tax not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $id)
    {
        $tax = Tax::findOrFail($id);

        Gate::authorize('delete', $tax);

        $tax->delete();

        return response()->json([
            'message' => 'Tax deleted successfully.',
        ]);
    }
}
