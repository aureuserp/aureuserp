<?php

namespace Webkul\Project\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MilestoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
        $requiredRule = $isUpdate ? ['sometimes', 'required'] : ['required'];

        return [
            'name'         => [...$requiredRule, 'string', 'max:255'],
            'deadline'     => ['nullable', 'date'],
            'is_completed' => ['nullable', 'boolean'],
            'project_id'   => [...$requiredRule, 'integer', 'exists:projects_projects,id'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'Milestone name.',
                'example'     => 'Phase 1 Signoff',
            ],
            'deadline' => [
                'description' => 'Milestone deadline date-time.',
                'example'     => '2026-03-20 12:00:00',
            ],
            'is_completed' => [
                'description' => 'Completion flag.',
                'example'     => false,
            ],
            'project_id' => [
                'description' => 'Project ID.',
                'example'     => 1,
            ],
        ];
    }
}
