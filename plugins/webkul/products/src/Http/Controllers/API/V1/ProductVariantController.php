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
#[Subgroup('Product Variants', 'Manage product variants')]
#[Authenticated]
class ProductVariantController extends Controller
{
    #[Endpoint('List product variants', 'Retrieve a paginated list of variants for a specific product')]
    #[UrlParam('product_id', 'integer', 'The product ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> parent, uom, uomPO, category, tags, attributes, attribute_values, company, creator', required: false, example: 'attributes')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[type]', 'string', 'Filter by product type', enum: ProductType::class, required: false, example: 'No-example')]
    #[QueryParam('filter[enable_sales]', 'boolean', 'Filter by sales enabled', required: false, example: 'true')]
    #[QueryParam('filter[enable_purchase]', 'boolean', 'Filter by purchase enabled', required: false, example: 'false')]
    #[QueryParam('filter[category_id]', 'string', 'Comma-separated list of category IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[trashed]', 'string', 'Filter by trashed status: "with" (include trashed), "only" (only trashed), or any other value for non-trashed only', required: false, enum: ['with', 'only'], example: 'with')]
    #[QueryParam('sort', 'string', 'Sort field', example: '-created_at')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(ProductResource::class, Product::class, collection: true, paginate: 10, with: ['attributes'])]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index(string $product)
    {
        Gate::authorize('viewAny', Product::class);

        $variants = QueryBuilder::for(Product::where('parent_id', $product))
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
                'uom',
                'uomPO',
                'category',
                'tags',
                'attributes',
                'attribute_values',
                'company',
                'creator',
            ])
            ->paginate();

        return ProductResource::collection($variants);
    }

    #[Endpoint('Sync product variants', 'Sync variants for a specific product based on attributes')]
    #[UrlParam('product_id', 'integer', 'The product ID', required: true, example: 1)]
    #[ResponseFromApiResource(ProductResource::class, Product::class, collection: true, status: 201, with: ['attributes'], additional: ['message' => 'Product variants synced successfully.'])]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(string $product)
    {
        Gate::authorize('create', Product::class);

        $product = Product::findOrFail($product);

        $product->generateVariants();

        $product->fresh();

        return ProductResource::collection($product->variants)->additional(['message' => 'Product variants synced successfully.']);
    }

    #[Endpoint('Show product variant', 'Retrieve a specific product variant by its ID')]
    #[UrlParam('product_id', 'integer', 'The product ID', required: true, example: 1)]
    #[UrlParam('id', 'integer', 'The variant ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> parent, uom, uomPO, category, tags, attributes, attribute_values, company, creator', required: false, example: 'attributes')]
    #[ResponseFromApiResource(ProductResource::class, Product::class, with: ['attributes'])]
    #[Response(status: 404, description: 'Product variant not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $product, string $variant)
    {
        $variantModel = QueryBuilder::for(Product::where('id', $variant)->where('parent_id', $product))
            ->allowedIncludes([
                'parent',
                'uom',
                'uomPO',
                'category',
                'tags',
                'attributes',
                'attribute_values',
                'company',
                'creator',
            ])
            ->firstOrFail();

        Gate::authorize('view', $variantModel);

        return new ProductResource($variantModel);
    }

    #[Endpoint('Update product variant', 'Update an existing product variant')]
    #[UrlParam('product_id', 'integer', 'The product ID', required: true, example: 1)]
    #[UrlParam('id', 'integer', 'The variant ID', required: true, example: 1)]
    #[ResponseFromApiResource(ProductResource::class, Product::class, with: ['attributes'], additional: ['message' => 'Product variant updated successfully.'])]
    #[Response(status: 404, description: 'Product variant not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"name": ["The name field must be a string."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(ProductRequest $request, string $product, string $variant)
    {
        $variantModel = Product::where('id', $variant)->where('parent_id', $product)->firstOrFail();

        Gate::authorize('update', $variantModel);

        $validated = $request->validated();

        $tags = $validated['tags'] ?? null;
        unset($validated['tags']);

        $variantModel->update($validated);

        if ($tags !== null) {
            $variantModel->tags()->sync($tags);
        }

        return (new ProductResource($variantModel->load(['attributes', 'tags'])))
            ->additional(['message' => 'Product variant updated successfully.']);
    }

    #[Endpoint('Delete product variant', 'Soft delete a product variant')]
    #[UrlParam('product_id', 'integer', 'The product ID', required: true, example: 1)]
    #[UrlParam('id', 'integer', 'The variant ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Product variant deleted', content: '{"message": "Product variant deleted successfully."}')]
    #[Response(status: 404, description: 'Product variant not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $product, string $variant)
    {
        $variantModel = Product::where('id', $variant)->where('parent_id', $product)->firstOrFail();

        Gate::authorize('delete', $variantModel);

        $variantModel->delete();

        return response()->json([
            'message' => 'Product variant deleted successfully.',
        ]);
    }

    #[Endpoint('Restore product variant', 'Restore a soft-deleted product variant')]
    #[UrlParam('product_id', 'integer', 'The product ID', required: true, example: 1)]
    #[UrlParam('id', 'integer', 'The variant ID', required: true, example: 1)]
    #[ResponseFromApiResource(ProductResource::class, Product::class, with: ['attributes'], additional: ['message' => 'Product variant restored successfully.'])]
    #[Response(status: 404, description: 'Product variant not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function restore(string $product, string $variant)
    {
        $variantModel = Product::withTrashed()->where('id', $variant)->where('parent_id', $product)->firstOrFail();

        Gate::authorize('restore', $variantModel);

        $variantModel->restore();

        return (new ProductResource($variantModel->load(['attributes'])))
            ->additional(['message' => 'Product variant restored successfully.']);
    }

    #[Endpoint('Force delete product variant', 'Permanently delete a product variant (cannot be restored)')]
    #[UrlParam('product_id', 'integer', 'The product ID', required: true, example: 1)]
    #[UrlParam('id', 'integer', 'The variant ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Product variant permanently deleted', content: '{"message": "Product variant permanently deleted."}')]
    #[Response(status: 404, description: 'Product variant not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function forceDestroy(string $product, string $variant)
    {
        $variantModel = Product::withTrashed()->where('id', $variant)->where('parent_id', $product)->firstOrFail();

        Gate::authorize('forceDelete', $variantModel);

        $variantModel->forceDelete();

        return response()->json([
            'message' => 'Product variant permanently deleted.',
        ]);
    }
}
