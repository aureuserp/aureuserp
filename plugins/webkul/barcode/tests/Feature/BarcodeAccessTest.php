<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';

beforeEach(function () {
    foreach (['products', 'inventories', 'barcode'] as $plugin) {
        TestBootstrapHelper::ensurePluginInstalled($plugin);
    }

    foreach (['products', 'inventories', 'barcode'] as $plugin) {
        DB::table('plugins')->updateOrInsert(
            ['name' => $plugin],
            ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
        );
    }

    Package::$plugins = Plugin::all()->keyBy('name');

    if (! Route::has('barcode.dashboard')) {
        require base_path('plugins/webkul/barcode/routes/web.php');

        Route::getRoutes()->refreshNameLookups();
        Route::getRoutes()->refreshActionLookups();
    }

});

it('redirects guests to the barcode login', function () {
    $this->get(route('barcode.dashboard'))->assertRedirect(route('barcode.login'));
    $this->get(route('barcode.adjustments'))->assertRedirect(route('barcode.login'));
});

it('forbids the barcode app without the page permission', function () {
    FilamentHelper::actingAs([])->forceFill(['is_active' => true])->saveQuietly();

    $this->get(route('barcode.dashboard'))->assertForbidden();
    $this->get(route('barcode.adjustments'))->assertForbidden();
});

it('allows the barcode app with the page permission', function () {
    FilamentHelper::actingAs(['page_barcode_launch_barcode'])->forceFill(['is_active' => true])->saveQuietly();

    $this->get(route('barcode.dashboard'))->assertOk();
    $this->get(route('barcode.adjustments'))->assertOk();
});
