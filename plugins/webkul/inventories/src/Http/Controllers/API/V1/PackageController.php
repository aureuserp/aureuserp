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
use Webkul\Inventory\Http\Requests\PackageRequest;
use Webkul\Inventory\Http\Resources\V1\PackageResource;
use Webkul\Inventory\Models\Package;

#[Group('InventoryAPI Management')]
#[Subgroup('Packages', 'Manage inventory packages')]
#[Authenticated]
class PackageController extends Controller
{
    protected array $allowedIncludes = [
        'packageType',
        'location',
        'company',
        'creator',
        'operations',
        'moves',
        'moveLines',
    ];

    #[Endpoint('List packages', 'Retrieve a paginated list of packages with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> packageType, location, company, creator, operations, moves, moveLines', required: false, example: 'packageType,location')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false)]
    #[QueryParam('filter[name]', 'string', 'Filter by package name', required: false, example: 'PACK')]
    #[QueryParam('filter[package_type_id]', 'string', 'Filter by package type IDs', required: false)]
    #[QueryParam('filter[location_id]', 'string', 'Filter by location IDs', required: false)]
    #[QueryParam('filter[company_id]', 'string', 'Filter by company IDs', required: false)]
    #[QueryParam('sort', 'string', 'Sort field', required: false, example: '-created_at')]
    #[ResponseFromApiResource(PackageResource::class, Package::class, collection: true, paginate: 10)]
    public function index()
    {
        Gate::authorize('viewAny', Package::class);

        $packages = QueryBuilder::for(Package::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::partial('name'),
                AllowedFilter::exact('package_type_id'),
                AllowedFilter::exact('location_id'),
                AllowedFilter::exact('company_id'),
            ])
            ->allowedSorts(['id', 'name', 'pack_date', 'created_at', 'updated_at'])
            ->allowedIncludes($this->allowedIncludes)
            ->paginate();

        return PackageResource::collection($packages);
    }

    #[Endpoint('Create package', 'Create a new package')]
    #[ResponseFromApiResource(PackageResource::class, Package::class, status: 201, additional: ['message' => 'Package created successfully.'])]
    #[Response(status: 422, description: 'Validation error')]
    public function store(PackageRequest $request)
    {
        Gate::authorize('create', Package::class);

        $package = Package::create($request->validated());

        return (new PackageResource($package->load($this->allowedIncludes)))
            ->additional(['message' => 'Package created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show package', 'Retrieve a specific package by ID')]
    #[UrlParam('id', 'integer', 'The package ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> packageType, location, company, creator, operations, moves, moveLines', required: false, example: 'packageType,location')]
    #[ResponseFromApiResource(PackageResource::class, Package::class)]
    #[Response(status: 404, description: 'Package not found')]
    public function show(string $id)
    {
        $package = QueryBuilder::for(Package::where('id', $id))
            ->allowedIncludes($this->allowedIncludes)
            ->firstOrFail();

        Gate::authorize('view', $package);

        return new PackageResource($package);
    }

    #[Endpoint('Update package', 'Update an existing package')]
    #[UrlParam('id', 'integer', 'The package ID', required: true, example: 1)]
    #[ResponseFromApiResource(PackageResource::class, Package::class, additional: ['message' => 'Package updated successfully.'])]
    #[Response(status: 404, description: 'Package not found')]
    #[Response(status: 422, description: 'Validation error')]
    public function update(PackageRequest $request, string $id)
    {
        $package = Package::findOrFail($id);

        Gate::authorize('update', $package);

        $package->update($request->validated());

        return (new PackageResource($package->load($this->allowedIncludes)))
            ->additional(['message' => 'Package updated successfully.']);
    }

    #[Endpoint('Delete package', 'Delete a package')]
    #[UrlParam('id', 'integer', 'The package ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Package deleted successfully', content: '{"message":"Package deleted successfully."}')]
    #[Response(status: 404, description: 'Package not found')]
    public function destroy(string $id)
    {
        $package = Package::findOrFail($id);

        Gate::authorize('delete', $package);

        $package->delete();

        return response()->json([
            'message' => 'Package deleted successfully.',
        ]);
    }
}
