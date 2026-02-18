<?php

namespace Webkul\Project\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Webkul\Project\Enums\ProjectVisibility;

class ProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'visibility' => ['required', Rule::in(array_column(ProjectVisibility::cases(), 'value'))],
            'stage_id' => ['required', 'integer', 'exists:projects_project_stages,id'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'partner_id' => ['nullable', 'integer', 'exists:partners_partners,id'],
            'company_id' => ['nullable', 'integer', 'exists:companies,id'],
            'start_date' => ['nullable', 'date', 'required_with:end_date', 'before_or_equal:end_date'],
            'end_date' => ['nullable', 'date', 'required_with:start_date', 'after_or_equal:start_date'],
            'allocated_hours' => ['nullable', 'numeric', 'min:0'],
            'allow_timesheets' => ['nullable', 'boolean'],
            'allow_milestones' => ['nullable', 'boolean'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:projects_tags,id'],
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            foreach ($rules as $key => $rule) {
                if (is_string($rule) && str_starts_with($rule, 'required')) {
                    $rules[$key] = str_replace('required', 'sometimes|required', $rule);
                }

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
                'description' => 'Project name.',
                'example' => 'Website Revamp',
            ],
            'description' => [
                'description' => 'Project description.',
                'example' => 'Rebuild website pages and forms.',
            ],
            'visibility' => [
                'description' => 'Project visibility.',
                'example' => 'internal',
            ],
            'stage_id' => [
                'description' => 'Project stage ID.',
                'example' => 1,
            ],
            'user_id' => [
                'description' => 'Project manager user ID.',
                'example' => 1,
            ],
            'partner_id' => [
                'description' => 'Customer ID.',
                'example' => 1,
            ],
            'company_id' => [
                'description' => 'Company ID.',
                'example' => 1,
            ],
            'start_date' => [
                'description' => 'Planned start date.',
                'example' => '2026-03-01',
            ],
            'end_date' => [
                'description' => 'Planned end date.',
                'example' => '2026-03-31',
            ],
            'allocated_hours' => [
                'description' => 'Allocated effort in hours.',
                'example' => 120,
            ],
            'allow_timesheets' => [
                'description' => 'Allow timesheet entries.',
                'example' => true,
            ],
            'allow_milestones' => [
                'description' => 'Allow milestone usage.',
                'example' => true,
            ],
            'tags' => [
                'description' => 'Project tag IDs.',
                'example' => [1, 2],
            ],
        ];
    }
}
