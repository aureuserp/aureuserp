<?php

namespace Webkul\PointOfSale\Services;

use Webkul\PointOfSale\Models\Config;
use Webkul\PointOfSale\Models\Floor;
use Webkul\PointOfSale\Models\Table;

class RestaurantFloorProvisioner
{
    public function syncFor(Config $config): void
    {
        if (! $config->is_restaurant || $config->floors()->exists()) {
            return;
        }

        $floor = Floor::create([
            'name' => $config->name ?? $config->company?->name,
        ]);

        Table::create([
            'table_number' => '1',
            'seats'        => 1,
            'floor_id'     => $floor->id,
        ]);

        $config->floors()->attach($floor->id);
    }
}
