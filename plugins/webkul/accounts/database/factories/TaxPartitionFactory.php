<?php

namespace Webkul\Account\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\Tax;
use Webkul\Account\Models\TaxPartition;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class TaxPartitionFactory extends Factory
{
    protected $model = TaxPartition::class;

    public function definition(): array
    {
        return [
            'account_id'         => Account::factory(),
            'tax_id'             => Tax::factory(),
            'company_id'         => Company::factory(),
            'sort'               => 0,
            'repartition_type'   => 'base',
            'document_type'      => 'invoice',
            'use_in_tax_closing' => true,
            'factor_percent'     => 100.0,
            'creator_id'         => User::factory(),
        ];
    }

    public function refund(): static
    {
        return $this->state(fn (array $attributes) => [
            'document_type' => 'refund',
        ]);
    }

    public function taxRepartition(): static
    {
        return $this->state(fn (array $attributes) => [
            'repartition_type' => 'tax',
        ]);
    }
}
