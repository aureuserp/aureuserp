<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Webkul\Product\Enums\ProductType;
use Webkul\Product\Filament\Resources\ProductResource;
use Webkul\Support\Database\Seeders\UOMCategorySeeder;
use Webkul\Support\Database\Seeders\UOMSeeder;
use Webkul\Support\Models\UOM;

require_once __DIR__.'/../../Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('products');
});

afterEach(fn () => App::setLocale('en'));

it('gives every seeded unit and unit category a unique key', function () {
    foreach (['unit_of_measure_categories' => 6, 'unit_of_measures' => 26] as $table => $count) {
        $keys = DB::table($table)->whereNotNull('key')->pluck('key');

        expect($keys)->toHaveCount($count)
            ->and($keys->unique())->toHaveCount($count);
    }
});

it('seeds missing units with names in the active locale', function () {
    DB::table('unit_of_measures')->where('key', 'uom.days')->delete();

    App::setLocale('fr');

    (new UOMSeeder)->run();

    expect(UOM::where('key', 'uom.days')->value('name'))->toBe('Jours');
});

it('keeps renamed units and categories when the seeders run again', function () {
    UOM::where('key', 'uom.units')->update(['name' => 'Pieces']);

    DB::table('unit_of_measure_categories')->where('key', 'uom-category.unit')->update(['name' => 'Count']);

    (new UOMCategorySeeder)->run();

    (new UOMSeeder)->run();

    expect(UOM::where('key', 'uom.units')->value('name'))->toBe('Pieces')
        ->and(DB::table('unit_of_measure_categories')->where('key', 'uom-category.unit')->value('name'))->toBe('Count')
        ->and(UOM::where('key', 'uom.units')->count())->toBe(1);
});

it('resolves the default units by key after they are renamed', function () {
    UOM::where('key', 'uom.units')->update(['name' => 'Pieces']);

    UOM::where('key', 'uom.hours')->update(['name' => 'Heures']);

    expect(ProductResource::getDefaultUomIdByProductType(ProductType::GOODS))->toBe(UOM::where('key', 'uom.units')->value('id'))
        ->and(ProductResource::getDefaultUomIdByProductType(ProductType::SERVICE))->toBe(UOM::where('key', 'uom.hours')->value('id'));
});

it('restores unit keys from the seeded ids when the names are translated', function () {
    $expected = [];

    foreach (['unit_of_measure_categories', 'unit_of_measures'] as $table) {
        $expected[$table] = DB::table($table)->whereNotNull('key')->pluck('key', 'id')->all();

        foreach (array_keys($expected[$table]) as $id) {
            DB::table($table)->where('id', $id)->update(['key' => null, 'name' => "Nom {$id}"]);
        }
    }

    DB::table('unit_of_measures')->whereIn('id', array_keys($expected['unit_of_measures'], 'uom.hours'))->update(['type' => 'reference', 'factor' => 1]);

    DB::table('unit_of_measures')->whereIn('id', array_keys($expected['unit_of_measures'], 'uom.days'))->update(['type' => 'bigger', 'factor' => 0.125]);

    (require __DIR__.'/../../../database/migrations/2026_11_06_100000_add_key_to_unit_of_measures_tables.php')->backfill();

    expect($expected['unit_of_measure_categories'])->toHaveCount(6)
        ->and($expected['unit_of_measures'])->toHaveCount(26);

    foreach ($expected as $table => $keys) {
        expect(DB::table($table)->whereIn('id', array_keys($keys))->pluck('key', 'id')->all())->toEqual($keys);
    }
});
