<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Webkul\Crm\Console\Commands\AssignLeads;
use Webkul\Crm\Filament\Clusters\Configurations\Pages\ManageSettings;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource;
use Webkul\Crm\Models\CrmTeam;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\TeamMember;
use Webkul\Crm\Services\AssignmentRules;
use Webkul\Crm\Services\LeadAssignment;
use Webkul\Crm\Services\TeamMembership;
use Webkul\Crm\Settings\CrmSettings;
use Webkul\Sale\Models\Team;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Country;

require_once __DIR__.'/../../Helpers/CrmTestHelper.php';

beforeEach(function () {
    CrmTestHelper::bootstrap();

    $settings = app(CrmSettings::class);
    $settings->multi_teams = false;
    $settings->use_auto_assignment = true;
    $settings->auto_assignment_action = 'manual';
    $settings->auto_assignment_interval_number = 1;
    $settings->auto_assignment_interval_type = 'days';
    $settings->auto_assignment_run_at = null;
    $settings->save();

    CrmTeam::query()->update(['assignment_optout' => true]);
});

function assignmentSeller(string $name = 'Seller'): User
{
    return User::withoutEvents(fn (): User => User::factory()->create(['name' => $name, 'default_company_id' => CrmTestHelper::companyId(), 'is_active' => true]));
}

function assignmentTeam(array $rules = []): Team
{
    $team = Team::factory()->create(['company_id' => CrmTestHelper::companyId(), 'is_active' => true]);

    app(TeamMembership::class)->sync($team);

    CrmTeam::query()->where('team_id', $team->id)->update(['assignment_optout' => false, 'assignment_domain' => $rules ? json_encode($rules) : null]);

    return $team->fresh();
}

function assignmentLead(array $attributes = []): Lead
{
    $lead = CrmTestHelper::lead($attributes);

    $lead->forceFill(['user_id' => null, 'team_id' => null])->saveQuietly();

    return $lead->fresh();
}

describe('settings page', function () {
    it('shows multi teams and rule-based assignment with its running options', function () {
        CrmTestHelper::actingAs(['page_crm_manage_settings']);

        Livewire::test(ManageSettings::class)
            ->assertSuccessful()
            ->assertFormFieldExists('multi_teams')
            ->assertFormFieldExists('use_auto_assignment')
            ->fillForm(['use_auto_assignment' => true, 'auto_assignment_action' => 'auto', 'auto_assignment_interval_number' => 2, 'auto_assignment_interval_type' => 'hours'])
            ->call('save')
            ->assertHasNoFormErrors();

        $settings = app(CrmSettings::class)->refresh();

        expect($settings->auto_assignment_action)->toBe('auto')
            ->and($settings->auto_assignment_run_at)->not->toBeNull();
    });

    it('clears the next run when assignment is manual', function () {
        CrmTestHelper::actingAs(['page_crm_manage_settings']);

        Livewire::test(ManageSettings::class)
            ->fillForm(['use_auto_assignment' => true, 'auto_assignment_action' => 'manual'])
            ->call('save');

        expect(app(CrmSettings::class)->refresh()->auto_assignment_run_at)->toBeNull();
    });

    it('is forbidden without its page permission', function () {
        CrmTestHelper::actingAs([]);

        Livewire::test(ManageSettings::class)->assertForbidden();
    });
});

describe('team members', function () {
    it('syncs crm members with the sales team members', function () {
        CrmTestHelper::actingAs();

        $team = assignmentTeam();
        $user = assignmentSeller();

        $team->members()->attach($user->id);
        app(TeamMembership::class)->sync($team);

        expect(TeamMember::query()->where('team_id', $team->id)->where('user_id', $user->id)->exists())->toBeTrue();

        $team->members()->detach($user->id);
        app(TeamMembership::class)->sync($team);

        expect(TeamMember::query()->where('team_id', $team->id)->where('user_id', $user->id)->exists())->toBeFalse();
    });

    it('removes a salesperson from other teams when multi teams is off', function () {
        CrmTestHelper::actingAs();

        $first = assignmentTeam();
        $second = assignmentTeam();
        $user = assignmentSeller();

        app(TeamMembership::class)->addMember($first, $user->id);
        $removed = app(TeamMembership::class)->addMember($second, $user->id);

        expect($removed)->toBe([$first->name])
            ->and(DB::table('sales_team_members')->where('user_id', $user->id)->pluck('team_id')->all())->toBe([$second->id]);
    });

    it('keeps a salesperson in several teams when multi teams is on', function () {
        CrmTestHelper::actingAs();

        $settings = app(CrmSettings::class);
        $settings->multi_teams = true;
        $settings->save();

        $first = assignmentTeam();
        $second = assignmentTeam();
        $user = assignmentSeller();

        app(TeamMembership::class)->addMember($first, $user->id);

        expect(app(TeamMembership::class)->addMember($second, $user->id))->toBe([])
            ->and(DB::table('sales_team_members')->where('user_id', $user->id)->count())->toBe(2);
    });
});

describe('assignment rule conditions', function () {
    it('matches leads on field conditions, all or any', function () {
        CrmTestHelper::actingAs();

        $lead = assignmentLead(['probability' => 30, 'email' => 'Buyer@Example.com', 'city' => null]);

        $condition = fn (string $field, string $operator, mixed $value = null): array => ['field' => $field, 'operator' => $operator, 'value' => $value];

        expect(AssignmentRules::matches($lead, ['conditions' => [$condition('probability', '>=', 20)]]))->toBeTrue()
            ->and(AssignmentRules::matches($lead, ['conditions' => [$condition('probability', '>=', 40)]]))->toBeFalse()
            ->and(AssignmentRules::matches($lead, ['conditions' => [$condition('email', 'contains', 'example')]]))->toBeTrue()
            ->and(AssignmentRules::matches($lead, ['conditions' => [$condition('email', 'not_contains', 'example')]]))->toBeFalse()
            ->and(AssignmentRules::matches($lead, ['conditions' => [$condition('city', 'not_set')]]))->toBeTrue()
            ->and(AssignmentRules::matches($lead, ['conditions' => [$condition('type', 'in', [Lead::TYPE_OPPORTUNITY])]]))->toBeFalse()
            ->and(AssignmentRules::matches($lead, ['conditions' => [$condition('probability', '>=', 40), $condition('city', 'not_set')]]))->toBeFalse()
            ->and(AssignmentRules::matches($lead, ['match' => 'any', 'conditions' => [$condition('probability', '>=', 40), $condition('city', 'not_set')]]))->toBeTrue();
    });

    it('gives the same result in a lead query as for a single lead', function () {
        CrmTestHelper::actingAs();

        $high = assignmentLead(['probability' => 50]);
        $low = assignmentLead(['probability' => 10]);

        $rules = ['conditions' => [['field' => 'probability', 'operator' => '>=', 'value' => '20']]];

        $matchingIds = AssignmentRules::apply(Lead::query()->whereKey([$high->id, $low->id]), $rules)->pluck('id')->all();

        expect($matchingIds)->toBe([$high->id])
            ->and(AssignmentRules::matches($high, $rules))->toBeTrue()
            ->and(AssignmentRules::matches($low, $rules))->toBeFalse();
    });

    it('drops incomplete conditions and unknown fields when saved', function () {
        CrmTestHelper::actingAs();

        $team = assignmentTeam();

        $crmTeam = CrmTeam::query()->where('team_id', $team->id)->first();
        $crmTeam->update(['assignment_domain' => [
            'match'      => 'all',
            'conditions' => [
                'item-1' => ['field' => 'probability', 'operator' => '>=', 'value' => '20'],
                'item-2' => ['field' => 'email', 'operator' => 'contains', 'value' => ''],
                'item-3' => ['field' => 'password', 'operator' => '=', 'value' => 'x'],
            ],
        ]]);

        expect($crmTeam->fresh()->assignment_domain)->toBe([
            'match'      => 'all',
            'conditions' => [['field' => 'probability', 'operator' => '>=', 'value' => 20]],
        ]);

        $crmTeam->update(['assignment_domain' => ['conditions' => []]]);

        expect($crmTeam->fresh()->assignment_domain)->toBeNull();
    });

    it('assigns only leads matching the team conditions', function () {
        CrmTestHelper::actingAs();

        $team = assignmentTeam(['conditions' => [['field' => 'probability', 'operator' => '>=', 'value' => 20]]]);
        app(TeamMembership::class)->addMember($team, assignmentSeller()->id);

        $likely = assignmentLead(['probability' => 60]);
        $unlikely = assignmentLead(['probability' => 5]);

        app(LeadAssignment::class)->run();

        expect($likely->fresh()->team_id)->toBe($team->id)
            ->and($unlikely->fresh()->team_id)->toBeNull();
    });
});

describe('rule-based assignment', function () {
    it('matches leads against assignment rules', function () {
        CrmTestHelper::actingAs();

        $us = Country::query()->where('code', 'US')->value('id');

        $lead = assignmentLead(['country_id' => $us]);

        expect(AssignmentRules::matches($lead, null))->toBeTrue()
            ->and(AssignmentRules::matches($lead, ['country_ids' => [$us]]))->toBeTrue()
            ->and(AssignmentRules::matches($lead, ['country_ids' => [$us + 1]]))->toBeFalse()
            ->and(AssignmentRules::matches($lead, ['lead_types' => [Lead::TYPE_OPPORTUNITY]]))->toBeFalse();
    });

    it('assigns matching leads to the team and spreads them by member capacity', function () {
        CrmTestHelper::actingAs();

        $us = Country::query()->where('code', 'US')->value('id');
        $fr = Country::query()->where('code', 'FR')->value('id');

        $team = assignmentTeam(['country_ids' => [$us]]);
        $big = assignmentSeller('Big');
        $small = assignmentSeller('Small');

        app(TeamMembership::class)->addMember($team, $big->id, ['assignment_max' => 2]);
        app(TeamMembership::class)->addMember($team, $small->id, ['assignment_max' => 1]);

        $usLeads = collect(range(1, 3))->map(fn () => assignmentLead(['country_id' => $us]));
        $frLead = assignmentLead(['country_id' => $fr]);

        app(LeadAssignment::class)->run();

        $usLeads = $usLeads->map->fresh();

        expect($usLeads->pluck('team_id')->unique()->all())->toBe([$team->id])
            ->and($usLeads->where('user_id', $big->id))->toHaveCount(2)
            ->and($usLeads->where('user_id', $small->id))->toHaveCount(1)
            ->and($frLead->fresh()->team_id)->toBeNull();
    });

    it('respects the 30-day capacity, paused members and skipped teams', function () {
        CrmTestHelper::actingAs();

        $team = assignmentTeam();
        $paused = assignmentSeller('Paused');
        $active = assignmentSeller('Active');

        app(TeamMembership::class)->addMember($team, $paused->id, ['assignment_max' => 10, 'assignment_optout' => true]);
        app(TeamMembership::class)->addMember($team, $active->id, ['assignment_max' => 1]);

        $first = assignmentLead(['team_id' => $team->id]);
        $second = assignmentLead(['team_id' => $team->id]);
        $first->forceFill(['team_id' => $team->id])->saveQuietly();
        $second->forceFill(['team_id' => $team->id])->saveQuietly();

        app(LeadAssignment::class)->run($team);

        expect(collect([$first->fresh(), $second->fresh()])->where('user_id', $active->id))->toHaveCount(1)
            ->and(collect([$first->fresh(), $second->fresh()])->where('user_id', $paused->id))->toHaveCount(0);

        CrmTeam::query()->where('team_id', $team->id)->update(['assignment_optout' => true]);

        $third = assignmentLead();

        app(LeadAssignment::class)->run();

        expect($third->fresh()->team_id)->toBeNull();
    });

    it('does not assign lost or already-owned leads to a team', function () {
        $owner = CrmTestHelper::actingAs();

        $team = assignmentTeam();
        app(TeamMembership::class)->addMember($team, assignmentSeller()->id);

        $lost = assignmentLead();
        $lost->markAsLost(null);

        $owned = CrmTestHelper::lead(['team_id' => null, 'user_id' => $owner->id]);
        $owned->forceFill(['team_id' => null])->saveQuietly();

        app(LeadAssignment::class)->run();

        expect(Lead::withTrashed()->find($lost->id)->team_id)->toBeNull()
            ->and($owned->fresh()->team_id)->toBeNull();
    });

    it('runs from the command only when repeated assignment is due', function () {
        CrmTestHelper::actingAs();

        $settings = app(CrmSettings::class);
        $settings->auto_assignment_action = 'auto';
        $settings->auto_assignment_run_at = now()->addHour()->toIso8601String();
        $settings->save();

        Artisan::call('crm:assign-leads', ['--scheduled' => true]);

        expect(app(CrmSettings::class)->refresh()->auto_assignment_run_at)->toBe($settings->auto_assignment_run_at);

        $settings->auto_assignment_run_at = now()->subMinute()->toIso8601String();
        $settings->save();

        Artisan::call('crm:assign-leads', ['--scheduled' => true]);

        expect(Carbon::parse(app(CrmSettings::class)->refresh()->auto_assignment_run_at)->isFuture())->toBeTrue();
    });

    it('plans the next run from the interval', function () {
        $settings = app(CrmSettings::class);
        $settings->auto_assignment_interval_number = 3;
        $settings->auto_assignment_interval_type = 'hours';

        $from = now()->startOfHour();

        expect(AssignLeads::nextRun($settings, $from)->equalTo($from->copy()->addHours(3)))->toBeTrue();
    });
});

it('hides assignment on the sales team and does not assign when rule-based assignment is off', function () {
    CrmTestHelper::actingAs();

    $settings = app(CrmSettings::class);
    $settings->use_auto_assignment = false;
    $settings->save();

    expect(TeamResource::usesRuleBasedAssignment())->toBeFalse();

    $team = assignmentTeam();
    app(TeamMembership::class)->addMember($team, assignmentSeller()->id);

    $lead = assignmentLead(['team_id' => $team->id]);
    $lead->forceFill(['team_id' => $team->id])->saveQuietly();

    expect(app(LeadAssignment::class)->run($team))->toBe(['teams' => 0, 'salespeople' => 0])
        ->and($lead->fresh()->user_id)->toBeNull();
});
