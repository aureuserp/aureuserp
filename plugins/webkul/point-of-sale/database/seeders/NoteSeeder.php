<?php

namespace Webkul\PointOfSale\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $notes = [
            'Wait'        => '#ef4444',
            'To Serve'    => '#f97316',
            'Emergency'   => '#eab308',
            'No Dressing' => '#3b82f6',
        ];

        $now = now();

        $sort = 0;

        foreach ($notes as $name => $color) {
            DB::table('pos_notes')->updateOrInsert(
                ['name' => $name],
                [
                    'sort'       => ++$sort,
                    'color'      => $color,
                    'updated_at' => $now,
                    'created_at' => $now,
                ],
            );
        }
    }
}
