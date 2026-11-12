<?php

namespace Webkul\Support\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
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

            seed_rows('unit_of_measures', [
                [
                    'type'        => UOMType::REFERENCE,
                    'key'         => 'uom.units',
                    'name'        => __('support::seeders.uoms.units'),
                    'category_id' => 1,
                    'factor'      => 1.0,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::BIGGER,
                    'key'         => 'uom.dozens',
                    'name'        => __('support::seeders.uoms.dozens'),
                    'category_id' => 1,
                    'factor'      => '0.08333333333333333',
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::REFERENCE,
                    'key'         => 'uom.kg',
                    'name'        => __('support::seeders.uoms.kg'),
                    'category_id' => 2,
                    'factor'      => 1.0,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::SMALLER,
                    'key'         => 'uom.g',
                    'name'        => __('support::seeders.uoms.g'),
                    'category_id' => 2,
                    'factor'      => 1000.0,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::BIGGER,
                    'key'         => 'uom.t',
                    'name'        => __('support::seeders.uoms.t'),
                    'category_id' => 2,
                    'factor'      => 0.001,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::SMALLER,
                    'key'         => 'uom.lb',
                    'name'        => __('support::seeders.uoms.lb'),
                    'category_id' => 2,
                    'factor'      => 2.20462,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::SMALLER,
                    'key'         => 'uom.oz',
                    'name'        => __('support::seeders.uoms.oz'),
                    'category_id' => 2,
                    'factor'      => 35.274,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::REFERENCE,
                    'key'         => 'uom.days',
                    'name'        => __('support::seeders.uoms.days'),
                    'category_id' => 3,
                    'factor'      => 8.0,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::BIGGER,
                    'key'         => 'uom.hours',
                    'name'        => __('support::seeders.uoms.hours'),
                    'category_id' => 3,
                    'factor'      => 1.0,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::REFERENCE,
                    'key'         => 'uom.m',
                    'name'        => __('support::seeders.uoms.m'),
                    'category_id' => 4,
                    'factor'      => 1.0,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::SMALLER,
                    'key'         => 'uom.mm',
                    'name'        => __('support::seeders.uoms.mm'),
                    'category_id' => 4,
                    'factor'      => 1000.0,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::BIGGER,
                    'key'         => 'uom.km',
                    'name'        => __('support::seeders.uoms.km'),
                    'category_id' => 4,
                    'factor'      => 0.001,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::SMALLER,
                    'key'         => 'uom.cm',
                    'name'        => __('support::seeders.uoms.cm'),
                    'category_id' => 4,
                    'factor'      => 100.0,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::SMALLER,
                    'key'         => 'uom.in',
                    'name'        => __('support::seeders.uoms.in'),
                    'category_id' => 4,
                    'factor'      => 39.3701,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::SMALLER,
                    'key'         => 'uom.ft',
                    'name'        => __('support::seeders.uoms.ft'),
                    'category_id' => 4,
                    'factor'      => 3.28084,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::SMALLER,
                    'key'         => 'uom.yd',
                    'name'        => __('support::seeders.uoms.yd'),
                    'category_id' => 4,
                    'factor'      => 1.09361,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::BIGGER,
                    'key'         => 'uom.mi',
                    'name'        => __('support::seeders.uoms.mi'),
                    'category_id' => 4,
                    'factor'      => 1.09361,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::REFERENCE,
                    'key'         => 'uom.m2',
                    'name'        => __('support::seeders.uoms.m2'),
                    'category_id' => 5,
                    'factor'      => 1.0,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::SMALLER,
                    'key'         => 'uom.ft2',
                    'name'        => __('support::seeders.uoms.ft2'),
                    'category_id' => 5,
                    'factor'      => 10.76391,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::REFERENCE,
                    'key'         => 'uom.l',
                    'name'        => __('support::seeders.uoms.l'),
                    'category_id' => 6,
                    'factor'      => 1.0,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::BIGGER,
                    'key'         => 'uom.m3',
                    'name'        => __('support::seeders.uoms.m3'),
                    'category_id' => 6,
                    'factor'      => 0.001,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::SMALLER,
                    'key'         => 'uom.fl-oz-us',
                    'name'        => __('support::seeders.uoms.fl-oz-us'),
                    'category_id' => 6,
                    'factor'      => 33.814,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::SMALLER,
                    'key'         => 'uom.qt-us',
                    'name'        => __('support::seeders.uoms.qt-us'),
                    'category_id' => 6,
                    'factor'      => 1.05669,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::BIGGER,
                    'key'         => 'uom.gal-us',
                    'name'        => __('support::seeders.uoms.gal-us'),
                    'category_id' => 6,
                    'factor'      => '0.26417217685798894',
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::SMALLER,
                    'key'         => 'uom.in3',
                    'name'        => __('support::seeders.uoms.in3'),
                    'category_id' => 6,
                    'factor'      => 61.0237,
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
                [
                    'type'        => UOMType::BIGGER,
                    'key'         => 'uom.ft3',
                    'name'        => __('support::seeders.uoms.ft3'),
                    'category_id' => 6,
                    'factor'      => '0.035314724827664144',
                    'rounding'    => 0.01,
                    'creator_id'  => $user?->id,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
            ], 'key');
        } catch (Throwable $e) {
            report($e);
        }
    }
}
