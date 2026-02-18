<?php

namespace Webkul\Project\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectStageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('projects_project_stages', 'name')->whereNull('deleted_at')->ignore($this->route('project_stage')),
            ],
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
                'description' => 'Project stage name.',
                'example' => 'In Progress',
            ],
        ];
    }
}
