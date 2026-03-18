<?php

namespace Webkul\Purchase\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Partner\Models\Partner;
use Webkul\Purchase\Enums\RequisitionState;
use Webkul\Purchase\Enums\RequisitionType;
use Webkul\Purchase\Models\Requisition;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

/**
 * @extends Factory<Requisition>
 */
class RequisitionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Requisition::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'        => 'PA-'.fake()->unique()->numberBetween(1000, 9999),
            'type'        => RequisitionType::BLANKET_ORDER,
            'state'       => RequisitionState::DRAFT,
            'currency_id' => Currency::factory(),
            'partner_id'  => Partner::query()->value('id') ?? Partner::factory(),
            'company_id'  => Company::factory(),
            'creator_id'  => User::query()->value('id') ?? User::factory(),
        ];
    }

    /**
     * Draft state.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'state' => RequisitionState::DRAFT,
        ]);
    }

    /**
     * Confirmed state.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'state' => RequisitionState::CONFIRMED,
        ]);
    }

    /**
     * Closed state.
     */
    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'state' => RequisitionState::CLOSED,
        ]);
    }

    /**
     * Canceled state.
     */
    public function canceled(): static
    {
        return $this->state(fn (array $attributes) => [
            'state' => RequisitionState::CANCELED,
        ]);
    }
}
