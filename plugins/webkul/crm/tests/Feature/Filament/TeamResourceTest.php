<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource\Pages\CreateTeam;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Sale\Models\Team;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('crm');

    foreach (['sales', 'crm'] as $plugin) {
        DB::table('plugins')->updateOrInsert(
            ['name' => $plugin],
            ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
        );
    }

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');
});

it('creates a sales team from the crm configuration and redirects inside crm', function () {
    $user = FilamentHelper::actingAs(['view_any_crm_team', 'view_crm_team', 'create_crm_team', 'update_crm_team']);

    $component = Livewire::test(CreateTeam::class)
        ->fillForm([
            'name'       => 'CRM Team',
            'company_id' => Company::query()->value('id'),
            'user_id'    => $user->id,
            'is_active'  => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $component->assertRedirect();

    expect(Team::query()->where('name', 'CRM Team')->where('user_id', $user->id)->exists())->toBeTrue();
});

it('forbids creating a crm sales team without the crm create permission', function () {
    FilamentHelper::actingAs(['view_any_crm_team', 'create_sale_team']);

    Livewire::test(CreateTeam::class)->assertForbidden();
});
