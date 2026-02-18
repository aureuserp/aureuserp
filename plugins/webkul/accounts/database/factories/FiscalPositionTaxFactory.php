<?php

namespace Webkul\Account\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Account\Models\FiscalPosition;
use Webkul\Account\Models\FiscalPositionTax;
use Webkul\Account\Models\Tax;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class FiscalPositionTaxFactory extends Factory
{
    protected $model = FiscalPositionTax::class;

    public function definition(): array
    {
        return [
            'fiscal_position_id' => FiscalPosition::factory(),
            'company_id'         => Company::factory(),
            'tax_source_id'      => Tax::factory(),
            'tax_destination_id' => Tax::factory(),
            'creator_id'         => User::factory(),
        ];
    }
}
