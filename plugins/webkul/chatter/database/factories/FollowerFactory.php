<?php

namespace Webkul\Chatter\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Chatter\Models\Follower;
use Webkul\Partner\Models\Partner;

/**
 * @extends Factory<Follower>
 */
class FollowerFactory extends Factory
{
    protected $model = Follower::class;

    public function definition(): array
    {
        return [
            'followable_id'   => null,
            'followable_type' => null,
            'partner_id'      => Partner::factory(),
            'followed_at'     => now(),
        ];
    }

    public function followable($followable): static
    {
        return $this->state(fn (array $attributes) => [
            'followable_id'   => $followable->id,
            'followable_type' => get_class($followable),
        ]);
    }
}
