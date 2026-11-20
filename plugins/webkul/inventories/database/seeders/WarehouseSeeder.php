<?php

namespace Webkul\Inventory\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Webkul\Inventory\Enums\DeliveryStep;
use Webkul\Inventory\Enums\ReceptionStep;
use Webkul\Security\Models\User;

class WarehouseSeeder extends Seeder
{
    /**
     * Seed the application's database with currencies.
     */
    public function run(): void
    {
        $user = User::first();

        $inserted = DB::table('inventories_warehouses')->insertOrIgnore([
            [
                'id'                       => 1,
                'name'                     => 'Your Company',
                'code'                     => 'WH',
                'sort'                     => 1,
                'reception_steps'          => ReceptionStep::ONE_STEP,
                'delivery_steps'           => DeliveryStep::ONE_STEP,
                'company_id'               => $user->default_company_id,
                'creator_id'               => $user->id,
                'created_at'               => now(),
                'updated_at'               => now(),
                'view_location_id'         => 11,
                'lot_stock_location_id'    => 12,
                'input_stock_location_id'  => 13,
                'qc_stock_location_id'     => 14,
                'output_stock_location_id' => 15,
                'pack_stock_location_id'   => 16,
                'mto_pull_id'              => 5,
                'buy_pull_id'              => 13,
                'pick_type_id'             => 3,
                'pack_type_id'             => 4,
                'out_type_id'              => 2,
                'in_type_id'               => 1,
                'internal_type_id'         => 7,
                'qc_type_id'               => 5,
                'store_type_id'            => 6,
                'xdock_type_id'            => 8,
                'crossdock_route_id'       => 4,
                'reception_route_id'       => 2,
                'delivery_route_id'        => 3,
            ],
        ]);

        if (! $inserted) {
            return;
        }

        DB::table('inventories_locations')->whereIn('id', [11, 12, 13, 14, 15, 16])->update([
            'warehouse_id' => 1,
        ]);

        DB::table('inventories_route_warehouses')->insert([
            [
                'warehouse_id' => 1,
                'route_id'     => 2,
            ],
            [
                'warehouse_id' => 1,
                'route_id'     => 3,
            ],
        ]);

        DB::table('inventories_operation_types')->whereIn('id', range(1, 8))->update(['warehouse_id' => 1]);

        DB::table('inventories_rules')->whereIn('id', range(1, 13))->update(['warehouse_id' => 1]);
    }
}
