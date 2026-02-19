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
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            'name' => [($isUpdate ? 'sometimes|required' : 'required'), 'string', 'max:255'],
            'project_id' => [($isUpdate ? 'sometimes|required' : 'required'), 'integer', 'exists:projects_projects,id'],
        ];
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
