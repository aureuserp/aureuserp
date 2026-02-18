<?php

namespace Webkul\Project\Http\Controllers\API\V1;

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
use Webkul\Project\Http\Requests\ProjectStageRequest;
use Webkul\Project\Http\Resources\V1\ProjectStageResource;
use Webkul\Project\Models\ProjectStage;

#[Group('Projects API Management')]
#[Subgroup('Project Stages', 'Manage project stages')]
#[Authenticated]
class ProjectStageController extends Controller
{
    protected array $allowedIncludes = [
        'creator',
        'company',
        'projects',
    ];

    #[Endpoint('List project stages', 'Retrieve a paginated list of project stages')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> creator, company, projects', required: false, example: 'projects')]
    #[QueryParam('filter[trashed]', 'string', 'Filter by trashed status. </br></br><b>Available options:</b> with, only', required: false, example: 'with')]
    #[ResponseFromApiResource(ProjectStageResource::class, ProjectStage::class, collection: true, paginate: 10)]
    public function index()
    {
        Gate::authorize('viewAny', ProjectStage::class);

        $stages = QueryBuilder::for(ProjectStage::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('name'),
                AllowedFilter::exact('is_active'),
                AllowedFilter::exact('is_collapsed'),
                AllowedFilter::exact('company_id'),
                AllowedFilter::exact('creator_id'),
                AllowedFilter::trashed(),
            ])
            ->allowedSorts(['id', 'name', 'sort', 'created_at', 'updated_at'])
            ->allowedIncludes($this->allowedIncludes)
            ->paginate();

        return ProjectStageResource::collection($stages);
    }

    #[Endpoint('Create project stage', 'Create a new project stage')]
    #[ResponseFromApiResource(ProjectStageResource::class, ProjectStage::class, status: 201, additional: ['message' => 'Project stage created successfully.'])]
    public function store(ProjectStageRequest $request)
    {
        Gate::authorize('create', ProjectStage::class);

        $stage = ProjectStage::create($request->validated());

        return (new ProjectStageResource($stage->load(['creator', 'company'])))
            ->additional(['message' => 'Project stage created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show project stage', 'Retrieve a specific project stage by ID')]
    #[UrlParam('id', 'integer', 'The project stage ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> creator, company, projects', required: false, example: 'projects')]
    #[ResponseFromApiResource(ProjectStageResource::class, ProjectStage::class)]
    #[Response(status: 404, description: 'Project stage not found', content: '{"message":"Resource not found."}')]
    public function show(string $id)
    {
        $stage = QueryBuilder::for(ProjectStage::where('id', $id))
            ->allowedIncludes($this->allowedIncludes)
            ->firstOrFail();

        Gate::authorize('view', $stage);

        return new ProjectStageResource($stage);
    }

    #[Endpoint('Update project stage', 'Update an existing project stage')]
    #[UrlParam('id', 'integer', 'The project stage ID', required: true, example: 1)]
    #[ResponseFromApiResource(ProjectStageResource::class, ProjectStage::class, additional: ['message' => 'Project stage updated successfully.'])]
    public function update(ProjectStageRequest $request, string $id)
    {
        $stage = ProjectStage::findOrFail($id);

        Gate::authorize('update', $stage);

        $stage->update($request->validated());

        return (new ProjectStageResource($stage->load(['creator', 'company'])))
            ->additional(['message' => 'Project stage updated successfully.']);
    }

    #[Endpoint('Delete project stage', 'Soft delete a project stage')]
    #[UrlParam('id', 'integer', 'The project stage ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Project stage deleted successfully', content: '{"message":"Project stage deleted successfully."}')]
    public function destroy(string $id)
    {
        $stage = ProjectStage::findOrFail($id);

        Gate::authorize('delete', $stage);

        $stage->delete();

        return response()->json([
            'message' => 'Project stage deleted successfully.',
        ]);
    }

    #[Endpoint('Restore project stage', 'Restore a soft-deleted project stage')]
    #[UrlParam('id', 'integer', 'The project stage ID', required: true, example: 1)]
    #[ResponseFromApiResource(ProjectStageResource::class, ProjectStage::class, additional: ['message' => 'Project stage restored successfully.'])]
    public function restore(string $id)
    {
        $stage = ProjectStage::withTrashed()->findOrFail($id);

        Gate::authorize('restore', $stage);

        $stage->restore();

        return (new ProjectStageResource($stage->load(['creator', 'company'])))
            ->additional(['message' => 'Project stage restored successfully.']);
    }

    #[Endpoint('Force delete project stage', 'Permanently delete a project stage')]
    #[UrlParam('id', 'integer', 'The project stage ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Project stage permanently deleted', content: '{"message":"Project stage permanently deleted."}')]
    public function forceDestroy(string $id)
    {
        $stage = ProjectStage::withTrashed()->findOrFail($id);

        Gate::authorize('forceDelete', $stage);

        $stage->forceDelete();

        return response()->json([
            'message' => 'Project stage permanently deleted.',
        ]);
    }
}
