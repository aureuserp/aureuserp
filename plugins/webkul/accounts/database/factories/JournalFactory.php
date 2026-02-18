<?php

namespace Webkul\Account\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\Journal;
use Webkul\Partner\Models\BankAccount;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

class JournalFactory extends Factory
{
    protected $model = Journal::class;

    public function definition(): array
    {
        return [
            'default_account_id'       => Account::factory(),
            'suspense_account_id'      => null,
            'sort'                     => 0,
            'currency_id'              => Currency::factory(),
            'company_id'               => Company::factory(),
            'profit_account_id'        => null,
            'loss_account_id'          => null,
            'bank_account_id'          => null,
            'creator_id'               => User::factory(),
            'color'                    => $this->faker->numberBetween(1, 10),
            'access_token'             => null,
            'code'                     => strtoupper($this->faker->unique()->lexify('??')),
            'type'                     => JournalType::GENERAL,
            'invoice_reference_type'   => 'invoice',
            'invoice_reference_model'  => 'invoice_number',
            'bank_statements_source'   => 'undefined',
            'name'                     => $this->faker->words(2, true),
            'order_override_regex'     => null,
            'auto_check_on_post'       => false,
            'restrict_mode_hash_table' => false,
            'refund_order'             => false,
            'payment_order'            => false,
            'show_on_dashboard'        => true,
        ];
    }

    public function sale(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => JournalType::SALE,
        ]);
    }

    public function purchase(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => JournalType::PURCHASE,
        ]);
    }

    public function bank(): static
    {
        return $this->state(fn (array $attributes) => [
            'type'            => JournalType::BANK,
            'bank_account_id' => BankAccount::factory(),
        ]);
    }
}
