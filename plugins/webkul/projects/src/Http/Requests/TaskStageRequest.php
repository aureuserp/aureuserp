<?php

namespace Webkul\Project\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskStageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
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
                'description' => 'Task stage name.',
                'example' => 'Backlog',
            ],
            'project_id' => [
                'description' => 'Project ID this stage belongs to.',
                'example' => 1,
            ],
        ];
    }
}
