<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource\Pages\CreateTeam;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource\Pages\EditTeam;
use Webkul\Crm\Models\TeamMember;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Sale\Models\Team;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
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

it('adds members with their capacity while creating a team', function () {
    $user = FilamentHelper::actingAs(['view_any_crm_team', 'view_crm_team', 'create_crm_team', 'update_crm_team']);

    Livewire::test(CreateTeam::class)
        ->fillForm([
            'name'       => 'Team With Members',
            'company_id' => Company::query()->value('id'),
            'is_active'  => true,
            'crmMembers' => [
                ['user_id' => $user->id, 'assignment_max' => 45, 'assignment_optout' => false, 'is_active' => true],
            ],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $team = Team::query()->where('name', 'Team With Members')->firstOrFail();

    expect(TeamMember::query()->where('team_id', $team->id)->where('user_id', $user->id)->value('assignment_max'))->toBe(45)
        ->and($team->members()->pluck('users.id')->all())->toBe([$user->id]);
});

it('removes a member from the sales team when it is removed on the edit form', function () {
    $user = FilamentHelper::actingAs(['view_any_crm_team', 'view_crm_team', 'create_crm_team', 'update_crm_team']);

    $team = Team::factory()->create(['company_id' => Company::query()->value('id'), 'is_active' => true]);
    $team->members()->attach($user->id);

    Livewire::test(EditTeam::class, ['record' => $team->getRouteKey()])
        ->fillForm(['crmMembers' => []])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(TeamMember::query()->where('team_id', $team->id)->exists())->toBeFalse()
        ->and($team->members()->exists())->toBeFalse();
});

it('creates a new salesperson for a member row', function () {
    FilamentHelper::actingAs(['view_any_crm_team', 'view_crm_team', 'create_crm_team', 'update_crm_team', 'create_crm_user']);

    $roleId = Role::query()->value('id');
    $companyId = Company::query()->value('id');

    $userId = TeamResource::createSalesperson([
        'name'     => 'Quick Seller',
        'email'    => 'quick.seller@example.test',
        'password' => 'secret123',
        'roles'    => [$roleId],
    ], $companyId);

    $user = User::query()->find($userId);

    expect($user->name)->toBe('Quick Seller')
        ->and($user->is_active)->toBeTrue()
        ->and($user->default_company_id)->toBe($companyId)
        ->and(Hash::check('secret123', $user->password))->toBeTrue()
        ->and($user->roles()->pluck('id')->all())->toBe([$roleId]);
});

it('does not create a salesperson without permission to create users', function () {
    FilamentHelper::actingAs(['view_any_crm_team', 'view_crm_team', 'update_crm_team']);

    TeamResource::createSalesperson([
        'name' => 'Nope', 'email' => 'nope@example.test', 'password' => 'secret123', 'roles' => [],
    ], null);
})->throws(HttpException::class);

it('does not create a salesperson in a company the user cannot use', function () {
    FilamentHelper::actingAs(['view_any_crm_team', 'view_crm_team', 'update_crm_team', 'create_crm_user']);

    $otherCompany = Company::factory()->create();

    TeamResource::createSalesperson([
        'name' => 'Other', 'email' => 'other.company@example.test', 'password' => 'secret123', 'roles' => [],
    ], $otherCompany->id);
})->throws(HttpException::class);

it('only lets a team be created for a company that is switched on', function () {
    $user = FilamentHelper::actingAs(['view_any_crm_team', 'view_crm_team', 'create_crm_team', 'update_crm_team']);

    $otherCompany = Company::factory()->create();
    $user->allowedCompanies()->syncWithoutDetaching([$otherCompany->id]);

    Livewire::test(CreateTeam::class)
        ->fillForm(['name' => 'Other company team', 'company_id' => $otherCompany->id, 'is_active' => true])
        ->call('create')
        ->assertHasFormErrors(['company_id']);

    expect(Team::query()->withoutGlobalScopes()->where('name', 'Other company team')->exists())->toBeFalse();
});
