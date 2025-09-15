<?php

namespace Webkul\Partner\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Models\Industry;
use Webkul\Partner\Models\Partner;
use Webkul\Partner\Models\Title;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

/**
 * @extends Factory<Partner>
 */
class PartnerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Partner::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_type'     => AccountType::INDIVIDUAL,
            'name'             => fake()->name(),
            'email'            => fake()->unique()->safeEmail(),
            'job_title'        => fake()->jobTitle(),
            'website'          => fake()->url(),
            'tax_id'           => fake()->vat(),
            'phone'            => fake()->phoneNumber(),
            'mobile'           => fake()->phoneNumber(),
            'color'            => fake()->hexColor(),
            'company_registry' => fake()->companyNumber(),
            'reference'        => fake()->unique()->word(),
            'creator_id'       => User::factory(),
            'user_id'          => User::factory(),
            'title_id'         => Title::factory(),
            'company_id'       => Company::factory(),
            'industry_id'      => Industry::factory(),
        ];
    }
}
