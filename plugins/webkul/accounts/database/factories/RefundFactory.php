<?php

namespace Webkul\Account\Database\Factories;

use Webkul\Account\Models\Move;

/**
 * @extends MoveFactory<\Webkul\Account\Models\Move>
 */
class RefundFactory extends MoveFactory
{
    protected $model = Move::class;

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterMaking(function (Move $move) {
            //
        })->afterCreating(function (Move $move) {
            //
        })->refund();
    }
}
