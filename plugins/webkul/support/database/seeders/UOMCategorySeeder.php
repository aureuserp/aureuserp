<?php

namespace Webkul\Support\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;
use Webkul\Security\Models\User;

class UOMCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            if (! Schema::hasTable('unit_of_measure_categories')) {
                $this->command?->warn('Skipping UOMCategorySeeder: Table unit_of_measure_categories does not exist.');

                return;
            }

            if (! Schema::hasTable('users')) {
                $this->command?->warn('Skipping UOMCategorySeeder: Table users does not exist.');

                return;
            }

            $user = User::first();

            $categories = [
                ['id' => 1, 'name' => 'Unit'],
                ['id' => 2, 'name' => 'Weight'],
                ['id' => 3, 'name' => 'Working Time'],
                ['id' => 4, 'name' => 'Length / Distance'],
                ['id' => 5, 'name' => 'Surface'],
                ['id' => 6, 'name' => 'Volume'],
            ];

            foreach ($categories as $category) {
                DB::table('unit_of_measure_categories')->updateOrInsert(
                    ['id' => $category['id']],
                    [
                        'name'       => $category['name'],
                        'creator_id' => $user?->id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]
                );
            }
        } catch (Throwable $e) {
            report($e);
        }
    }
}
