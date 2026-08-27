<?php

namespace Webkul\Employee\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Employee\Enums\WorkLocation as WorkLocationEnum;
use Webkul\Employee\Models\WorkLocation;
use Webkul\Security\Models\User;
use Webkul\Support\Database\Factories\Concerns\HasCompanyDefault;

class WorkLocationFactory extends Factory
{
    use HasCompanyDefault;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = WorkLocation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'creator_id'      => User::query()->value('id') ?? User::factory(),
            'name'            => fake()->name,
            'location_type'   => WorkLocationEnum::Office,
            'location_number' => fake()->numberBetween(1, 100),
            'is_active'       => true,
        ];
    }
}
