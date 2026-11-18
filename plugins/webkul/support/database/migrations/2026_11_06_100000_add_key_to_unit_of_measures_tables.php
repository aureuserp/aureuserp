<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected array $categories = [
        'unit'         => 1,
        'weight'       => 2,
        'working-time' => 3,
        'length'       => 4,
        'surface'      => 5,
        'volume'       => 6,
    ];

    protected array $units = [
        'units'    => ['unit', 'reference'],
        'dozens'   => ['unit', 'bigger'],
        'kg'       => ['weight', 'reference'],
        'g'        => ['weight', 'smaller'],
        't'        => ['weight', 'bigger'],
        'lb'       => ['weight', 'smaller'],
        'oz'       => ['weight', 'smaller'],
        'days'     => ['working-time', 'reference'],
        'hours'    => ['working-time', 'bigger'],
        'm'        => ['length', 'reference'],
        'mm'       => ['length', 'smaller'],
        'km'       => ['length', 'bigger'],
        'cm'       => ['length', 'smaller'],
        'in'       => ['length', 'smaller'],
        'ft'       => ['length', 'smaller'],
        'yd'       => ['length', 'smaller'],
        'mi'       => ['length', 'bigger'],
        'm2'       => ['surface', 'reference'],
        'ft2'      => ['surface', 'smaller'],
        'l'        => ['volume', 'reference'],
        'm3'       => ['volume', 'bigger'],
        'fl-oz-us' => ['volume', 'smaller'],
        'qt-us'    => ['volume', 'smaller'],
        'gal-us'   => ['volume', 'bigger'],
        'in3'      => ['volume', 'smaller'],
        'ft3'      => ['volume', 'bigger'],
    ];

    public function up(): void
    {
        foreach (['unit_of_measure_categories', 'unit_of_measures'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->string('key')->nullable()->unique();
            });
        }

        $this->backfill();
    }

    public function backfill(): void
    {
        foreach ($this->categories as $key => $id) {
            DB::table('unit_of_measure_categories')
                ->where('id', $id)
                ->whereNull('key')
                ->update(['key' => "uom-category.{$key}"]);
        }

        foreach (array_keys($this->units) as $index => $key) {
            [$category, $type] = $this->units[$key];

            $this->assignKey(
                DB::table('unit_of_measures')
                    ->where('id', $index + 1)
                    ->where('category_id', $this->categories[$category])
                    ->where('type', $type),
                "uom.{$key}",
            );
        }

        $categoryIds = DB::table('unit_of_measure_categories')->whereNotNull('key')->pluck('id');

        foreach (array_keys($this->units) as $key) {
            $this->assignKey(
                DB::table('unit_of_measures')
                    ->where('name', trans("support::seeders.uoms.{$key}", [], 'en'))
                    ->whereIn('category_id', $categoryIds),
                "uom.{$key}",
            );
        }
    }

    public function down(): void
    {
        foreach (['unit_of_measure_categories', 'unit_of_measures'] as $tableName) {
            if (Schema::hasColumn($tableName, 'key')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropUnique(['key']);

                    $table->dropColumn('key');
                });
            }
        }
    }

    protected function assignKey(Builder $candidates, string $seedKey): void
    {
        if (DB::table('unit_of_measures')->where('key', $seedKey)->exists()) {
            return;
        }

        $ids = $candidates->whereNull('key')->pluck('id');

        if ($ids->count() === 1) {
            DB::table('unit_of_measures')->where('id', $ids->first())->update(['key' => $seedKey]);
        }
    }
};
