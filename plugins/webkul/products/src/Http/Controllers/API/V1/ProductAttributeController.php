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
use Webkul\Product\Http\Requests\ProductAttributeRequest;
use Webkul\Product\Http\Resources\V1\ProductAttributeResource;
use Webkul\Product\Models\Product;
use Webkul\Product\Models\ProductAttribute;
use Webkul\Product\Models\ProductAttributeValue;

#[Group('Product API Management')]
#[Subgroup('Product Attributes', 'Manage product attributes')]
#[Authenticated]
class ProductAttributeController extends Controller
{
    #[Endpoint('List product attributes', 'Retrieve a paginated list of attributes for a specific product')]
    #[UrlParam('product_id', 'integer', 'The product ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> attribute, values, creator, values.attributeOption', required: false, example: 'attribute,values')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[attribute_id]', 'string', 'Comma-separated list of attribute IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('sort', 'string', 'Sort field', example: 'sort')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(ProductAttributeResource::class, ProductAttribute::class, collection: true, paginate: 10, with: ['attribute'])]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index(string $product)
    {
        $productModel = Product::findOrFail($product);

        Gate::authorize('view', $productModel);

        $productAttributes = QueryBuilder::for(ProductAttribute::where('product_id', $product))
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('attribute_id'),
            ])
            ->allowedSorts(['id', 'sort', 'created_at'])
            ->allowedIncludes([
                'attribute',
                'values',
                'creator',
                'values.attributeOption',
            ])
            ->paginate();

        return ProductAttributeResource::collection($productAttributes);
    }

    #[Endpoint('Create product attribute', 'Create a new attribute for a specific product')]
    #[UrlParam('product_id', 'integer', 'The product ID', required: true, example: 1)]
    #[ResponseFromApiResource(ProductAttributeResource::class, ProductAttribute::class, status: 201, with: ['attribute'], additional: ['message' => 'Product attribute created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"attribute_id": ["The attribute id field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(ProductAttributeRequest $request, string $product)
    {
        $productModel = Product::findOrFail($product);

        Gate::authorize('update', $productModel);

        $data = $request->validated();
        $data['product_id'] = $product;

        $options = $data['options'] ?? [];
        unset($data['options']);

        $productAttribute = ProductAttribute::create($data);

        // Create product attribute values for each option
        if (! empty($options)) {
            foreach ($options as $optionId) {
                ProductAttributeValue::create([
                    'product_id'             => $product,
                    'attribute_id'           => $data['attribute_id'],
                    'product_attribute_id'   => $productAttribute->id,
                    'attribute_option_id'    => $optionId,
                ]);
            }
        }

        return (new ProductAttributeResource($productAttribute->load(['attribute', 'values'])))
            ->additional(['message' => 'Product attribute created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show product attribute', 'Retrieve a specific product attribute by its ID')]
    #[UrlParam('product_id', 'integer', 'The product ID', required: true, example: 1)]
    #[UrlParam('id', 'integer', 'The product attribute ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> attribute, values, creator, values.attributeOption', required: false, example: 'attribute,values')]
    #[ResponseFromApiResource(ProductAttributeResource::class, ProductAttribute::class, with: ['attribute'])]
    #[Response(status: 404, description: 'Product attribute not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $product, string $attribute)
    {
        $productModel = Product::findOrFail($product);

        Gate::authorize('view', $productModel);

        $productAttribute = QueryBuilder::for(ProductAttribute::where('id', $attribute)->where('product_id', $product))
            ->allowedIncludes([
                'attribute',
                'values',
                'creator',
                'values.attributeOption',
            ])
            ->firstOrFail();

        return new ProductAttributeResource($productAttribute);
    }

    #[Endpoint('Update product attribute', 'Update an existing product attribute')]
    #[UrlParam('product_id', 'integer', 'The product ID', required: true, example: 1)]
    #[UrlParam('id', 'integer', 'The product attribute ID', required: true, example: 1)]
    #[ResponseFromApiResource(ProductAttributeResource::class, ProductAttribute::class, with: ['attribute'], additional: ['message' => 'Product attribute updated successfully.'])]
    #[Response(status: 404, description: 'Product attribute not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"attribute_id": ["The attribute id must be an integer."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(ProductAttributeRequest $request, string $product, string $attribute)
    {
        $productModel = Product::findOrFail($product);

        Gate::authorize('update', $productModel);

        $productAttribute = ProductAttribute::where('id', $attribute)->where('product_id', $product)->firstOrFail();

        $data = $request->validated();
        $options = $data['options'] ?? null;
        unset($data['options']);

        $productAttribute->update($data);

        if ($options !== null) {
            ProductAttributeValue::where('product_attribute_id', $productAttribute->id)->delete();

            foreach ($options as $optionId) {
                ProductAttributeValue::create([
                    'product_id'             => $product,
                    'attribute_id'           => $productAttribute->attribute_id,
                    'product_attribute_id'   => $productAttribute->id,
                    'attribute_option_id'    => $optionId,
                ]);
            }
        }

        return (new ProductAttributeResource($productAttribute->load(['attribute', 'values'])))
            ->additional(['message' => 'Product attribute updated successfully.']);
    }

    #[Endpoint('Delete product attribute', 'Delete a product attribute')]
    #[UrlParam('product_id', 'integer', 'The product ID', required: true, example: 1)]
    #[UrlParam('id', 'integer', 'The product attribute ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Product attribute deleted', content: '{"message": "Product attribute deleted successfully."}')]
    #[Response(status: 404, description: 'Product attribute not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $product, string $attribute)
    {
        $productModel = Product::findOrFail($product);

        Gate::authorize('update', $productModel);

        $productAttribute = ProductAttribute::where('id', $attribute)->where('product_id', $product)->firstOrFail();
        $productAttribute->delete();

        return response()->json([
            'message' => 'Product attribute deleted successfully.',
        ]);
    }
}
