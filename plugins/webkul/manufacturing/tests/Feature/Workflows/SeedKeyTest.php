<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Webkul\Inventory\Enums\ManufactureStep;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../inventories/tests/Helpers/InventoryHelper.php';
require_once __DIR__.'/../../Helpers/ManufacturingHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    TestBootstrapHelper::ensurePluginInstalled('manufacturing');

    foreach (['inventories', 'manufacturing'] as $plugin) {
        DB::table('plugins')->updateOrInsert(
            ['name' => $plugin],
            ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
        );
    }

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    ManufacturingHelper::actingAsAdmin();
});

it('restores manufacturing rule keys from the warehouse structure when names are translated', function () {
    $otherCompany = Company::factory()->create();

    $warehouse = ManufacturingHelper::multiStepWarehouse(ManufactureStep::THREE_STEPS);

    $expected = DB::table('inventories_rules')
        ->where('warehouse_id', $warehouse->id)
        ->whereIn('key', ['rule.stock-pre-production', 'rule.pre-production-production', 'rule.post-production-stock'])
        ->pluck('key', 'id')
        ->all();

    foreach (array_keys($expected) as $id) {
        DB::table('inventories_rules')->where('id', $id)->update(['key' => null, 'name' => "Nom {$id}"]);
    }

    (require __DIR__.'/../../../database/migrations/2026_11_06_100000_backfill_manufacturing_keys.php')->backfill();

    expect(DB::table('inventories_locations')->where('key', 'location.production')->where('company_id', $otherCompany->id)->exists())->toBeTrue()
        ->and($expected)->toHaveCount(3)
        ->and(DB::table('inventories_rules')->whereIn('id', array_keys($expected))->pluck('key', 'id')->all())->toEqual($expected);
});
