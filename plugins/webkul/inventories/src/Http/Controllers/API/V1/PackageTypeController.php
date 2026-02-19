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
use Webkul\Inventory\Http\Requests\PackageTypeRequest;
use Webkul\Inventory\Http\Resources\V1\PackageTypeResource;
use Webkul\Inventory\Models\PackageType;

#[Group('Inventory API Management')]
#[Subgroup('Package Types', 'Manage package type configurations')]
#[Authenticated]
class PackageTypeController extends Controller
{
    protected array $allowedIncludes = [
        'company',
        'creator',
    ];

    #[Endpoint('List package types', 'Retrieve a paginated list of package types with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> company, creator', required: false, example: 'company')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false)]
    #[QueryParam('filter[name]', 'string', 'Filter by package type name', required: false, example: 'Box')]
    #[QueryParam('filter[barcode]', 'string', 'Filter by barcode', required: false, example: 'PKG')]
    #[QueryParam('filter[company_id]', 'string', 'Filter by company IDs', required: false)]
    #[QueryParam('sort', 'string', 'Sort field', required: false, example: '-created_at')]
    #[ResponseFromApiResource(PackageTypeResource::class, PackageType::class, collection: true, paginate: 10)]
    public function index()
    {
        Gate::authorize('viewAny', PackageType::class);

        $packageTypes = QueryBuilder::for(PackageType::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::partial('name'),
                AllowedFilter::partial('barcode'),
                AllowedFilter::exact('company_id'),
            ])
            ->allowedSorts(['id', 'name', 'sort', 'height', 'width', 'length', 'base_weight', 'max_weight', 'created_at', 'updated_at'])
            ->allowedIncludes($this->allowedIncludes)
            ->paginate();

        return PackageTypeResource::collection($packageTypes);
    }

    #[Endpoint('Create package type', 'Create a new package type')]
    #[ResponseFromApiResource(PackageTypeResource::class, PackageType::class, status: 201, additional: ['message' => 'Package type created successfully.'])]
    #[Response(status: 422, description: 'Validation error')]
    public function store(PackageTypeRequest $request)
    {
        Gate::authorize('create', PackageType::class);

        $packageType = PackageType::create($request->validated());

        return (new PackageTypeResource($packageType->load($this->allowedIncludes)))
            ->additional(['message' => 'Package type created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show package type', 'Retrieve a specific package type by ID')]
    #[UrlParam('id', 'integer', 'The package type ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> company, creator', required: false, example: 'company')]
    #[ResponseFromApiResource(PackageTypeResource::class, PackageType::class)]
    #[Response(status: 404, description: 'Package type not found')]
    public function show(string $id)
    {
        $packageType = QueryBuilder::for(PackageType::where('id', $id))
            ->allowedIncludes($this->allowedIncludes)
            ->firstOrFail();

        Gate::authorize('view', $packageType);

        return new PackageTypeResource($packageType);
    }

    #[Endpoint('Update package type', 'Update an existing package type')]
    #[UrlParam('id', 'integer', 'The package type ID', required: true, example: 1)]
    #[ResponseFromApiResource(PackageTypeResource::class, PackageType::class, additional: ['message' => 'Package type updated successfully.'])]
    #[Response(status: 404, description: 'Package type not found')]
    #[Response(status: 422, description: 'Validation error')]
    public function update(PackageTypeRequest $request, string $id)
    {
        $packageType = PackageType::findOrFail($id);

        Gate::authorize('update', $packageType);

        $packageType->update($request->validated());

        return (new PackageTypeResource($packageType->load($this->allowedIncludes)))
            ->additional(['message' => 'Package type updated successfully.']);
    }

    #[Endpoint('Delete package type', 'Delete a package type')]
    #[UrlParam('id', 'integer', 'The package type ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Package type deleted successfully', content: '{"message":"Package type deleted successfully."}')]
    #[Response(status: 404, description: 'Package type not found')]
    public function destroy(string $id)
    {
        $packageType = PackageType::findOrFail($id);

        Gate::authorize('delete', $packageType);

        $packageType->delete();

        return response()->json([
            'message' => 'Package type deleted successfully.',
        ]);
    }
}
