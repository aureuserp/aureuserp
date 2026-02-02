<?php

namespace Webkul\Support\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Security\Models\User;
use Webkul\Support\Models\UOM;
use Webkul\Support\Models\UOMCategory;

class UOMFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UOM::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type'        => 'smaller',
            'name'        => $this->faker->word(),
            'factor'      => $this->faker->randomFloat(2, 0.1, 10),
            'category_id' => UOMCategory::factory(),
            'creator_id'  => User::factory(),
        ];
    }

    public function bigger(): static
    {
        return $this->state(fn (array $attributes) => [
            'type'   => 'bigger',
            'factor' => $this->faker->randomFloat(2, 1, 100),
        ]);
    }

    public function reference(): static
    {
        return $this->state(fn (array $attributes) => [
            'type'   => 'reference',
            'factor' => 1.0,
        ]);
    }
}
