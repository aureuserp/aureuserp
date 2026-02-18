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
use Webkul\Project\Http\Requests\MilestoneRequest;
use Webkul\Project\Http\Resources\V1\MilestoneResource;
use Webkul\Project\Models\Milestone;

#[Group('Projects API Management')]
#[Subgroup('Milestones', 'Manage milestones')]
#[Authenticated]
class MilestoneController extends Controller
{
    protected array $allowedIncludes = [
        'project',
        'creator',
    ];

    #[Endpoint('List milestones', 'Retrieve a paginated list of milestones')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> project, creator', required: false, example: 'project')]
    #[QueryParam('filter[is_completed]', 'string', 'Filter by completion state', required: false, example: '1')]
    #[ResponseFromApiResource(MilestoneResource::class, Milestone::class, collection: true, paginate: 10)]
    public function index()
    {
        Gate::authorize('viewAny', Milestone::class);

        $milestones = QueryBuilder::for(Milestone::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::exact('name'),
                AllowedFilter::exact('project_id'),
                AllowedFilter::exact('creator_id'),
                AllowedFilter::exact('is_completed'),
            ])
            ->allowedSorts(['id', 'name', 'deadline', 'completed_at', 'created_at', 'updated_at'])
            ->allowedIncludes($this->allowedIncludes)
            ->paginate();

        return MilestoneResource::collection($milestones);
    }

    #[Endpoint('Create milestone', 'Create a new milestone')]
    #[ResponseFromApiResource(MilestoneResource::class, Milestone::class, status: 201, additional: ['message' => 'Milestone created successfully.'])]
    public function store(MilestoneRequest $request)
    {
        Gate::authorize('create', Milestone::class);

        $data = $request->validated();

        if (($data['is_completed'] ?? false) && empty($data['completed_at'])) {
            $data['completed_at'] = now();
        }

        if (array_key_exists('is_completed', $data) && ! $data['is_completed']) {
            $data['completed_at'] = null;
        }

        $milestone = Milestone::create($data);

        return (new MilestoneResource($milestone->load(['project', 'creator'])))
            ->additional(['message' => 'Milestone created successfully.'])
            ->response()
            ->setStatusCode(201);
    }

    #[Endpoint('Show milestone', 'Retrieve a specific milestone by ID')]
    #[UrlParam('id', 'integer', 'The milestone ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> project, creator', required: false, example: 'creator')]
    #[ResponseFromApiResource(MilestoneResource::class, Milestone::class)]
    #[Response(status: 404, description: 'Milestone not found', content: '{"message":"Resource not found."}')]
    public function show(string $id)
    {
        $milestone = QueryBuilder::for(Milestone::where('id', $id))
            ->allowedIncludes($this->allowedIncludes)
            ->firstOrFail();

        Gate::authorize('view', $milestone);

        return new MilestoneResource($milestone);
    }

    #[Endpoint('Update milestone', 'Update an existing milestone')]
    #[UrlParam('id', 'integer', 'The milestone ID', required: true, example: 1)]
    #[ResponseFromApiResource(MilestoneResource::class, Milestone::class, additional: ['message' => 'Milestone updated successfully.'])]
    public function update(MilestoneRequest $request, string $id)
    {
        $milestone = Milestone::findOrFail($id);

        Gate::authorize('update', $milestone);

        $data = $request->validated();

        if (array_key_exists('is_completed', $data)) {
            if ($data['is_completed'] && ! $milestone->completed_at) {
                $data['completed_at'] = now();
            }

            if (! $data['is_completed']) {
                $data['completed_at'] = null;
            }
        }

        $milestone->update($data);

        return (new MilestoneResource($milestone->load(['project', 'creator'])))
            ->additional(['message' => 'Milestone updated successfully.']);
    }

    #[Endpoint('Delete milestone', 'Delete a milestone')]
    #[UrlParam('id', 'integer', 'The milestone ID', required: true, example: 1)]
    #[Response(status: 200, description: 'Milestone deleted successfully', content: '{"message":"Milestone deleted successfully."}')]
    public function destroy(string $id)
    {
        $milestone = Milestone::findOrFail($id);

        Gate::authorize('delete', $milestone);

        $milestone->delete();

        return response()->json([
            'message' => 'Milestone deleted successfully.',
        ]);
    }
}
