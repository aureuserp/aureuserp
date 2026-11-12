<?php

namespace Webkul\Inventory\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Webkul\Security\Models\User;

class RouteSeeder extends Seeder
{
    /**
     * Seed the application's database with currencies.
     */
    public function run(): void
    {
        $user = User::first();

        DB::table('inventories_routes')->delete();

        DB::table('inventories_routes')->insert([
            [
                'id'                          => 1,
                'sort'                        => 1,
                'key'                         => 'route.mto',
                'name'                        => __('inventories::seeders.routes.mto'),
                'product_selectable'          => true,
                'product_category_selectable' => false,
                'warehouse_selectable'        => false,
                'packaging_selectable'        => false,
                'company_id'                  => $user->default_company_id,
                'creator_id'                  => $user->id,
                'deleted_at'                  => now(),
                'created_at'                  => now(),
                'updated_at'                  => now(),
            ], [
                'id'                          => 2,
                'sort'                        => 2,
                'key'                         => 'route.reception',
                'name'                        => __('inventories::seeders.routes.reception-one-step', ['warehouse' => 'Your Company']),
                'product_selectable'          => false,
                'product_category_selectable' => true,
                'warehouse_selectable'        => true,
                'packaging_selectable'        => false,
                'company_id'                  => $user->default_company_id,
                'creator_id'                  => $user->id,
                'deleted_at'                  => null,
                'created_at'                  => now(),
                'updated_at'                  => now(),
            ], [
                'id'                          => 3,
                'sort'                        => 3,
                'key'                         => 'route.delivery',
                'name'                        => __('inventories::seeders.routes.delivery-one-step', ['warehouse' => 'Your Company']),
                'product_selectable'          => false,
                'product_category_selectable' => true,
                'warehouse_selectable'        => true,
                'packaging_selectable'        => false,
                'company_id'                  => $user->default_company_id,
                'creator_id'                  => $user->id,
                'deleted_at'                  => null,
                'created_at'                  => now(),
                'updated_at'                  => now(),
            ], [
                'id'                          => 4,
                'sort'                        => 4,
                'key'                         => 'route.crossdock',
                'name'                        => __('inventories::seeders.routes.crossdock', ['warehouse' => 'Your Company']),
                'product_selectable'          => true,
                'product_category_selectable' => true,
                'warehouse_selectable'        => false,
                'packaging_selectable'        => false,
                'company_id'                  => $user->default_company_id,
                'creator_id'                  => $user->id,
                'deleted_at'                  => now(),
                'created_at'                  => now(),
                'updated_at'                  => now(),
            ], [
                'id'                          => 5,
                'sort'                        => 5,
                'key'                         => 'route.buy',
                'name'                        => __('inventories::seeders.routes.buy'),
                'product_selectable'          => true,
                'product_category_selectable' => false,
                'warehouse_selectable'        => false,
                'packaging_selectable'        => false,
                'company_id'                  => null,
                'creator_id'                  => $user->id,
                'deleted_at'                  => now(),
                'created_at'                  => now(),
                'updated_at'                  => now(),
            ], [
                'id'                          => 6,
                'sort'                        => 6,
                'key'                         => 'route.dropship',
                'name'                        => __('inventories::seeders.routes.dropship'),
                'product_selectable'          => true,
                'product_category_selectable' => true,
                'warehouse_selectable'        => false,
                'packaging_selectable'        => false,
                'company_id'                  => null,
                'creator_id'                  => $user->id,
                'deleted_at'                  => now(),
                'created_at'                  => now(),
                'updated_at'                  => now(),
            ],
        ]);
    }
}
