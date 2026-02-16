<?php

namespace Webkul\Account\Http\Controllers\API\V1;

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
use Webkul\Account\Http\Requests\CategoryRequest;
use Webkul\Account\Http\Resources\V1\CategoryResource;
use Webkul\Account\Models\Category;

#[Group('Account API Management')]
#[Subgroup('Product Categories', 'Manage product categories with accounting properties')]
#[Authenticated]
class CategoryController extends Controller
{
    #[Endpoint('List categories', 'Retrieve a paginated list of product categories with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> parent, children, creator, propertyAccountIncome, propertyAccountExpense, propertyAccountDownPayment', required: false, example: 'parent,propertyAccountIncome')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[name]', 'string', 'Filter by category name (partial match)', required: false, example: 'No-example')]
    #[QueryParam('filter[parent_id]', 'string', 'Comma-separated list of parent IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('sort', 'string', 'Sort field', example: 'name')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(CategoryResource::class, Category::class, collection: true, paginate: 10)]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index()
    {
        Gate::authorize('viewAny', Category::class);

        $categories = QueryBuilder::for(Category::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::partial('name'),
                AllowedFilter::exact('parent_id'),
            ])
            ->allowedSorts(['id', 'name', 'created_at'])
            ->allowedIncludes([
                'parent',
                'children',
                'creator',
                'propertyAccountIncome',
                'propertyAccountExpense',
                'propertyAccountDownPayment',
            ])
            ->paginate();

        return CategoryResource::collection($categories);
    }

    #[Endpoint('Create category', 'Create a new product category with accounting properties')]
    #[ResponseFromApiResource(CategoryResource::class, Category::class, status: 201, additional: ['message' => 'Category created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"name": ["The name field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(CategoryRequest $request)
    {
        Gate::authorize('create', Category::class);

        $category = Category::create($request->validated());

        return (new CategoryResource($category))
            ->additional(['message' => 'Category created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show category', 'Retrieve a specific product category by its ID')]
    #[UrlParam('id', 'integer', 'The category ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> parent, children, creator, propertyAccountIncome, propertyAccountExpense, propertyAccountDownPayment', required: false, example: 'parent,propertyAccountIncome')]
    #[ResponseFromApiResource(CategoryResource::class, Category::class)]
    #[Response(status: 404, description: 'Category not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $id)
    {
        $category = QueryBuilder::for(Category::where('id', $id))
            ->allowedIncludes([
                'parent',
                'children',
                'creator',
                'propertyAccountIncome',
                'propertyAccountExpense',
                'propertyAccountDownPayment',
            ])
            ->firstOrFail();

        Gate::authorize('view', $category);

        return new CategoryResource($category);
    }

    #[Endpoint('Update category', 'Update an existing product category')]
    #[UrlParam('id', 'integer', 'The category ID', required: true, example: 1)]
    #[ResponseFromApiResource(CategoryResource::class, Category::class, additional: ['message' => 'Category updated successfully.'])]
    #[Response(status: 404, description: 'Category not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"name": ["The name field must be a string."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(CategoryRequest $request, string $id)
    {
        $category = Category::findOrFail($id);

        Gate::authorize('update', $category);

        $category->update($request->validated());

        return (new CategoryResource($category))
            ->additional(['message' => 'Category updated successfully.']);
    }

    #[Endpoint('Delete category', 'Delete a product category')]
    #[UrlParam('id', 'integer', 'The category ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Category deleted', content: '{"message": "Category deleted successfully."}')]
    #[Response(status: 404, description: 'Category not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);

        Gate::authorize('delete', $category);

        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully.',
        ]);
    }
}
