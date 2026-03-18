<?php

namespace Webkul\Support\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\State;

class CompanyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Company::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'                  => fake()->company(),
            'company_id'            => fake()->uuid(),
            'tax_id'                => fake()->bothify('??-########'),
            'registration_number'   => fake()->randomNumber(8, true),
            'email'                 => fake()->unique()->companyEmail(),
            'phone'                 => fake()->phoneNumber(),
            'mobile'                => fake()->e164PhoneNumber(),
            'website'               => fake()->url(),
            'color'                 => fake()->hexColor(),
            'is_active'             => fake()->boolean(),
            'founded_date'          => fake()->date('Y-m-d', '-10 years'),
            'currency_id'           => fake()->randomElement([1, 2, 3]),
        ];
    }
}
