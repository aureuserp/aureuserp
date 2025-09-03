<?php

namespace Webkul\Support\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;
use Webkul\Security\Models\User;
use Webkul\Support\Enums\UOMType;

class UOMSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            if (! Schema::hasTable('unit_of_measures')) {
                $this->command?->warn('Skipping UOMSeeder: Table unit_of_measures does not exist.');

                return;
            }

            if (! Schema::hasTable('users')) {
                $this->command?->warn('Skipping UOMSeeder: Table users does not exist.');

                return;
            }

            $user = User::first();

            DB::table('unit_of_measures')->insert([
                [
                    'type'        => UOMType::REFERENCE,
                    'name'        => 'Units',
                    'category_id' => 1,
                    'factor'      => 1.0,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::BIGGER,
                    'name'        => 'Dozens',
                    'category_id' => 1,
                    'factor'      => 0.08333333333333333,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::REFERENCE,
                    'name'        => 'kg',
                    'category_id' => 2,
                    'factor'      => 1.0,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::SMALLER,
                    'name'        => 'g',
                    'category_id' => 2,
                    'factor'      => 1000.0,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::BIGGER,
                    'name'        => 't',
                    'category_id' => 2,
                    'factor'      => 0.001,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::SMALLER,
                    'name'        => 'lb',
                    'category_id' => 2,
                    'factor'      => 2.20462,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::SMALLER,
                    'name'        => 'oz',
                    'category_id' => 2,
                    'factor'      => 35.274,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::REFERENCE,
                    'name'        => 'Days',
                    'category_id' => 3,
                    'factor'      => 8.0,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::BIGGER,
                    'name'        => 'Hours',
                    'category_id' => 3,
                    'factor'      => 1.0,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::REFERENCE,
                    'name'        => 'm',
                    'category_id' => 4,
                    'factor'      => 1.0,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::SMALLER,
                    'name'        => 'mm',
                    'category_id' => 4,
                    'factor'      => 1000.0,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::BIGGER,
                    'name'        => 'km',
                    'category_id' => 4,
                    'factor'      => 0.001,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::SMALLER,
                    'name'        => 'cm',
                    'category_id' => 4,
                    'factor'      => 100.0,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::SMALLER,
                    'name'        => 'in',
                    'category_id' => 4,
                    'factor'      => 39.3701,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::SMALLER,
                    'name'        => 'ft',
                    'category_id' => 4,
                    'factor'      => 3.28084,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::SMALLER,
                    'name'        => 'yd',
                    'category_id' => 4,
                    'factor'      => 1.09361,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::BIGGER,
                    'name'        => 'mi',
                    'category_id' => 4,
                    'factor'      => 1.09361,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::REFERENCE,
                    'name'        => 'm²',
                    'category_id' => 5,
                    'factor'      => 1.0,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::SMALLER,
                    'name'        => 'ft²',
                    'category_id' => 5,
                    'factor'      => 10.76391,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::REFERENCE,
                    'name'        => 'L',
                    'category_id' => 6,
                    'factor'      => 1.0,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::BIGGER,
                    'name'        => 'm³',
                    'category_id' => 6,
                    'factor'      => 0.001,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::SMALLER,
                    'name'        => 'fl oz (US)',
                    'category_id' => 6,
                    'factor'      => 33.814,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::SMALLER,
                    'name'        => 'qt (US)',
                    'category_id' => 6,
                    'factor'      => 1.05669,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::BIGGER,
                    'name'        => 'gal (US)',
                    'category_id' => 6,
                    'factor'      => 0.26417217685798894,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::SMALLER,
                    'name'        => 'in³',
                    'category_id' => 6,
                    'factor'      => 61.0237,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::BIGGER,
                    'name'        => 'ft³',
                    'category_id' => 6,
                    'factor'      => 0.035314724827664144,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
            ]);
        } catch (Throwable $e) {
            report($e);
        }
    }
}
