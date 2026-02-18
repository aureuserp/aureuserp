<?php

namespace Webkul\Support\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\CurrencyRate;

/**
 * @extends Factory<CurrencyRate>
 */
class CurrencyRateFactory extends Factory
{
    protected $model = CurrencyRate::class;

    public function definition(): array
    {
        return [
            'name'        => $this->faker->date(),
            'rate'        => $this->faker->randomFloat(6, 0.5, 2),
            'currency_id' => Currency::factory(),
            'creator_id'  => User::factory(),
            'company_id'  => Company::factory(),
        ];
    }
}
