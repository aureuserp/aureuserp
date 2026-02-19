<?php

namespace Webkul\Project\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
class ProjectStageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
        $stage = $this->route('project_stage');
        $stageId = is_object($stage) ? $stage->id : $stage;

        return [
            'name' => ($isUpdate ? 'sometimes|' : '').'required|string|max:255|unique:projects_project_stages,name,'.($stageId ?: 'NULL').',id,deleted_at,NULL',
        ];
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
