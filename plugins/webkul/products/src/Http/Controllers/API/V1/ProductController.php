<?php

namespace Webkul\Product\Http\Controllers\API\V1;

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
use Webkul\Product\Enums\ProductType;
use Webkul\Product\Http\Requests\ProductRequest;
use Webkul\Product\Http\Resources\V1\ProductResource;
use Webkul\Product\Models\Product;

#[Group('Product API Management')]
#[Subgroup('Products', 'Manage products')]
#[Authenticated]
class ProductController extends Controller
{
    #[Endpoint('List products', 'Retrieve a paginated list of products with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> parent, variants, uom, uomPO, category, tags, attributes, attribute_values, company, creator', required: false, example: 'category,tags')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[type]', 'string', 'Filter by product type', enum: ProductType::class, required: false, example: 'No-example')]
    #[QueryParam('filter[enable_sales]', 'boolean', 'Filter by sales enabled', required: false, example: 'true')]
    #[QueryParam('filter[enable_purchase]', 'boolean', 'Filter by purchase enabled', required: false, example: 'false')]
    #[QueryParam('filter[category_id]', 'string', 'Comma-separated list of category IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[trashed]', 'string', 'Filter by trashed status: "with" (include trashed), "only" (only trashed), or any other value for non-trashed only', required: false, enum: ['with', 'only'], example: 'with')]
    #[QueryParam('sort', 'string', 'Sort field', example: '-created_at')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(ProductResource::class, Product::class, collection: true, paginate: 10, with: ['category', 'tags'])]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
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
                AllowedFilter::trashed(),
            ])
            ->allowedSorts(['id', 'name', 'price', 'cost', 'created_at', 'sort'])
            ->allowedIncludes([
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
            ])
            ->paginate();

        return ProductResource::collection($products);
    }

    #[Endpoint('Create product', 'Create a new product')]
    #[ResponseFromApiResource(ProductResource::class, Product::class, status: 201, with: ['category', 'tags'], additional: ['message' => 'Product created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"name": ["The name field is required."], "type": ["The type field is required."], "category_id": ["The category id field is required."], "price": ["The price field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(ProductRequest $request)
    {
        Gate::authorize('create', Product::class);

        $validated = $request->validated();

        $tags = $validated['tags'] ?? [];
        unset($validated['tags']);

        $product = Product::create($validated);

        if (! empty($tags)) {
            $product->tags()->sync($tags);
        }

        return (new ProductResource($product->load(['category', 'tags'])))
            ->additional(['message' => 'Product created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show product', 'Retrieve a specific product by its ID')]
    #[UrlParam('id', 'integer', 'The product ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> parent, variants, uom, uomPO, category, attributes, attribute_values, tags, company, creator', required: false, example: 'category,tags')]
    #[ResponseFromApiResource(ProductResource::class, Product::class, with: ['category', 'tags'])]
    #[Response(status: 404, description: 'Product not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $id)
    {
        $product = QueryBuilder::for(Product::where('id', $id))
            ->allowedIncludes([
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
            ])
            ->firstOrFail();

        Gate::authorize('view', $product);

        return new ProductResource($product);
    }

    #[Endpoint('Update product', 'Update an existing product')]
    #[UrlParam('id', 'integer', 'The product ID', required: true, example: 1)]
    #[ResponseFromApiResource(ProductResource::class, Product::class, with: ['category', 'tags'], additional: ['message' => 'Product updated successfully.'])]
    #[Response(status: 404, description: 'Product not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"name": ["The name field must be a string."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(ProductRequest $request, string $id)
    {
        $product = Product::findOrFail($id);

        Gate::authorize('update', $product);

        $validated = $request->validated();

        $tags = $validated['tags'] ?? null;
        unset($validated['tags']);

        $product->update($validated);

        if ($tags !== null) {
            $product->tags()->sync($tags);
        }

        return (new ProductResource($product->load(['category', 'tags'])))
            ->additional(['message' => 'Product updated successfully.']);
    }

    #[Endpoint('Delete product', 'Soft delete a product')]
    #[UrlParam('id', 'integer', 'The product ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Product deleted', content: '{"message": "Product deleted successfully."}')]
    #[Response(status: 404, description: 'Product not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);

        Gate::authorize('delete', $product);

        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully.',
        ]);
    }

    #[Endpoint('Restore product', 'Restore a soft-deleted product')]
    #[UrlParam('id', 'integer', 'The product ID', required: true, example: 1)]
    #[ResponseFromApiResource(ProductResource::class, Product::class, with: ['category', 'tags'], additional: ['message' => 'Product restored successfully.'])]
    #[Response(status: 404, description: 'Product not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function restore(string $id)
    {
        $product = Product::withTrashed()->findOrFail($id);

        Gate::authorize('restore', $product);

        $product->restore();

        return (new ProductResource($product->load(['category', 'tags'])))
            ->additional(['message' => 'Product restored successfully.']);
    }

    #[Endpoint('Force delete product', 'Permanently delete a product (cannot be restored)')]
    #[UrlParam('id', 'integer', 'The product ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Product permanently deleted', content: '{"message": "Product permanently deleted."}')]
    #[Response(status: 404, description: 'Product not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function forceDestroy(string $id)
    {
        $product = Product::withTrashed()->findOrFail($id);

        Gate::authorize('forceDelete', $product);

        $product->forceDelete();

        return response()->json([
            'message' => 'Product permanently deleted.',
        ]);
    }
}
