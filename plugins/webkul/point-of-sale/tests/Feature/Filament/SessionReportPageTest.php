<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\PointOfSale\Filament\Admin\Clusters\Reporting\Pages\SessionReport;
use Webkul\PointOfSale\Models\Session;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../inventories/tests/Helpers/InventoryHelper.php';
require_once __DIR__.'/../../Helpers/PosHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    TestBootstrapHelper::ensurePluginInstalled('accounts');
    TestBootstrapHelper::ensurePluginInstalled('point-of-sale');

    DB::table('plugins')->whereIn('name', ['inventories', 'accounts', 'point-of-sale'])->update([
        'is_installed' => true,
        'is_active'    => true,
        'updated_at'   => now(),
    ]);

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    FilamentHelper::actingAs(['page_point_of_sale_session_report']);

    $this->warehouse = InventoryHelper::warehouse();
});

it('shows the terminal and date in each session option', function () {
    $session = PosHelper::openSession($this->warehouse);

    $session->update(['started_at' => '2026-01-05 09:00:00']);

    Livewire::test(SessionReport::class)
        ->assertFormFieldExists('sessionId', 'filtersForm', fn ($field) => $field->getOptions()[$session->id]
            === $session->name.' — '.$session->config->name.' — Jan 5, 2026');
});

it('finds a session older than the latest fifty by searching', function () {
    $oldest = PosHelper::openSession($this->warehouse);

    Session::factory()->count(50)->create(['config_id' => $oldest->config_id]);

    Livewire::test(SessionReport::class)
        ->assertFormFieldExists('sessionId', 'filtersForm', fn ($field) => ! array_key_exists($oldest->id, $field->getOptions())
            && array_key_exists($oldest->id, $field->getSearchResults($oldest->name)));
});
