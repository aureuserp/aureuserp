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
use Webkul\Product\Http\Requests\AttributeOptionRequest;
use Webkul\Product\Http\Resources\V1\AttributeOptionResource;
use Webkul\Product\Models\Attribute;
use Webkul\Product\Models\AttributeOption;

#[Group('Product API Management')]
#[Subgroup('Attribute Options', 'Manage attribute options')]
#[Authenticated]
class AttributeOptionController extends Controller
{
    #[Endpoint('List attribute options', 'Retrieve a paginated list of attribute options for a specific attribute with filtering and sorting')]
    #[UrlParam('attribute_id', 'integer', 'The attribute ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> creator', required: false, example: 'attribute')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[name]', 'string', 'Filter by option name (partial match)', required: false, example: 'No-example')]
    #[QueryParam('sort', 'string', 'Sort field', example: 'sort')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(AttributeOptionResource::class, AttributeOption::class, collection: true, paginate: 10, with: ['attribute'])]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index(string $attribute)
    {
        $attributeModel = Attribute::findOrFail($attribute);

        Gate::authorize('view', $attributeModel);

        $options = QueryBuilder::for(AttributeOption::where('attribute_id', $attribute))
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::partial('name'),
            ])
            ->allowedSorts(['id', 'name', 'sort', 'created_at'])
            ->allowedIncludes([
                'creator',
            ])
            ->paginate();

        return AttributeOptionResource::collection($options);
    }

    #[Endpoint('Create attribute option', 'Create a new option for a specific attribute')]
    #[UrlParam('attribute_id', 'integer', 'The attribute ID', required: true, example: 1)]
    #[ResponseFromApiResource(AttributeOptionResource::class, AttributeOption::class, status: 201, with: ['attribute'], additional: ['message' => 'Attribute option created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"name": ["The name field is required."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function store(AttributeOptionRequest $request, string $attribute)
    {
        $attributeModel = Attribute::findOrFail($attribute);

        Gate::authorize('update', $attributeModel);

        $data = $request->validated();
        $data['attribute_id'] = $attribute;

        $option = AttributeOption::create($data);

        return (new AttributeOptionResource($option->load(['attribute'])))
            ->additional(['message' => 'Attribute option created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show attribute option', 'Retrieve a specific attribute option by its ID')]
    #[UrlParam('attribute_id', 'integer', 'The attribute ID', required: true, example: 1)]
    #[UrlParam('id', 'integer', 'The attribute option ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> creator', required: false, example: 'attribute')]
    #[ResponseFromApiResource(AttributeOptionResource::class, AttributeOption::class, with: ['attribute'])]
    #[Response(status: 404, description: 'Attribute option not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $attribute, string $option)
    {
        $attributeModel = Attribute::findOrFail($attribute);

        Gate::authorize('view', $attributeModel);

        $optionModel = QueryBuilder::for(AttributeOption::where('id', $option)->where('attribute_id', $attribute))
            ->allowedIncludes([
                'creator',
            ])
            ->firstOrFail();

        return new AttributeOptionResource($optionModel);
    }

    #[Endpoint('Update attribute option', 'Update an existing attribute option')]
    #[UrlParam('attribute_id', 'integer', 'The attribute ID', required: true, example: 1)]
    #[UrlParam('id', 'integer', 'The attribute option ID', required: true, example: 1)]
    #[ResponseFromApiResource(AttributeOptionResource::class, AttributeOption::class, with: ['attribute'], additional: ['message' => 'Attribute option updated successfully.'])]
    #[Response(status: 404, description: 'Attribute option not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"name": ["The name field must be a string."]}}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function update(AttributeOptionRequest $request, string $attribute, string $option)
    {
        $attributeModel = Attribute::findOrFail($attribute);

        Gate::authorize('update', $attributeModel);

        $optionModel = AttributeOption::where('id', $option)->where('attribute_id', $attribute)->firstOrFail();
        $optionModel->update($request->validated());

        return (new AttributeOptionResource($optionModel->load(['attribute'])))
            ->additional(['message' => 'Attribute option updated successfully.']);
    }

    #[Endpoint('Delete attribute option', 'Delete an attribute option')]
    #[UrlParam('attribute_id', 'integer', 'The attribute ID', required: true, example: 1)]
    #[UrlParam('id', 'integer', 'The attribute option ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Attribute option deleted', content: '{"message": "Attribute option deleted successfully."}')]
    #[Response(status: 404, description: 'Attribute option not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function destroy(string $attribute, string $option)
    {
        $attributeModel = Attribute::findOrFail($attribute);

        Gate::authorize('update', $attributeModel);

        $optionModel = AttributeOption::where('id', $option)->where('attribute_id', $attribute)->firstOrFail();
        $optionModel->delete();

        return response()->json([
            'message' => 'Attribute option deleted successfully.',
        ]);
    }
}
