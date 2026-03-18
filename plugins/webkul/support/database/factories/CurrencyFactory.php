<?php

namespace Webkul\Support\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Support\Models\Currency;

class CurrencyFactory extends Factory
{
    protected $model = Currency::class;

    public function definition(): array
    {
        return [
            'name'           => fake()->unique()->currencyCode(),
            'symbol'         => fake()->randomElement(['$', '€', '£', '¥', '₹']),
            'iso_numeric'    => fake()->unique()->numerify('###'),
            'decimal_places' => 2,
            'full_name'      => fake()->unique()->words(2, true),
            'rounding'       => 0.01,
            'active'         => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'active' => false,
        ]);
    }
}
