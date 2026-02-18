<?php

namespace Webkul\Support\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Support\Models\Country;
use Webkul\Support\Models\Currency;

/**
 * @extends Factory<Country>
 */
class CountryFactory extends Factory
{
    protected $model = Country::class;

    public function definition(): array
    {
        return [
            'code'           => $this->faker->unique()->countryCode(),
            'name'           => $this->faker->country(),
            'phone_code'     => $this->faker->numberBetween(1, 999),
            'state_required' => false,
            'zip_required'   => false,
            'currency_id'    => null,
        ];
    }

    public function withCurrency(): static
    {
        return $this->state(fn (array $attributes) => [
            'currency_id' => Currency::factory(),
        ]);
    }
}
