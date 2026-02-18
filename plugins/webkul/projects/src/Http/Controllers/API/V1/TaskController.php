<?php

namespace Webkul\Project\Http\Controllers\API\V1;

use Illuminate\Support\Facades\DB;
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
use Webkul\Project\Http\Requests\TaskRequest;
use Webkul\Project\Http\Resources\V1\TaskResource;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\Task;

#[Group('Projects API Management')]
#[Subgroup('Tasks', 'Manage tasks')]
#[Authenticated]
class TaskController extends Controller
{
    protected array $allowedIncludes = [
        'stage',
        'project',
        'milestone',
        'partner',
        'parent',
        'company',
        'creator',
        'subTasks',
        'users',
        'tags',
    ];

    #[Endpoint('List tasks', 'Retrieve a paginated list of tasks with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> stage, project, milestone, partner, parent, company, creator, subTasks, users, tags', required: false, example: 'project,users')]
    #[QueryParam('filter[id]', 'string', 'Filter by task IDs', required: false, example: 'No-example')]
    #[QueryParam('filter[state]', 'string', 'Filter by task state', required: false, example: 'in_progress')]
    #[QueryParam('filter[trashed]', 'string', 'Filter by trashed status. </br></br><b>Available options:</b> with, only', required: false, example: 'with')]
    #[QueryParam('sort', 'string', 'Sort field', required: false, example: '-created_at')]
    #[ResponseFromApiResource(TaskResource::class, Task::class, collection: true, paginate: 10)]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message":"Unauthenticated."}')]
    public function index()
    {
        Gate::authorize('viewAny', Task::class);

        $tasks = QueryBuilder::for(Task::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('stage_id'),
                AllowedFilter::exact('project_id'),
                AllowedFilter::exact('milestone_id'),
                AllowedFilter::exact('partner_id'),
                AllowedFilter::exact('parent_id'),
                AllowedFilter::exact('company_id'),
                AllowedFilter::exact('state'),
                AllowedFilter::exact('priority'),
                AllowedFilter::exact('is_active'),
                AllowedFilter::exact('is_recurring'),
                AllowedFilter::trashed(),
            ])
            ->allowedSorts(['id', 'title', 'state', 'priority', 'sort', 'deadline', 'allocated_hours', 'total_hours_spent', 'created_at', 'updated_at'])
            ->allowedIncludes($this->allowedIncludes)
            ->paginate();

        return TaskResource::collection($tasks);
    }

    #[Endpoint('Create task', 'Create a new task')]
    #[ResponseFromApiResource(TaskResource::class, Task::class, status: 201, additional: ['message' => 'Task created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message":"The given data was invalid."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message":"Unauthenticated."}')]
    public function store(TaskRequest $request)
    {
        Gate::authorize('create', Task::class);

        $data = $request->validated();

        return DB::transaction(function () use ($data, $request) {
            $users = $data['users'] ?? [];
            $tags = $data['tags'] ?? [];
            $milestoneId = $data['milestone_id'] ?? null;

            unset($data['users'], $data['tags'], $data['milestone_id']);

            if (isset($data['project_id']) && ! array_key_exists('partner_id', $data)) {
                $data['partner_id'] = Project::find($data['project_id'])?->partner_id;
            }

            $task = Task::create($data);

            if ($request->exists('milestone_id')) {
                $task->milestone_id = $milestoneId;
                $task->save();
            }

            if ($request->has('users')) {
                $task->users()->sync($users);
            }

            if ($request->has('tags')) {
                $task->tags()->sync($tags);
            }

            $task->load(['stage', 'project', 'milestone', 'partner', 'parent', 'company', 'creator', 'users', 'tags']);

            return (new TaskResource($task))
                ->additional(['message' => 'Task created successfully.'])
                ->response()
                ->setStatusCode(201);
        });
    }

    #[Endpoint('Show task', 'Retrieve a specific task by ID')]
    #[UrlParam('id', 'integer', 'The task ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> stage, project, milestone, partner, parent, company, creator, subTasks, users, tags', required: false, example: 'stage,tags')]
    #[ResponseFromApiResource(TaskResource::class, Task::class)]
    #[Response(status: 404, description: 'Task not found', content: '{"message":"Resource not found."}')]
    public function show(string $id)
    {
        $task = QueryBuilder::for(Task::where('id', $id))
            ->allowedIncludes($this->allowedIncludes)
            ->firstOrFail();

        Gate::authorize('view', $task);

        return new TaskResource($task);
    }

    #[Endpoint('Update task', 'Update an existing task')]
    #[UrlParam('id', 'integer', 'The task ID', required: true, example: 1)]
    #[ResponseFromApiResource(TaskResource::class, Task::class, additional: ['message' => 'Task updated successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message":"The given data was invalid."}')]
    #[Response(status: 404, description: 'Task not found', content: '{"message":"Resource not found."}')]
    public function update(TaskRequest $request, string $id)
    {
        $task = Task::findOrFail($id);

        Gate::authorize('update', $task);

        $data = $request->validated();

        return DB::transaction(function () use ($task, $data, $request) {
            $users = $data['users'] ?? [];
            $tags = $data['tags'] ?? [];
            $milestoneId = $data['milestone_id'] ?? null;

            unset($data['users'], $data['tags'], $data['milestone_id']);

            if (isset($data['project_id']) && ! array_key_exists('partner_id', $data)) {
                $data['partner_id'] = Project::find($data['project_id'])?->partner_id;
            }

            $task->update($data);

            if ($request->exists('milestone_id')) {
                $task->milestone_id = $milestoneId;
                $task->save();
            }

            if ($request->has('users')) {
                $task->users()->sync($users);
            }

            if ($request->has('tags')) {
                $task->tags()->sync($tags);
            }

            $task->load(['stage', 'project', 'milestone', 'partner', 'parent', 'company', 'creator', 'users', 'tags']);

            return (new TaskResource($task))
                ->additional(['message' => 'Task updated successfully.']);
        });
    }

    #[Endpoint('Delete task', 'Soft delete a task')]
    #[UrlParam('id', 'integer', 'The task ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Task deleted successfully', content: '{"message":"Task deleted successfully."}')]
    #[Response(status: 404, description: 'Task not found', content: '{"message":"Resource not found."}')]
    public function destroy(string $id)
    {
        $task = Task::findOrFail($id);

        Gate::authorize('delete', $task);

        $task->delete();

        return response()->json([
            'message' => 'Task deleted successfully.',
        ]);
    }

    #[Endpoint('Restore task', 'Restore a soft-deleted task')]
    #[UrlParam('id', 'integer', 'The task ID', required: true, example: 1)]
    #[ResponseFromApiResource(TaskResource::class, Task::class, additional: ['message' => 'Task restored successfully.'])]
    #[Response(status: 404, description: 'Task not found', content: '{"message":"Resource not found."}')]
    public function restore(string $id)
    {
        $task = Task::withTrashed()->findOrFail($id);

        Gate::authorize('restore', $task);

        $task->restore();

        return (new TaskResource($task->load(['stage', 'project', 'milestone', 'partner', 'parent', 'company', 'creator', 'users', 'tags'])))
            ->additional(['message' => 'Task restored successfully.']);
    }

    #[Endpoint('Force delete task', 'Permanently delete a task')]
    #[UrlParam('id', 'integer', 'The task ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Task permanently deleted', content: '{"message":"Task permanently deleted."}')]
    #[Response(status: 404, description: 'Task not found', content: '{"message":"Resource not found."}')]
    public function forceDestroy(string $id)
    {
        $task = Task::withTrashed()->findOrFail($id);

        Gate::authorize('forceDelete', $task);

        $task->forceDelete();

        return response()->json([
            'message' => 'Task permanently deleted.',
        ]);
    }
}
