<?php

namespace Webkul\Account\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\FiscalPosition;
use Webkul\Account\Models\FiscalPositionAccount;
use Webkul\Security\Models\User;
use Webkul\Support\Database\Factories\Concerns\HasCompanyDefault;

class FiscalPositionAccountFactory extends Factory
{
    use HasCompanyDefault;

    protected $model = FiscalPositionAccount::class;

    public function definition(): array
    {
        return [
            'fiscal_position_id'     => FiscalPosition::factory(),
            'account_source_id'      => Account::factory(),
            'account_destination_id' => Account::factory(),
            'creator_id'             => User::query()->value('id') ?? User::factory(),
        ];
    }

    public function mapping(int $sourceId, int $destinationId): static
    {
        return $this->state(fn () => [
            'account_source_id'      => $sourceId,
            'account_destination_id' => $destinationId,
        ]);
    }
}
