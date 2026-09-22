<?php

namespace Webkul\Product\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Product\Models\PriceList;
use Webkul\Security\Models\User;
use Webkul\Support\Database\Factories\Concerns\HasCompanyDefault;
use Webkul\Support\Models\Currency;

/**
 * @extends Factory<PriceList>
 */
class PriceListFactory extends Factory
{
    use HasCompanyDefault;

    protected $model = PriceList::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sort'        => fake()->randomNumber(2),
            'currency_id' => default_currency_id() ?? Currency::query()->value('id'),
            'creator_id'  => User::query()->value('id') ?? User::factory(),
            'name'        => fake()->words(3, true),
            'is_active'   => true,
        ];
    }
}
