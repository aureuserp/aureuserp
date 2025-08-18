<?php

namespace Webkul\Partner\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Partner\Models\BankAccount;
use Webkul\Partner\Models\Title;
use Webkul\Security\Models\User;

/**
 * @extends Factory<BankAccount>
 */
class TitleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Title::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'       => fake()->name(),
            'short_name' => fake()->name(),
            'creator_id' => User::factory(),
        ];
    }
}
