<?php

namespace Webkul\Product\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Webkul\Product\Enums\PriceRuleApplyTo;
use Webkul\Product\Enums\PriceRuleBase;
use Webkul\Product\Enums\PriceRuleType;
use Webkul\Support\Rules\WithinAllowedCompanies;

class PriceListRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
        $requiredRule = $isUpdate ? ['sometimes', 'required'] : ['required'];

        return [
            'name'                        => [...$requiredRule, 'string', 'max:255'],
            'currency_id'                 => [...$requiredRule, 'integer', 'exists:currencies,id'],
            'company_id'                  => ['nullable', 'integer', 'exists:companies,id', new WithinAllowedCompanies],
            'sort'                        => ['nullable', 'integer'],
            'is_active'                   => ['nullable', 'boolean'],
            'items'                       => ['nullable', 'array'],
            'items.*.id'                  => ['nullable', 'integer', 'exists:products_price_rule_items,id'],
            'items.*.display_apply_to'    => ['nullable', Rule::in([PriceRuleApplyTo::PRODUCT->value, PriceRuleApplyTo::CATEGORY->value])],
            'items.*.type'                => ['required', Rule::enum(PriceRuleType::class)],
            'items.*.base'                => ['nullable', Rule::enum(PriceRuleBase::class)],
            'items.*.product_id'          => ['nullable', 'integer', 'exists:products_products,id'],
            'items.*.category_id'         => ['nullable', 'integer', 'exists:products_categories,id'],
            'items.*.base_price_list_id'  => ['nullable', 'integer', 'exists:products_product_price_lists,id', 'required_if:items.*.base,'.PriceRuleBase::PRICE_RULES->value],
            'items.*.min_quantity'        => ['nullable', 'numeric', 'min:0'],
            'items.*.fixed_price'         => ['nullable', 'numeric'],
            'items.*.percent_price'       => ['nullable', 'numeric'],
            'items.*.price_discount'      => ['nullable', 'numeric'],
            'items.*.price_markup'        => ['nullable', 'numeric'],
            'items.*.price_round'         => ['nullable', 'numeric', 'min:0'],
            'items.*.price_surcharge'     => ['nullable', 'numeric'],
            'items.*.price_min_margin'    => ['nullable', 'numeric'],
            'items.*.price_max_margin'    => ['nullable', 'numeric'],
            'items.*.starts_at'           => ['nullable', 'date'],
            'items.*.ends_at'             => ['nullable', 'date', 'after_or_equal:items.*.starts_at'],
        ];
    }

    /**
     * Get body parameters for API documentation.
     *
     * @return array<string, array<string, mixed>>
     */
    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'Price list name (max 255 characters).',
                'example'     => 'Wholesale',
            ],
            'currency_id' => [
                'description' => 'Currency the price list is expressed in. Orders using this list adopt its currency.',
                'example'     => 1,
            ],
            'company_id' => [
                'description' => 'Company ID. Leave empty to share the price list across companies.',
                'example'     => 1,
            ],
            'is_active' => [
                'description' => 'Whether the price list can be applied to new orders.',
                'example'     => true,
            ],
            'items' => [
                'description' => 'Price rules. The most specific rule that matches a product decides its price.',
            ],
            'items.*.display_apply_to' => [
                'description' => 'Whether the rule targets a product or a category. Defaults to product.',
                'example'     => 'product',
            ],
            'items.*.product_id' => [
                'description' => 'Product the rule applies to. Leave empty to cover all products. Pass a variant to scope the rule to that variant alone.',
                'example'     => 1,
            ],
            'items.*.category_id' => [
                'description' => 'Category the rule applies to. Leave empty to cover all categories.',
                'example'     => 1,
            ],
            'items.*.type' => [
                'description' => 'How the price is computed: fixed, percentage or formula.',
                'example'     => 'fixed',
            ],
            'items.*.base' => [
                'description' => 'Price the rule starts from: list_price, standard_price or price_rules.',
                'example'     => 'list_price',
            ],
            'items.*.min_quantity' => [
                'description' => 'Quantity, in the product unit of measure, from which the rule applies.',
                'example'     => 10,
            ],
            'items.*.fixed_price' => [
                'description' => 'Unit price used when the rule type is fixed.',
                'example'     => 42.5,
            ],
            'items.*.percent_price' => [
                'description' => 'Discount percentage used when the rule type is percentage. Negative applies a mark-up.',
                'example'     => 25,
            ],
        ];
    }
}
