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
use Webkul\Project\Http\Requests\TaskStageRequest;
use Webkul\Project\Http\Resources\V1\TaskStageResource;
use Webkul\Project\Models\TaskStage;

#[Group('Projects API Management')]
#[Subgroup('Task Stages', 'Manage task stages')]
#[Authenticated]
class TaskStageController extends Controller
{
    protected array $allowedIncludes = [
        'project',
        'user',
        'creator',
        'company',
        'tasks',
    ];

    #[Endpoint('List task stages', 'Retrieve a paginated list of task stages')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> project, user, creator, company, tasks', required: false, example: 'project')]
    #[QueryParam('filter[trashed]', 'string', 'Filter by trashed status. </br></br><b>Available options:</b> with, only', required: false, example: 'with')]
    #[ResponseFromApiResource(TaskStageResource::class, TaskStage::class, collection: true, paginate: 10)]
    public function index()
    {
        Gate::authorize('viewAny', TaskStage::class);

        $stages = QueryBuilder::for(TaskStage::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('name'),
                AllowedFilter::exact('project_id'),
                AllowedFilter::exact('user_id'),
                AllowedFilter::exact('company_id'),
                AllowedFilter::exact('creator_id'),
                AllowedFilter::exact('is_active'),
                AllowedFilter::exact('is_collapsed'),
                AllowedFilter::trashed(),
            ])
            ->allowedSorts(['id', 'name', 'sort', 'created_at', 'updated_at'])
            ->allowedIncludes($this->allowedIncludes)
            ->paginate();

        return TaskStageResource::collection($stages);
    }

    #[Endpoint('Create task stage', 'Create a new task stage')]
    #[ResponseFromApiResource(TaskStageResource::class, TaskStage::class, status: 201, additional: ['message' => 'Task stage created successfully.'])]
    public function store(TaskStageRequest $request)
    {
        Gate::authorize('create', TaskStage::class);

        $stage = TaskStage::create($request->validated());

        return (new TaskStageResource($stage->load(['project', 'user', 'creator', 'company'])))
            ->additional(['message' => 'Task stage created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show task stage', 'Retrieve a specific task stage by ID')]
    #[UrlParam('id', 'integer', 'The task stage ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> project, user, creator, company, tasks', required: false, example: 'tasks')]
    #[ResponseFromApiResource(TaskStageResource::class, TaskStage::class)]
    #[Response(status: 404, description: 'Task stage not found', content: '{"message":"Resource not found."}')]
    public function show(string $id)
    {
        $stage = QueryBuilder::for(TaskStage::where('id', $id))
            ->allowedIncludes($this->allowedIncludes)
            ->firstOrFail();

        Gate::authorize('view', $stage);

        return new TaskStageResource($stage);
    }

    #[Endpoint('Update task stage', 'Update an existing task stage')]
    #[UrlParam('id', 'integer', 'The task stage ID', required: true, example: 1)]
    #[ResponseFromApiResource(TaskStageResource::class, TaskStage::class, additional: ['message' => 'Task stage updated successfully.'])]
    public function update(TaskStageRequest $request, string $id)
    {
        $stage = TaskStage::findOrFail($id);

        Gate::authorize('update', $stage);

        $stage->update($request->validated());

        return (new TaskStageResource($stage->load(['project', 'user', 'creator', 'company'])))
            ->additional(['message' => 'Task stage updated successfully.']);
    }

    #[Endpoint('Delete task stage', 'Soft delete a task stage')]
    #[UrlParam('id', 'integer', 'The task stage ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Task stage deleted successfully', content: '{"message":"Task stage deleted successfully."}')]
    public function destroy(string $id)
    {
        $stage = TaskStage::findOrFail($id);

        Gate::authorize('delete', $stage);

        $stage->delete();

        return response()->json([
            'message' => 'Task stage deleted successfully.',
        ]);
    }

    #[Endpoint('Restore task stage', 'Restore a soft-deleted task stage')]
    #[UrlParam('id', 'integer', 'The task stage ID', required: true, example: 1)]
    #[ResponseFromApiResource(TaskStageResource::class, TaskStage::class, additional: ['message' => 'Task stage restored successfully.'])]
    public function restore(string $id)
    {
        $stage = TaskStage::withTrashed()->findOrFail($id);

        Gate::authorize('restore', $stage);

        $stage->restore();

        return (new TaskStageResource($stage->load(['project', 'user', 'creator', 'company'])))
            ->additional(['message' => 'Task stage restored successfully.']);
    }

    #[Endpoint('Force delete task stage', 'Permanently delete a task stage')]
    #[UrlParam('id', 'integer', 'The task stage ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Task stage permanently deleted', content: '{"message":"Task stage permanently deleted."}')]
    public function forceDestroy(string $id)
    {
        $stage = TaskStage::withTrashed()->findOrFail($id);

        Gate::authorize('forceDelete', $stage);

        $stage->forceDelete();

        return response()->json([
            'message' => 'Task stage permanently deleted.',
        ]);
    }
}
