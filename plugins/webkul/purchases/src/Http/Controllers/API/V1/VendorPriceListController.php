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
use Webkul\Purchase\Http\Requests\VendorPriceListRequest;
use Webkul\Purchase\Http\Resources\V1\VendorPriceListResource;
use Webkul\Purchase\Models\ProductSupplier;

#[Group('Purchase API Management')]
#[Subgroup('Vendor Price Lists', 'Manage vendor price lists')]
#[Authenticated]
class VendorPriceListController extends Controller
{
    protected array $allowedIncludes = [
        'partner',
        'product',
        'currency',
        'company',
        'creator',
    ];

    #[Endpoint('List vendor price lists', 'Retrieve a paginated list of vendor price lists with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> partner, product, currency, company, creator', required: false, example: 'partner,product')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[partner_id]', 'string', 'Filter by vendor IDs', required: false, example: 'No-example')]
    #[QueryParam('filter[product_id]', 'string', 'Filter by product IDs', required: false, example: 'No-example')]
    #[QueryParam('filter[currency_id]', 'string', 'Filter by currency IDs', required: false, example: 'No-example')]
    #[QueryParam('filter[company_id]', 'string', 'Filter by company IDs', required: false, example: 'No-example')]
    #[QueryParam('filter[product_name]', 'string', 'Partial match on vendor product name', required: false, example: 'Bolt')]
    #[QueryParam('filter[product_code]', 'string', 'Partial match on vendor product code', required: false, example: 'M8')]
    #[QueryParam('sort', 'string', 'Sort field', example: '-created_at')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(VendorPriceListResource::class, ProductSupplier::class, collection: true, paginate: 10)]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index()
    {
        Gate::authorize('viewAny', ProductSupplier::class);

        $vendorPriceLists = QueryBuilder::for(ProductSupplier::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('partner_id'),
                AllowedFilter::exact('product_id'),
                AllowedFilter::exact('currency_id'),
                AllowedFilter::exact('company_id'),
                AllowedFilter::partial('product_name'),
                AllowedFilter::partial('product_code'),
            ])
            ->allowedSorts(['id', 'price', 'min_qty', 'delay', 'starts_at', 'ends_at', 'created_at'])
            ->allowedIncludes($this->allowedIncludes)
            ->paginate();

        return VendorPriceListResource::collection($vendorPriceLists);
    }

    #[Endpoint('Create vendor price list', 'Create a new vendor price list')]
    #[ResponseFromApiResource(VendorPriceListResource::class, ProductSupplier::class, status: 201, additional: ['message' => 'Vendor price list created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"partner_id": ["The partner id field is required."], "product_id": ["The product id field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(VendorPriceListRequest $request)
    {
        Gate::authorize('create', ProductSupplier::class);

        $vendorPriceList = ProductSupplier::create($request->validated());

        return (new VendorPriceListResource($vendorPriceList->load($this->allowedIncludes)))
            ->additional(['message' => 'Vendor price list created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show vendor price list', 'Retrieve a specific vendor price list by its ID')]
    #[UrlParam('id', 'integer', 'The vendor price list ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> partner, product, currency, company, creator', required: false, example: 'partner,product')]
    #[ResponseFromApiResource(VendorPriceListResource::class, ProductSupplier::class)]
    #[Response(status: 404, description: 'Vendor price list not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $id)
    {
        $vendorPriceList = QueryBuilder::for(ProductSupplier::where('id', $id))
            ->allowedIncludes($this->allowedIncludes)
            ->firstOrFail();

        Gate::authorize('view', $vendorPriceList);

        return new VendorPriceListResource($vendorPriceList);
    }

    #[Endpoint('Update vendor price list', 'Update an existing vendor price list')]
    #[UrlParam('id', 'integer', 'The vendor price list ID', required: true, example: 1)]
    #[ResponseFromApiResource(VendorPriceListResource::class, ProductSupplier::class, additional: ['message' => 'Vendor price list updated successfully.'])]
    #[Response(status: 404, description: 'Vendor price list not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"price": ["The price field must be numeric."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(VendorPriceListRequest $request, string $id)
    {
        $vendorPriceList = ProductSupplier::findOrFail($id);

        Gate::authorize('update', $vendorPriceList);

        $vendorPriceList->update($request->validated());

        return (new VendorPriceListResource($vendorPriceList->load($this->allowedIncludes)))
            ->additional(['message' => 'Vendor price list updated successfully.']);
    }

    #[Endpoint('Delete vendor price list', 'Delete a vendor price list')]
    #[UrlParam('id', 'integer', 'The vendor price list ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Vendor price list deleted successfully', content: '{"message": "Vendor price list deleted successfully."}')]
    #[Response(status: 404, description: 'Vendor price list not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $id)
    {
        $vendorPriceList = ProductSupplier::findOrFail($id);

        Gate::authorize('delete', $vendorPriceList);

        $vendorPriceList->delete();

        return response()->json([
            'message' => 'Vendor price list deleted successfully.',
        ]);
    }
}
