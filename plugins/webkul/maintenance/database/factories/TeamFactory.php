<?php

namespace Webkul\Maintenance\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Maintenance\Models\Team;
use Webkul\Security\Models\User;
use Webkul\Support\Database\Factories\Concerns\HasCompanyDefault;

/**
 * @extends Factory<Team>
 */
class TeamFactory extends Factory
{
    use HasCompanyDefault;

    protected $model = Team::class;

    public function definition(): array
    {
        return [
            'name'       => fake()->company().' Maintenance',
            'creator_id' => User::query()->value('id') ?? User::factory(),
        ];
    }

    public function withUsers(int $count = 1): static
    {
        return $this->afterCreating(function (Team $team) use ($count): void {
            $team->users()->attach(
                User::factory()
                    ->count($count)
                    ->create()
                    ->pluck('id')
                    ->all()
            );
        });
    }
}
