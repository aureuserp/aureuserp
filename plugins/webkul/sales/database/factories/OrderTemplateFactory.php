<?php

namespace Webkul\Sale\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Sale\Models\OrderTemplate;

/**
 * @extends Factory<OrderTemplate>
 */
class OrderTemplateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OrderTemplate::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sort'                       => fake()->randomNumber(),
            'company_id'                 => null,
            'journal_id'                 => null,
            'creator_id'                 => null,
            'name'                       => fake()->name,
            'number_of_days'             => fake()->numberBetween(1, 90),
            'require_signature'          => fake()->boolean(30),
            'require_payment'            => fake()->boolean(30),
            'recurrence'                 => fake()->boolean(20),
            'recurrence_period'          => fake()->optional()->numberBetween(1, 12),
            'mail_template_id'           => null,
            'auto_confirmation'          => fake()->boolean(50),
            'confirmation_mail_template' => null,
            'is_active'                  => fake()->boolean(80),
            'note'                       => fake()->optional()->paragraph(),
        ];
    }
}
