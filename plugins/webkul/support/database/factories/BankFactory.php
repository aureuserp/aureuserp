<?php

namespace Webkul\Support\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Bank;
use Webkul\Support\Models\Country;
use Webkul\Support\Models\State;

/**
 * @extends Factory<Bank>
 */
class BankFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Bank::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'       => fake()->name(),
            'code'       => fake()->swiftBicNumber(),
            'email'      => fake()->unique()->safeEmail(),
            'phone'      => fake()->phoneNumber(),
            'street1'    => fake()->streetAddress(),
            'street2'    => fake()->streetAddress(),
            'city'       => fake()->city(),
            'zip'        => fake()->postcode(),
            'state_id'   => State::factory(),
            'country_id' => Country::factory(),
            'creator_id' => User::factory(),
        ];
    }
}
