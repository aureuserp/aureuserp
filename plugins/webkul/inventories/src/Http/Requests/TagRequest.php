<?php

namespace Webkul\Inventory\Http\Requests;

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
        $tagId = $this->route('tag') ?? $this->route('id');

        return [
            'name'  => ($isUpdate ? 'sometimes|' : '').'required|string|max:255|unique:inventories_tags,name'.($tagId ? ','.$tagId : ''),
            'color' => 'nullable|string|max:255',
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'Tag name.',
                'example'     => 'Fragile',
            ],
            'color' => [
                'description' => 'Tag color.',
                'example'     => '#f97316',
            ],
        ];
    }
}
