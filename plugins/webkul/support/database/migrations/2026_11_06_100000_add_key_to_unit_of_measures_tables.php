<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['unit_of_measure_categories', 'unit_of_measures'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->string('key')->nullable()->unique();
            });
        }

        $categoryIds = [];

        foreach (array_keys(trans('support::seeders.uom-categories', [], 'en')) as $index => $key) {
            $categoryId = $index + 1;

            $updated = DB::table('unit_of_measure_categories')
                ->where('id', $categoryId)
                ->where('name', trans("support::seeders.uom-categories.{$key}", [], 'en'))
                ->whereNull('key')
                ->update(['key' => "uom-category.{$key}"]);

            if ($updated) {
                $categoryIds[] = $categoryId;
            }
        }

        foreach (trans('support::seeders.uoms', [], 'en') as $key => $name) {
            $ids = DB::table('unit_of_measures')
                ->where('name', $name)
                ->whereIn('category_id', $categoryIds)
                ->whereNull('key')
                ->pluck('id');

            if ($ids->count() === 1) {
                DB::table('unit_of_measures')->where('id', $ids->first())->update(['key' => "uom.{$key}"]);
            }
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
};
