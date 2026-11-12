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
