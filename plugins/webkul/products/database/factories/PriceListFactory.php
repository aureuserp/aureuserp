<?php

namespace Webkul\Product\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Product\Models\PriceList;

/**
 * @extends Factory<\App\Models\PriceList>
 */
class PriceListFactory extends Factory
{
    protected $model = PriceList::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sort'        => $this->faker->randomNumber(2),
            'currency_id' => 1,
            'company_id'  => 1,
            'creator_id'  => 1,
            'name'        => $this->faker->name,
            'is_active'   => true,
        ];
    }
}
