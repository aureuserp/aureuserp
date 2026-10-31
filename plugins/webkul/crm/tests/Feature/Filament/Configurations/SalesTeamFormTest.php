<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource\Pages\CreateTeam;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource\Pages\EditTeam;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource\Pages\ManageTeamAlias;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource\Pages\ManageTeamMembers;
use Webkul\Crm\Models\TeamMember;
use Webkul\Crm\Settings\CRMSettings;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Sale\Models\Team;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/FilamentHelper.php';

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

function useRuleBasedTeamAssignment(): void
{
    $settings = app(CRMSettings::class);
    $settings->use_auto_assignment = true;
    $settings->save();
}

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

it('opens the create page for a user with every team permission without the record buttons', function () {
    FilamentHelper::actingAs([
        'view_any_crm_team', 'view_crm_team', 'create_crm_team', 'update_crm_team',
        'delete_crm_team', 'delete_any_crm_team', 'restore_crm_team', 'restore_any_crm_team',
        'force_delete_crm_team', 'force_delete_any_crm_team',
    ]);

    Livewire::test(CreateTeam::class)
        ->assertOk()
        ->assertActionDoesNotExist('assignLeads')
        ->assertActionDoesNotExist('delete');
});

it('forbids creating a crm sales team without the crm create permission', function () {
    FilamentHelper::actingAs(['view_any_crm_team', 'create_sale_team']);

    Livewire::test(CreateTeam::class)->assertForbidden();
});

it('adds members with their capacity while creating a team', function () {
    $user = FilamentHelper::actingAs(['view_any_crm_team', 'view_crm_team', 'create_crm_team', 'update_crm_team']);

    useRuleBasedTeamAssignment();

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

it('adds, edits and removes team members from the members tab', function () {
    $user = FilamentHelper::actingAs(['view_any_crm_team', 'view_crm_team', 'create_crm_team', 'update_crm_team']);

    useRuleBasedTeamAssignment();

    $team = Team::factory()->create(['company_id' => Company::query()->value('id'), 'is_active' => true]);

    Livewire::test(ManageTeamMembers::class, ['record' => $team->getRouteKey()])
        ->callTableAction('addMember', data: ['user_id' => $user->id, 'assignment_max' => 25, 'is_active' => true])
        ->assertHasNoTableActionErrors();

    $member = TeamMember::query()->where('team_id', $team->id)->where('user_id', $user->id)->firstOrFail();

    expect($member->assignment_max)->toBe(25)
        ->and($team->members()->whereKey($user->id)->exists())->toBeTrue();

    Livewire::test(ManageTeamMembers::class, ['record' => $team->getRouteKey()])
        ->mountTableAction('edit', $member)
        ->assertTableActionDataSet(['member_name' => $user->name, 'member_email' => $user->email])
        ->setTableActionData(['assignment_max' => 12])
        ->callMountedTableAction()
        ->assertHasNoTableActionErrors();

    expect($member->refresh()->assignment_max)->toBe(12)
        ->and($member->user_id)->toBe($user->id);

    Livewire::test(ManageTeamMembers::class, ['record' => $team->getRouteKey()])
        ->callTableAction('delete', $member);

    expect(TeamMember::query()->where('team_id', $team->id)->exists())->toBeFalse()
        ->and($team->members()->exists())->toBeFalse();
});

it('shows the members as core cards with the lead limit and the status badges', function () {
    $user = FilamentHelper::actingAs(['view_any_crm_team', 'view_crm_team', 'create_crm_team', 'update_crm_team']);

    useRuleBasedTeamAssignment();

    $team = Team::factory()->create(['company_id' => Company::query()->value('id'), 'is_active' => true]);

    Livewire::test(ManageTeamMembers::class, ['record' => $team->getRouteKey()])
        ->callTableAction('addMember', data: ['user_id' => $user->id, 'assignment_max' => 12, 'assignment_optout' => true, 'is_active' => false])
        ->assertHasNoTableActionErrors();

    $member = TeamMember::query()->where('team_id', $team->id)->where('user_id', $user->id)->firstOrFail();

    Livewire::test(ManageTeamMembers::class, ['record' => $team->getRouteKey()])
        ->assertTableColumnExists('user.partner.avatar')
        ->assertTableColumnExists('user.email')
        ->assertTableColumnFormattedStateSet('lead_limit', __('crm::filament/clusters/configurations/resources/team.pages.members.lead-limit', ['assigned' => 0, 'max' => 12]), $member)
        ->assertTableColumnStateSet('assignment_badges', ['inactive', 'skipped'], $member);
});

it('saves the team email alias from its own tab', function () {
    FilamentHelper::actingAs(['view_any_crm_team', 'view_crm_team', 'update_crm_team']);

    $team = Team::factory()->create(['company_id' => Company::query()->value('id'), 'is_active' => true]);

    Livewire::test(ManageTeamAlias::class, ['record' => $team->getRouteKey()])
        ->fillForm(['crmAlias' => ['alias_name' => 'tabbed-sales', 'alias_domain' => 'acme.test', 'alias_contact' => 'everyone']])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($team->refresh()->crmAlias?->alias_name)->toBe('tabbed-sales');
});

it('shows the team record tabs and assign leads and delete as buttons without an actions menu', function () {
    FilamentHelper::actingAs(['view_any_crm_team', 'view_crm_team', 'create_crm_team', 'update_crm_team', 'delete_crm_team']);

    $team = Team::factory()->create(['company_id' => Company::query()->value('id'), 'is_active' => true]);

    $component = Livewire::test(EditTeam::class, ['record' => $team->getRouteKey()]);

    $labels = collect($component->instance()->getRecordNavigationTabs())->map(fn ($tab): string => $tab->getLabel())->all();

    $members = collect($component->instance()->getRecordNavigationTabs())->first(fn ($tab): bool => $tab->getLabel() === __('crm::filament/assignment.members.title'));

    expect($members?->getBadge())->toBe('0')
        ->and($labels)->toContain(__('crm::filament/assignment.members.title'))
        ->toContain(__('crm::filament/clusters/configurations/resources/team.pages.alias.navigation'));

    $component->assertActionExists('assignLeads')
        ->assertActionVisible('delete')
        ->assertActionDoesNotExist('duplicateTeam')
        ->assertActionDoesNotExist('archiveTeam')
        ->assertActionDoesNotExist('forceDelete');
});

it('creates a new salesperson for a member row', function () {
    FilamentHelper::actingAs(['view_any_crm_team', 'view_crm_team', 'create_crm_team', 'update_crm_team', 'create_security_user']);

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
    $user = FilamentHelper::actingAs(['view_any_crm_team', 'view_crm_team', 'update_crm_team', 'create_security_user']);

    $otherCompany = Company::factory()->create();
    $user->allowedCompanies()->detach($otherCompany->id);

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
