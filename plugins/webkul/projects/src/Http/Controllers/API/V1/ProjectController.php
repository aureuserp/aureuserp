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
use Webkul\Project\Http\Requests\ProjectRequest;
use Webkul\Project\Http\Resources\V1\ProjectResource;
use Webkul\Project\Models\Project;

#[Group('Project API Management')]
#[Subgroup('Projects', 'Manage projects')]
#[Authenticated]
class ProjectController extends Controller
{
    protected array $allowedIncludes = [
        'stage',
        'partner',
        'company',
        'user',
        'creator',
        'tasks',
        'taskStages',
        'milestones',
        'tags',
        'favoriteUsers',
    ];

    #[Endpoint('List projects', 'Retrieve a paginated list of projects with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> stage, partner, company, user, creator, tasks, taskStages, milestones, tags, favoriteUsers', required: false, example: 'stage,tags')]
    #[QueryParam('filter[id]', 'string', 'Filter by project IDs', required: false, example: 'No-example')]
    #[QueryParam('filter[visibility]', 'string', 'Filter by visibility', required: false, example: 'internal')]
    #[QueryParam('filter[trashed]', 'string', 'Filter by trashed status. </br></br><b>Available options:</b> with, only', required: false, example: 'with')]
    #[QueryParam('sort', 'string', 'Sort field', required: false, example: '-created_at')]
    #[ResponseFromApiResource(ProjectResource::class, Project::class, collection: true, paginate: 10)]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message":"Unauthenticated."}')]
    public function index()
    {
        Gate::authorize('viewAny', Project::class);

        $projects = QueryBuilder::for(Project::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('stage_id'),
                AllowedFilter::exact('partner_id'),
                AllowedFilter::exact('company_id'),
                AllowedFilter::exact('user_id'),
                AllowedFilter::exact('is_active'),
                AllowedFilter::exact('allow_timesheets'),
                AllowedFilter::exact('allow_milestones'),
                AllowedFilter::exact('allow_task_dependencies'),
                AllowedFilter::exact('visibility'),
                AllowedFilter::trashed(),
            ])
            ->allowedSorts(['id', 'name', 'sort', 'start_date', 'end_date', 'allocated_hours', 'created_at', 'updated_at'])
            ->allowedIncludes($this->allowedIncludes)
            ->paginate();

        return ProjectResource::collection($projects);
    }

    #[Endpoint('Create project', 'Create a new project')]
    #[ResponseFromApiResource(ProjectResource::class, Project::class, status: 201, additional: ['message' => 'Project created successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message":"The given data was invalid."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message":"Unauthenticated."}')]
    public function store(ProjectRequest $request)
    {
        Gate::authorize('create', Project::class);

        $data = $request->validated();

        return DB::transaction(function () use ($data, $request) {
            $tags = $data['tags'] ?? [];
            unset($data['tags']);

            $project = Project::create($data);

            if ($request->has('tags')) {
                $project->tags()->sync($tags);
            }

            $project->load(['stage', 'partner', 'company', 'user', 'creator', 'tags']);

            return (new ProjectResource($project))
                ->additional(['message' => 'Project created successfully.'])
                ->response()
                ->setStatusCode(201);
        });
    }

    #[Endpoint('Show project', 'Retrieve a specific project by ID')]
    #[UrlParam('id', 'integer', 'The project ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> stage, partner, company, user, creator, tasks, taskStages, milestones, tags, favoriteUsers', required: false, example: 'tasks,tags')]
    #[ResponseFromApiResource(ProjectResource::class, Project::class)]
    #[Response(status: 404, description: 'Project not found', content: '{"message":"Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message":"Unauthenticated."}')]
    public function show(string $id)
    {
        $project = QueryBuilder::for(Project::where('id', $id))
            ->allowedIncludes($this->allowedIncludes)
            ->firstOrFail();

        Gate::authorize('view', $project);

        return new ProjectResource($project);
    }

    #[Endpoint('Update project', 'Update an existing project')]
    #[UrlParam('id', 'integer', 'The project ID', required: true, example: 1)]
    #[ResponseFromApiResource(ProjectResource::class, Project::class, additional: ['message' => 'Project updated successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message":"The given data was invalid."}')]
    #[Response(status: 404, description: 'Project not found', content: '{"message":"Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message":"Unauthenticated."}')]
    public function update(ProjectRequest $request, string $id)
    {
        $project = Project::findOrFail($id);

        Gate::authorize('update', $project);

        $data = $request->validated();

        return DB::transaction(function () use ($project, $data, $request) {
            $tags = $data['tags'] ?? [];
            unset($data['tags']);

            $project->update($data);

            if ($request->has('tags')) {
                $project->tags()->sync($tags);
            }

            $project->load(['stage', 'partner', 'company', 'user', 'creator', 'tags']);

            return (new ProjectResource($project))
                ->additional(['message' => 'Project updated successfully.']);
        });
    }

    #[Endpoint('Delete project', 'Soft delete a project')]
    #[UrlParam('id', 'integer', 'The project ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Project deleted successfully', content: '{"message":"Project deleted successfully."}')]
    #[Response(status: 404, description: 'Project not found', content: '{"message":"Resource not found."}')]
    public function destroy(string $id)
    {
        $project = Project::findOrFail($id);

        Gate::authorize('delete', $project);

        $project->delete();

        return response()->json([
            'message' => 'Project deleted successfully.',
        ]);
    }

    #[Endpoint('Restore project', 'Restore a soft-deleted project')]
    #[UrlParam('id', 'integer', 'The project ID', required: true, example: 1)]
    #[ResponseFromApiResource(ProjectResource::class, Project::class, additional: ['message' => 'Project restored successfully.'])]
    #[Response(status: 404, description: 'Project not found', content: '{"message":"Resource not found."}')]
    public function restore(string $id)
    {
        $project = Project::withTrashed()->findOrFail($id);

        Gate::authorize('restore', $project);

        $project->restore();

        return (new ProjectResource($project->load(['stage', 'partner', 'company', 'user', 'creator', 'tags'])))
            ->additional(['message' => 'Project restored successfully.']);
    }

    #[Endpoint('Force delete project', 'Permanently delete a project')]
    #[UrlParam('id', 'integer', 'The project ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Project permanently deleted', content: '{"message":"Project permanently deleted."}')]
    #[Response(status: 404, description: 'Project not found', content: '{"message":"Resource not found."}')]
    public function forceDestroy(string $id)
    {
        $project = Project::withTrashed()->findOrFail($id);

        Gate::authorize('forceDelete', $project);

        $project->forceDelete();

        return response()->json([
            'message' => 'Project permanently deleted.',
        ]);
    }
}
