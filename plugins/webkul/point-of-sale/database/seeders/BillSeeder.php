<?php

namespace Webkul\PointOfSale\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $values = ['0.05', '0.10', '0.20', '0.25', '0.50', '1.00', '2.00', '5.00', '10.00', '20.00', '50.00', '100.00', '200.00'];

        $now = now();

        foreach ($values as $sort => $value) {
            DB::table('pos_bills')->updateOrInsert(
                ['name' => $value],
                [
                    'value'              => $value,
                    'sort'               => $sort + 1,
                    'is_for_all_configs' => 1,
                    'updated_at'         => $now,
                    'created_at'         => $now,
                ],
            );
        }
    }
}
