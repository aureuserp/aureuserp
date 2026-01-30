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
            'sort'                       => $this->faker->randomNumber(),
            'company_id'                 => null,
            'journal_id'                 => null,
            'creator_id'                 => null,
            'name'                       => $this->faker->name,
            'number_of_days'             => $this->faker->numberBetween(1, 90),
            'require_signature'          => $this->faker->boolean(30),
            'require_payment'            => $this->faker->boolean(30),
            'recurrence'                 => $this->faker->boolean(20),
            'recurrence_period'          => $this->faker->optional()->numberBetween(1, 12),
            'mail_template_id'           => null,
            'auto_confirmation'          => $this->faker->boolean(50),
            'confirmation_mail_template' => null,
            'is_active'                  => $this->faker->boolean(80),
            'note'                       => $this->faker->optional()->paragraph(),
        ];
    }
}
