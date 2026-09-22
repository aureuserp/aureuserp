<?php

namespace Webkul\Product\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PriceListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencyId = default_currency_id();

        if (! $currencyId || DB::table('products_product_price_lists')->exists()) {
            return;
        }

        DB::table('products_product_price_lists')->insert([
            'name'        => 'Public Price List',
            'sort'        => 1,
            'currency_id' => $currencyId,
            'company_id'  => null,
            'creator_id'  => null,
            'is_active'   => true,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }
}
