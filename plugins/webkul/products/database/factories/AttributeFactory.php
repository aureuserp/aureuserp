<?php

namespace Webkul\Product\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Product\Enums\AttributeType;
use Webkul\Product\Models\Attribute;
use Webkul\Security\Models\User;

/**
 * @extends Factory<Attribute>
 */
class AttributeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Attribute::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'       => fake()->name(),
            'type'       => AttributeType::RADIO,
            'sort'       => fake()->randomNumber(),
            'creator_id' => User::factory(),
        ];
    }
}
