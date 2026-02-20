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
use Webkul\Inventory\Http\Requests\ProductRequest;
use Webkul\Inventory\Http\Resources\V1\ProductResource;
use Webkul\Inventory\Models\Product;

#[Group('InventoryAPI Management')]
#[Subgroup('Products', 'Manage inventory products')]
#[Authenticated]
class ProductController extends Controller
{
    protected array $allowedIncludes = [
        'parent',
        'variants',
        'uom',
        'uomPO',
        'category',
        'attributes',
        'attribute_values',
        'tags',
        'company',
        'creator',
        'propertyAccountIncome',
        'propertyAccountExpense',
        'routes',
        'responsible',
    ];

    #[Endpoint('List products', 'Retrieve a paginated list of products with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> parent, variants, uom, uomPO, category, attributes, attribute_values, tags, company, creator, propertyAccountIncome, propertyAccountExpense, routes, responsible', required: false, example: 'category,tags,routes')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false)]
    #[QueryParam('filter[type]', 'string', 'Filter by product type values', required: false, example: 'goods')]
    #[QueryParam('filter[enable_sales]', 'boolean', 'Filter by sales enabled', required: false, example: 'true')]
    #[QueryParam('filter[enable_purchase]', 'boolean', 'Filter by purchase enabled', required: false, example: 'true')]
    #[QueryParam('filter[category_id]', 'string', 'Filter by category IDs', required: false)]
    #[QueryParam('filter[tracking]', 'string', 'Filter by tracking values', required: false, example: 'qty')]
    #[QueryParam('filter[is_storable]', 'boolean', 'Filter by storable products', required: false, example: 'true')]
    #[QueryParam('filter[responsible_id]', 'string', 'Filter by responsible user IDs', required: false)]
    #[QueryParam('filter[trashed]', 'string', 'Filter by trashed status ("with" or "only")', required: false, example: 'with')]
    #[QueryParam('sort', 'string', 'Sort field', required: false, example: '-created_at')]
    #[ResponseFromApiResource(ProductResource::class, Product::class, collection: true, paginate: 10)]
    public function index()
    {
        Gate::authorize('viewAny', Product::class);

        $products = QueryBuilder::for(Product::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('type'),
                AllowedFilter::exact('enable_sales'),
                AllowedFilter::exact('enable_purchase'),
                AllowedFilter::exact('category_id'),
                AllowedFilter::exact('tracking'),
                AllowedFilter::exact('is_storable'),
                AllowedFilter::exact('responsible_id'),
                AllowedFilter::trashed(),
            ])
            ->allowedSorts(['id', 'name', 'price', 'cost', 'created_at', 'sort'])
            ->allowedIncludes($this->allowedIncludes)
            ->paginate();

        return ProductResource::collection($products);
    }

    #[Endpoint('Create product', 'Create a new inventory product')]
    #[ResponseFromApiResource(ProductResource::class, Product::class, status: 201, additional: ['message' => 'Product created successfully.'])]
    #[Response(status: 422, description: 'Validation error')]
    public function store(ProductRequest $request)
    {
        Gate::authorize('create', Product::class);

        $validated = $request->validated();
        $tags = $validated['tags'] ?? [];
        $routes = $validated['routes'] ?? [];
        unset($validated['tags'], $validated['routes']);

        $product = Product::create($validated);

        if (! empty($tags)) {
            $product->tags()->sync($tags);
        }

        if (! empty($routes)) {
            $product->routes()->sync($routes);
        }

        return (new ProductResource($product->load(['category', 'tags', 'routes'])))
            ->additional(['message' => 'Product created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show product', 'Retrieve a specific inventory product by ID')]
    #[UrlParam('id', 'integer', 'The product ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> parent, variants, uom, uomPO, category, attributes, attribute_values, tags, company, creator, propertyAccountIncome, propertyAccountExpense, routes, responsible', required: false, example: 'category,tags,routes')]
    #[ResponseFromApiResource(ProductResource::class, Product::class)]
    #[Response(status: 404, description: 'Product not found')]
    public function show(string $id)
    {
        $product = QueryBuilder::for(Product::where('id', $id))
            ->allowedIncludes($this->allowedIncludes)
            ->firstOrFail();

        Gate::authorize('view', $product);

        return new ProductResource($product);
    }

    #[Endpoint('Update product', 'Update an existing inventory product')]
    #[UrlParam('id', 'integer', 'The product ID', required: true, example: 1)]
    #[ResponseFromApiResource(ProductResource::class, Product::class, additional: ['message' => 'Product updated successfully.'])]
    #[Response(status: 404, description: 'Product not found')]
    #[Response(status: 422, description: 'Validation error')]
    public function update(ProductRequest $request, string $id)
    {
        $product = Product::findOrFail($id);

        Gate::authorize('update', $product);

        $validated = $request->validated();
        $tags = $validated['tags'] ?? null;
        $routes = $validated['routes'] ?? null;
        unset($validated['tags'], $validated['routes']);

        $product->update($validated);

        if ($tags !== null) {
            $product->tags()->sync($tags);
        }

        if ($routes !== null) {
            $product->routes()->sync($routes);
        }

        return (new ProductResource($product->load(['category', 'tags', 'routes'])))
            ->additional(['message' => 'Product updated successfully.']);
    }

    #[Endpoint('Delete product', 'Soft delete a product')]
    #[UrlParam('id', 'integer', 'The product ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Product deleted successfully', content: '{"message":"Product deleted successfully."}')]
    #[Response(status: 404, description: 'Product not found')]
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);

        Gate::authorize('delete', $product);

        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully.',
        ]);
    }

    #[Endpoint('Restore product', 'Restore a soft deleted product')]
    #[UrlParam('id', 'integer', 'The product ID', required: true, example: 1)]
    #[ResponseFromApiResource(ProductResource::class, Product::class, additional: ['message' => 'Product restored successfully.'])]
    #[Response(status: 404, description: 'Product not found')]
    public function restore(string $id)
    {
        $product = Product::withTrashed()->findOrFail($id);

        Gate::authorize('restore', $product);

        $product->restore();

        return (new ProductResource($product->load(['category', 'tags', 'routes'])))
            ->additional(['message' => 'Product restored successfully.']);
    }

    #[Endpoint('Force delete product', 'Permanently delete a product')]
    #[UrlParam('id', 'integer', 'The product ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Product permanently deleted successfully', content: '{"message":"Product permanently deleted successfully."}')]
    #[Response(status: 404, description: 'Product not found')]
    public function forceDestroy(string $id)
    {
        $product = Product::withTrashed()->findOrFail($id);

        Gate::authorize('forceDelete', $product);

        $product->forceDelete();

        return response()->json([
            'message' => 'Product permanently deleted successfully.',
        ]);
    }
}
