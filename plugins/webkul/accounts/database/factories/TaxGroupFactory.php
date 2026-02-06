<?php

namespace Webkul\Account\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Account\Models\TaxGroup;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Country;

class TaxGroupFactory extends Factory
{
    protected $model = TaxGroup::class;

    public function definition(): array
    {
        return [
            'sort'               => 0,
            'company_id'         => Company::factory(),
            'country_id'         => Country::factory(),
            'creator_id'         => User::factory(),
            'name'               => $this->faker->words(2, true),
            'preceding_subtotal' => 0,
        ];
    }
}
