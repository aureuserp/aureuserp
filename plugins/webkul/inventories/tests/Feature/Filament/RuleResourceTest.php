<?php

use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\RuleResource\Pages\EditRule;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\RuleResource\Pages\ViewRule;
use Webkul\Inventory\Models\Route;
use Webkul\Inventory\Models\Rule;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';

beforeEach(function () {
    foreach (['products', 'inventories'] as $plugin) {
        TestBootstrapHelper::ensurePluginInstalled($plugin);

        DB::table('plugins')->updateOrInsert(
            ['name' => $plugin],
            ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
        );
    }

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    FilamentHelper::actingAs(['view_any_inventory_rule', 'view_inventory_rule', 'update_inventory_rule']);
});

function ruleRouteSelect(Rule $rule): Select
{
    return Livewire::test(EditRule::class, ['record' => $rule->id])
        ->instance()
        ->form
        ->getFlatFields(withHidden: true)['route_id'];
}

it('labels a deleted route in the current locale and disables it by its trashed state', function () {
    $route = Route::factory()->create(['name' => 'Seasonal Route']);

    $rule = Rule::factory()->create(['route_id' => $route->id]);

    $route->delete();

    app()->setLocale('fr');

    $select = ruleRouteSelect($rule);

    $label = $select->getOptionLabelFromRecord($route->fresh());

    expect($label)->toBe('Seasonal Route '.__('support::support.trashed-suffix'))
        ->and($label)->not->toContain('(Deleted)')
        ->and($select->isOptionDisabled($route->id, $label))->toBeTrue();
});

it('keeps an active route enabled even when its name ends like the deleted suffix', function () {
    $route = Route::factory()->create(['name' => 'Legacy (Deleted)']);

    $rule = Rule::factory()->create(['route_id' => $route->id]);

    $select = ruleRouteSelect($rule);

    expect($select->isOptionDisabled($route->id, $select->getOptionLabelFromRecord($route)))->toBeFalse();
});

it('shows the lead time with a translated day unit', function () {
    $rule = Rule::factory()->create(['delay' => 3]);

    app()->setLocale('fr');

    Livewire::test(ViewRule::class, ['record' => $rule->id])
        ->assertOk()
        ->assertSee(trans_choice('inventories::filament/clusters/configurations/resources/rule.infolist.sections.general.entries.lead-time-days', 3, ['count' => 3]));
});
