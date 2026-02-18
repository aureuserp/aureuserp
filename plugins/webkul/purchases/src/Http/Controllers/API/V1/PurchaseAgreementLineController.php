<?php

namespace Webkul\Purchase\Http\Controllers\API\V1;

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
use Webkul\Purchase\Http\Resources\V1\RequisitionLineResource;
use Webkul\Purchase\Models\Requisition;
use Webkul\Purchase\Models\RequisitionLine;

#[Group('Purchase API Management')]
#[Subgroup('Purchase Agreement Lines', 'Manage purchase agreement line items')]
#[Authenticated]
class PurchaseAgreementLineController extends Controller
{
    protected array $allowedIncludes = [
        'requisition',
        'product',
        'uom',
        'company',
        'creator',
    ];

    #[Endpoint('List purchase agreement lines', 'Retrieve a paginated list of line items for a specific purchase agreement')]
    #[UrlParam('purchase_agreement_id', 'integer', 'The purchase agreement ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> requisition, product, uom, company, creator', required: false, example: 'product,uom')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of line IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[product_id]', 'string', 'Filter by product IDs', required: false, example: 'No-example')]
    #[QueryParam('sort', 'string', 'Sort field', example: '-id')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(RequisitionLineResource::class, RequisitionLine::class, collection: true, paginate: 10)]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index(string $purchaseAgreement)
    {
        $purchaseAgreementModel = Requisition::findOrFail($purchaseAgreement);

        Gate::authorize('view', $purchaseAgreementModel);

        $lines = QueryBuilder::for(RequisitionLine::where('requisition_id', $purchaseAgreementModel->id))
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('product_id'),
            ])
            ->allowedSorts(['id', 'qty', 'price_unit', 'created_at'])
            ->allowedIncludes($this->allowedIncludes)
            ->paginate();

        return RequisitionLineResource::collection($lines);
    }

    #[Endpoint('Show purchase agreement line', 'Retrieve a specific line item from a purchase agreement')]
    #[UrlParam('purchase_agreement_id', 'integer', 'The purchase agreement ID', required: true, example: 1)]
    #[UrlParam('id', 'integer', 'The purchase agreement line ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> requisition, product, uom, company, creator', required: false, example: 'product,uom')]
    #[ResponseFromApiResource(RequisitionLineResource::class, RequisitionLine::class)]
    #[Response(status: 404, description: 'Purchase agreement or line not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $purchaseAgreement, string $line)
    {
        $purchaseAgreementModel = Requisition::findOrFail($purchaseAgreement);

        Gate::authorize('view', $purchaseAgreementModel);

        $agreementLine = QueryBuilder::for(RequisitionLine::where('requisition_id', $purchaseAgreementModel->id)->where('id', $line))
            ->allowedIncludes($this->allowedIncludes)
            ->firstOrFail();

        return new RequisitionLineResource($agreementLine);
    }
}
