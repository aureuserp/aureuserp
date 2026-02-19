<?php

namespace Webkul\Project\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Webkul\Project\Enums\TaskState;
use Webkul\Project\Models\Milestone;
use Webkul\Project\Models\Task;

class TaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            'title' => [($isUpdate ? 'sometimes|required' : 'required'), 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'state' => [($isUpdate ? 'sometimes|required' : 'required'), 'string', Rule::enum(TaskState::class)],
            'stage_id' => [($isUpdate ? 'sometimes|required' : 'required'), 'integer', 'exists:projects_task_stages,id'],
            'project_id' => ['nullable', 'integer', 'exists:projects_projects,id'],
            'milestone_id' => ['nullable', 'integer', 'exists:projects_milestones,id'],
            'partner_id' => ['nullable', 'integer', 'exists:partners_partners,id'],
            'parent_id' => ['nullable', 'integer', 'exists:projects_tasks,id'],
            'deadline' => ['nullable', 'date'],
            'allocated_hours' => ['nullable', 'numeric', 'min:0'],
            'priority' => ['nullable', 'boolean'],
            'users' => ['nullable', 'array'],
            'users.*' => ['integer', 'exists:users,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:projects_tags,id'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $taskId = $this->route('task');
            $existingTask = $taskId ? Task::find($taskId) : null;

            $effectiveProjectId = $this->has('project_id')
                ? $this->input('project_id')
                : $existingTask?->project_id;

            $effectiveMilestoneId = $this->exists('milestone_id')
                ? $this->input('milestone_id')
                : $existingTask?->milestone_id;

            if (! $effectiveMilestoneId) {
                return;
            }

            if (! $effectiveProjectId) {
                $validator->errors()->add('project_id', 'The project field is required when milestone is selected.');

                return;
            }

            $exists = Milestone::query()
                ->where('id', $effectiveMilestoneId)
                ->where('project_id', $effectiveProjectId)
                ->exists();

            if (! $exists) {
                $validator->errors()->add('milestone_id', 'The selected milestone does not belong to the selected project.');
            }
        });
    }

    public function bodyParameters(): array
    {
        return [
            'title' => [
                'description' => 'Task title.',
                'example' => 'Prepare homepage wireframe',
            ],
            'description' => [
                'description' => 'Task description.',
                'example' => 'Create initial wireframe and review notes.',
            ],
            'state' => [
                'description' => 'Task state.',
                'example' => 'in_progress',
            ],
            'stage_id' => [
                'description' => 'Task stage ID.',
                'example' => 1,
            ],
            'project_id' => [
                'description' => 'Project ID.',
                'example' => 1,
            ],
            'milestone_id' => [
                'description' => 'Milestone ID.',
                'example' => 1,
            ],
            'partner_id' => [
                'description' => 'Customer ID.',
                'example' => 1,
            ],
            'parent_id' => [
                'description' => 'Parent task ID for sub-task.',
                'example' => 10,
            ],
            'deadline' => [
                'description' => 'Task deadline date-time.',
                'example' => '2026-03-10 18:00:00',
            ],
            'allocated_hours' => [
                'description' => 'Allocated effort in hours.',
                'example' => 12,
            ],
            'priority' => [
                'description' => 'Priority flag.',
                'example' => true,
            ],
            'users' => [
                'description' => 'Assigned user IDs.',
                'example' => [1, 2],
            ],
            'tags' => [
                'description' => 'Task tag IDs.',
                'example' => [1, 3],
            ],
        ];
    }
}
