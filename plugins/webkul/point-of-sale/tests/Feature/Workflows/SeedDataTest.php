<?php

use Illuminate\Support\Facades\DB;
use Webkul\PointOfSale\Database\Seeders\BillSeeder;
use Webkul\PointOfSale\Database\Seeders\NoteSeeder;
use Webkul\PointOfSale\Services\CatalogLoader;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../inventories/tests/Helpers/InventoryHelper.php';
require_once __DIR__.'/../../Helpers/PosHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    TestBootstrapHelper::ensurePluginInstalled('accounts');
    TestBootstrapHelper::ensurePluginInstalled('point-of-sale');

    InventoryHelper::actingAsAdmin();
});

it('seeds the default coin and bill denominations', function () {
    DB::table('pos_bills')->delete();

    (new BillSeeder)->run();

    $bills = DB::table('pos_bills')->orderBy('value')->get();

    expect($bills)->toHaveCount(13)
        ->and($bills->pluck('name')->all())->toBe(['0.05', '0.10', '0.20', '0.25', '0.50', '1.00', '2.00', '5.00', '10.00', '20.00', '50.00', '100.00', '200.00'])
        ->and($bills->pluck('is_for_all_configs')->map(fn ($isForAllConfigs): bool => (bool) $isForAllConfigs)->unique()->all())->toBe([true]);
});

it('seeds the default note models', function () {
    DB::table('pos_notes')->delete();

    (new NoteSeeder)->run();

    expect(DB::table('pos_notes')->orderBy('sort')->pluck('name')->all())
        ->toBe(['Wait', 'To Serve', 'Emergency', 'No Dressing']);
});

it('does not duplicate seeded records on a second run', function () {
    (new BillSeeder)->run();

    (new NoteSeeder)->run();

    (new BillSeeder)->run();

    (new NoteSeeder)->run();

    expect(DB::table('pos_bills')->count())->toBe(13)
        ->and(DB::table('pos_notes')->count())->toBe(4);
});

it('offers every all-config bill to a terminal without an explicit link', function () {
    (new BillSeeder)->run();

    $warehouse = InventoryHelper::warehouse();

    $config = PosHelper::configWithCashMethod($warehouse);

    $catalog = app(CatalogLoader::class)->load($config);

    expect($catalog['bills'])->toHaveCount(13);
});
