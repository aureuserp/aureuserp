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
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'deadline' => ['nullable', 'date'],
            'is_completed' => ['nullable', 'boolean'],
            'project_id' => ['required', 'integer', 'exists:projects_projects,id'],
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            foreach ($rules as $key => $rule) {
                if (is_array($rule) && in_array('required', $rule, true)) {
                    $updatedRule = $rule;

                    if (! in_array('sometimes', $updatedRule, true)) {
                        array_unshift($updatedRule, 'sometimes');
                    }

                    $rules[$key] = $updatedRule;
                }
            }
        }

        return $rules;
    }

    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'Milestone name.',
                'example' => 'Phase 1 Signoff',
            ],
            'deadline' => [
                'description' => 'Milestone deadline date-time.',
                'example' => '2026-03-20 12:00:00',
            ],
            'is_completed' => [
                'description' => 'Completion flag.',
                'example' => false,
            ],
            'project_id' => [
                'description' => 'Project ID.',
                'example' => 1,
            ],
        ];
    }
}
