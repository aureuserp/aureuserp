<?php

namespace Webkul\PointOfSale\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Models\Journal;
use Webkul\PointOfSale\Enums\PaymentTerminalType;
use Webkul\PointOfSale\Models\PaymentMethod;
use Webkul\Security\Models\User;
use Webkul\Support\Database\Factories\Concerns\HasCompanyDefault;

/**
 * @extends Factory<PaymentMethod>
 */
class PaymentMethodFactory extends Factory
{
    use HasCompanyDefault;

    protected $model = PaymentMethod::class;

    public function definition(): array
    {
        return [
            'name'                 => fake()->words(2, true),
            'terminal_type'        => PaymentTerminalType::NONE,
            'sort'                 => fake()->numberBetween(1, 20),
            'is_split_transaction' => false,
            'journal_id'           => static::journalOfType(JournalType::CASH),
            'creator_id'           => User::query()->value('id') ?? User::factory(),
        ];
    }

    public function cash(): static
    {
        return $this->state(fn () => ['journal_id' => static::journalOfType(JournalType::CASH)]);
    }

    public function bank(): static
    {
        return $this->state(fn () => ['journal_id' => static::journalOfType(JournalType::BANK)]);
    }

    public function payLater(): static
    {
        return $this->state(fn () => ['journal_id' => null]);
    }

    public function splitTransactions(): static
    {
        return $this->state(fn () => ['is_split_transaction' => true]);
    }

    protected static function journalOfType(JournalType $type): ?int
    {
        return Journal::query()->where('type', $type)->value('id');
    }
}
