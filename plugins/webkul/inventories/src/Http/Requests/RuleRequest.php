<?php

namespace Webkul\Inventory\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Webkul\Inventory\Enums\RuleAction;

class RuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name'                    => ['required', 'string', 'max:255'],
            'action'                  => ['required', Rule::enum(RuleAction::class)],
            'operation_type_id'       => ['required', 'integer', 'exists:inventories_operation_types,id'],
            'source_location_id'      => ['required', 'integer', 'exists:inventories_locations,id'],
            'destination_location_id' => ['required', 'integer', 'exists:inventories_locations,id'],
            'partner_address_id'      => ['nullable', 'integer', 'exists:partners_partners,id'],
            'delay'                   => ['nullable', 'integer', 'min:0'],
            'route_id'                => ['required', 'integer', 'exists:inventories_routes,id'],
            'company_id'              => ['nullable', 'integer', 'exists:companies,id'],
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            foreach ($rules as $key => $rule) {
                if (str_contains($key, '.')) {
                    continue;
                }

                if (is_array($rule) && in_array('required', $rule, true)) {
                    $index = array_search('required', $rule, true);

                    if ($index !== false) {
                        unset($rule[$index]);
                    }

                    array_unshift($rule, 'sometimes', 'required');
                    $rules[$key] = array_values($rule);
                }
            }
        }

        return $rules;
    }

    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'Rule name.',
                'example'     => 'WH1: Stock -> Output',
            ],
            'action' => [
                'description' => 'Rule action.',
                'example'     => 'pull',
            ],
            'operation_type_id' => [
                'description' => 'Operation type ID.',
                'example'     => 1,
            ],
            'source_location_id' => [
                'description' => 'Source location ID.',
                'example'     => 3,
            ],
            'destination_location_id' => [
                'description' => 'Destination location ID.',
                'example'     => 4,
            ],
            'partner_address_id' => [
                'description' => 'Partner address ID.',
                'example'     => 10,
            ],
            'delay' => [
                'description' => 'Lead time in days.',
                'example'     => 1,
            ],
            'route_id' => [
                'description' => 'Route ID.',
                'example'     => 1,
            ],
            'company_id' => [
                'description' => 'Company ID.',
                'example'     => 1,
            ],
        ];
    }
}
