<?php

namespace Webkul\Support\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
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

            $categories = ['unit', 'weight', 'working-time', 'length', 'surface', 'volume'];

            seed_rows('unit_of_measure_categories', collect($categories)
                ->map(fn (string $key, int $index): array => [
                    'id'         => $index + 1,
                    'key'        => "uom-category.{$key}",
                    'name'       => __("support::seeders.uom-categories.{$key}"),
                    'creator_id' => $user?->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ])
                ->all());
        } catch (Throwable $e) {
            report($e);
        }
    }
}
