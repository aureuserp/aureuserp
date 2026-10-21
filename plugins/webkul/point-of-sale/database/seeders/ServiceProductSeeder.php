<?php

namespace Webkul\PointOfSale\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Webkul\Product\Enums\ProductType;

class ServiceProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $uomId = DB::table('unit_of_measures')->orderBy('id')->value('id');

        $categoryId = DB::table('products_categories')->orderBy('id')->value('id');

        if (! $uomId || ! $categoryId) {
            return;
        }

        $products = [
            'TIPS'     => __('point-of-sale::system.products.tip'),
            'DISCOUNT' => __('point-of-sale::system.products.discount'),
        ];

        $now = now();

        foreach ($products as $reference => $name) {
            $exists = DB::table('products_products')
                ->where('reference', $reference)
                ->whereNull('deleted_at')
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('products_products')->insert([
                'type'         => ProductType::SERVICE->value,
                'name'         => $name,
                'reference'    => $reference,
                'price'        => 0,
                'cost'         => 0,
                'enable_sales' => true,
                'uom_id'       => $uomId,
                'uom_po_id'    => $uomId,
                'category_id'  => $categoryId,
                'company_id'   => null,
                'updated_at'   => $now,
                'created_at'   => $now,
            ]);
        }
    }
}
