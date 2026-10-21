<?php

namespace Webkul\PointOfSale\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\PointOfSale\Enums\SessionState;
use Webkul\PointOfSale\Models\Config;
use Webkul\PointOfSale\Models\Session;
use Webkul\Security\Models\User;
use Webkul\Support\Database\Factories\Concerns\HasCompanyDefault;

/**
 * @extends Factory<Session>
 */
class SessionFactory extends Factory
{
    use HasCompanyDefault;

    protected $model = Session::class;

    public function definition(): array
    {
        return [
            'state'              => SessionState::OPENING_CONTROL,
            'cash_balance_start' => 0,
            'config_id'          => Config::query()->value('id') ?? Config::factory(),
            'user_id'            => User::query()->value('id') ?? User::factory(),
            'creator_id'         => User::query()->value('id') ?? User::factory(),
        ];
    }

    public function opened(float $cashBalanceStart = 0): static
    {
        return $this->state(fn () => [
            'state'              => SessionState::OPENED,
            'cash_balance_start' => $cashBalanceStart,
            'started_at'         => now(),
        ]);
    }

    public function closingControl(): static
    {
        return $this->state(fn () => ['state' => SessionState::CLOSING_CONTROL]);
    }

    public function closed(): static
    {
        return $this->state(fn () => [
            'state'      => SessionState::CLOSED,
            'started_at' => now()->subHours(8),
            'stopped_at' => now(),
        ]);
    }

    public function rescueFor(Session|int $session): static
    {
        return $this->state(fn () => [
            'is_rescue'             => true,
            'rescue_for_session_id' => $session instanceof Session ? $session->id : $session,
        ]);
    }
}
