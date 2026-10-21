<?php

namespace Webkul\PointOfSale\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Inventory\Models\Lot;
use Webkul\PointOfSale\Models\OrderLine;
use Webkul\PointOfSale\Models\OrderLineLot;
use Webkul\Security\Models\User;

/**
 * @extends Factory<OrderLineLot>
 */
class OrderLineLotFactory extends Factory
{
    protected $model = OrderLineLot::class;

    public function definition(): array
    {
        return [
            'lot_name'      => strtoupper(fake()->bothify('LOT-#####')),
            'qty'           => 1,
            'order_line_id' => OrderLine::query()->value('id') ?? OrderLine::factory(),
            'lot_id'        => null,
            'creator_id'    => User::query()->value('id') ?? User::factory(),
        ];
    }

    public function forLot(Lot|int $lot): static
    {
        return $this->state(fn () => [
            'lot_id'   => $lot instanceof Lot ? $lot->id : $lot,
            'lot_name' => $lot instanceof Lot ? $lot->name : Lot::query()->whereKey($lot)->value('name'),
        ]);
    }
}
