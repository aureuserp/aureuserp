<?php

namespace Webkul\Account\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Account\Enums\AmountType;
use Webkul\Account\Enums\TaxIncludeOverride;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Models\Tax;
use Webkul\Account\Models\TaxGroup;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Country;

class TaxFactory extends Factory
{
    protected $model = Tax::class;

    public function definition(): array
    {
        return [
            'sort'                             => 0,
            'company_id'                       => Company::factory(),
            'tax_group_id'                     => TaxGroup::factory(),
            'cash_basis_transition_account_id' => null,
            'country_id'                       => null,
            'creator_id'                       => User::factory(),
            'type_tax_use'                     => TypeTaxUse::SALE,
            'tax_scope'                        => 'consu',
            'amount_type'                      => AmountType::PERCENT,
            'price_include_override'           => TaxIncludeOverride::DEFAULT,
            'tax_exigibility'                  => 'on_invoice',
            'name'                             => $this->faker->words(2, true),
            'description'                      => null,
            'invoice_label'                    => null,
            'invoice_legal_notes'              => null,
            'amount'                           => 10.0,
            'is_active'                        => true,
            'include_base_amount'              => false,
            'is_base_affected'                 => false,
            'analytic'                         => false,
        ];
    }

    public function purchase(): static
    {
        return $this->state(fn (array $attributes) => [
            'type_tax_use' => TypeTaxUse::PURCHASE,
        ]);
    }

    public function fixedAmount(): static
    {
        return $this->state(fn (array $attributes) => [
            'amount_type' => AmountType::FIXED,
            'amount'      => 5.0,
        ]);
    }

    public function withCountry(): static
    {
        return $this->state(fn (array $attributes) => [
            'country_id' => Country::factory(),
        ]);
    }
}
