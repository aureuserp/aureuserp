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
        'units'    => 'unit',
        'dozens'   => 'unit',
        'kg'       => 'weight',
        'g'        => 'weight',
        't'        => 'weight',
        'lb'       => 'weight',
        'oz'       => 'weight',
        'days'     => 'working-time',
        'hours'    => 'working-time',
        'm'        => 'length',
        'mm'       => 'length',
        'km'       => 'length',
        'cm'       => 'length',
        'in'       => 'length',
        'ft'       => 'length',
        'yd'       => 'length',
        'mi'       => 'length',
        'm2'       => 'surface',
        'ft2'      => 'surface',
        'l'        => 'volume',
        'm3'       => 'volume',
        'fl-oz-us' => 'volume',
        'qt-us'    => 'volume',
        'gal-us'   => 'volume',
        'in3'      => 'volume',
        'ft3'      => 'volume',
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
            $this->assignKey(
                DB::table('unit_of_measures')
                    ->where('id', $index + 1)
                    ->where('category_id', $this->categories[$this->units[$key]]),
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
