<?php

use Livewire\Livewire;
use Webkul\Crm\Filament\Clusters\Configurations;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource\Pages\EditTeam;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource\Pages\ListTeams;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource\Pages\ViewTeam;
use Webkul\Crm\Policies\TeamPolicy;
use Webkul\Sale\Models\Team;

require_once __DIR__.'/../../../Helpers/CrmTestHelper.php';

beforeEach(fn () => CrmTestHelper::bootstrap());

it('lists sales teams with the crm team permission', function () {
    CrmTestHelper::actingAsCrmUser(['team']);

    $team = Team::factory()->create([
        'name'       => 'Direct Sales (crm test)',
        'company_id' => CrmTestHelper::companyId(),
        'is_active'  => true,
    ]);

    Livewire::test(ListTeams::class)
        ->assertCanSeeTableRecords([$team]);
});

it('shows a sales team', function () {
    CrmTestHelper::actingAsCrmUser(['team']);

    $team = Team::factory()->create([
        'name'       => 'Inside Sales (crm test)',
        'company_id' => CrmTestHelper::companyId(),
    ]);

    Livewire::test(ViewTeam::class, ['record' => $team->getRouteKey()])
        ->assertSee('Inside Sales (crm test)');
});

it('edits a sales team', function () {
    CrmTestHelper::actingAsCrmUser(['team']);

    $team = Team::factory()->create([
        'name'       => 'Field Sales (crm test)',
        'company_id' => CrmTestHelper::companyId(),
        'is_active'  => true,
    ]);

    Livewire::test(EditTeam::class, ['record' => $team->getRouteKey()])
        ->assertSchemaStateSet(['name' => 'Field Sales (crm test)'])
        ->fillForm([
            'name'      => 'Field Sales Europe (crm test)',
            'is_active' => false,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $team->refresh();

    expect($team->name)->toBe('Field Sales Europe (crm test)')
        ->and((bool) $team->is_active)->toBeFalse();
});

it('shows the configuration menu on the sales team record pages', function () {
    CrmTestHelper::actingAs([
        ...CrmTestHelper::permissionsFor(['team']),
        'view_any_crm_stage',
        'view_any_crm_tag',
    ]);

    $team = Team::factory()->create(['company_id' => CrmTestHelper::companyId()]);

    $component = Livewire::test(ViewTeam::class, ['record' => $team->getRouteKey()]);

    expect(count($component->instance()->getSubNavigation()))->toBeGreaterThan(1);
});

it('forbids the crm sales teams with only the sales team permissions', function () {
    CrmTestHelper::actingAs([
        'view_any_sale_team',
        'view_sale_team',
        'update_sale_team',
    ]);

    $team = Team::factory()->create(['company_id' => CrmTestHelper::companyId()]);

    Livewire::test(ListTeams::class)->assertForbidden();
    Livewire::test(ViewTeam::class, ['record' => $team->getRouteKey()])->assertForbidden();
    Livewire::test(EditTeam::class, ['record' => $team->getRouteKey()])->assertForbidden();
});

it('forbids editing a sales team without the crm update permission', function () {
    CrmTestHelper::actingAs(['view_any_crm_team', 'view_crm_team', 'update_sale_team']);

    $team = Team::factory()->create(['company_id' => CrmTestHelper::companyId()]);

    Livewire::test(EditTeam::class, ['record' => $team->getRouteKey()])->assertForbidden();
});

it('authorizes the sales teams resource against the crm team policy', function () {
    expect(TeamResource::getCrmPolicy())->toBe(TeamPolicy::class)
        ->and(TeamResource::getCluster())->toBe(Configurations::class)
        ->and(TeamResource::getNavigationSort())->toBe(1)
        ->and(TeamResource::getModelLabel())->toBe(__('crm::filament/clusters/configurations/resources/team.model-label'))
        ->and(TeamResource::getPluralModelLabel())->toBe(__('crm::filament/clusters/configurations/resources/team.plural-model-label'))
        ->and(TeamResource::getNavigationLabel())->toBe(__('crm::filament/clusters/configurations/resources/team.navigation.title'));
});
