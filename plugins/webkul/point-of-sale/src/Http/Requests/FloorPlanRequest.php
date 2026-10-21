<?php

namespace Webkul\PointOfSale\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Webkul\PointOfSale\Enums\TableShape;

class FloorPlanRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'                    => ['sometimes', 'required', 'string', 'max:255'],
            'background_color'        => ['sometimes', 'nullable', 'string', 'max:32'],
            'tables'                  => ['sometimes', 'array'],
            'tables.*.id'             => ['nullable', 'integer'],
            'tables.*.table_number'   => ['required', 'string', 'max:16', 'distinct'],
            'tables.*.shape'          => ['nullable', Rule::enum(TableShape::class)],
            'tables.*.position_h'     => ['nullable', 'numeric', 'min:0'],
            'tables.*.position_v'     => ['nullable', 'numeric', 'min:0'],
            'tables.*.width'          => ['nullable', 'numeric', 'min:20'],
            'tables.*.height'         => ['nullable', 'numeric', 'min:20'],
            'tables.*.seats'          => ['nullable', 'integer', 'min:1'],
            'tables.*.color'          => ['nullable', 'string', 'max:32'],
        ];
    }
}
