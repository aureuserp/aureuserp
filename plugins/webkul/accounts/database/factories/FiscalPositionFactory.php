<?php

namespace Webkul\Account\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Account\Models\FiscalPosition;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Country;

class FiscalPositionFactory extends Factory
{
    protected $model = FiscalPosition::class;

    public function definition(): array
    {
        return [
            'sort'             => 0,
            'company_id'       => Company::factory(),
            'country_id'       => Country::factory(),
            'country_group_id' => null,
            'creator_id'       => User::factory(),
            'zip_from'         => null,
            'zip_to'           => null,
            'foreign_vat'      => null,
            'name'             => $this->faker->words(2, true),
            'notes'            => null,
            'auto_reply'       => false,
            'vat_required'     => false,
        ];
    }

    public function withVat(): static
    {
        return $this->state(fn (array $attributes) => [
            'vat_required' => true,
            'foreign_vat'  => $this->faker->bothify('??##########'),
        ]);
    }
}
