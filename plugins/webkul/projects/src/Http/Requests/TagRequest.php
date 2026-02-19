<?php

namespace Webkul\Project\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
class TagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
        $tagId = $this->route('tag');

        return [
            'name' => [($isUpdate ? 'sometimes|required' : 'required'), 'string', 'max:255', 'unique:projects_tags,name'.($tagId ? ','.$tagId.',id,deleted_at,NULL' : ',NULL,id,deleted_at,NULL')],
            'color' => ['nullable', 'string', 'regex:/^#(?:[0-9a-fA-F]{3}){1,2}$/'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'Tag name.',
                'example' => 'Urgent',
            ],
            'color' => [
                'description' => 'Hex color.',
                'example' => '#ff0000',
            ],
        ];
    }
}
