<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Webkul\Crm\Console\Commands\AssignLeads;
use Webkul\Crm\Enums\LeadType;
use Webkul\Crm\Filament\Clusters\Configurations\Resources\TeamResource;
use Webkul\Crm\Filament\Clusters\Settings\Pages\ManageCRM;
use Webkul\Crm\Models\CRMTeam;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\TeamMember;
use Webkul\Crm\Services\AssignmentRules;
use Webkul\Crm\Services\LeadAssignment;
use Webkul\Crm\Services\TeamMembership;
use Webkul\Crm\Settings\CRMSettings;
use Webkul\Sale\Models\Team;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Country;

require_once __DIR__.'/../../Helpers/CRMHelper.php';

beforeEach(function () {
    CRMHelper::bootstrap();

    $settings = app(CRMSettings::class);
    $settings->multi_teams = false;
    $settings->use_auto_assignment = true;
    $settings->auto_assignment_action = 'manual';
    $settings->auto_assignment_interval_number = 1;
    $settings->auto_assignment_interval_type = 'days';
    $settings->auto_assignment_run_at = null;
    $settings->save();

    CRMTeam::query()->update(['assignment_optout' => true]);
});

function assignmentSeller(string $name = 'Seller'): User
{
    return User::withoutEvents(fn (): User => User::factory()->create(['name' => $name, 'default_company_id' => CRMHelper::companyId(), 'is_active' => true]));
}

function assignmentTeam(array $rules = []): Team
{
    $team = Team::factory()->create(['company_id' => CRMHelper::companyId(), 'is_active' => true]);

    app(TeamMembership::class)->sync($team);

    CRMTeam::query()->where('team_id', $team->id)->update(['assignment_optout' => false, 'assignment_domain' => $rules ? json_encode($rules) : null]);

    return $team->fresh();
}

function assignmentLead(array $attributes = []): Lead
{
    $lead = CRMHelper::lead($attributes);

    $lead->forceFill(['user_id' => null, 'team_id' => null])->saveQuietly();

    return $lead->fresh();
}

describe('settings page', function () {
    it('shows multi teams and rule-based assignment with its running options', function () {
        CRMHelper::actingAs(['page_crm_manage_c_r_m']);

        Livewire::test(ManageCRM::class)
            ->assertSuccessful()
            ->assertFormFieldExists('multi_teams')
            ->assertFormFieldExists('use_auto_assignment')
            ->fillForm(['use_auto_assignment' => true, 'auto_assignment_action' => 'auto', 'auto_assignment_interval_number' => 2, 'auto_assignment_interval_type' => 'hours'])
            ->call('save')
            ->assertHasNoFormErrors();

        $settings = app(CRMSettings::class)->refresh();

        expect($settings->auto_assignment_action)->toBe('auto')
            ->and($settings->auto_assignment_run_at)->not->toBeNull();
    });

    it('clears the next run when assignment is manual', function () {
        CRMHelper::actingAs(['page_crm_manage_c_r_m']);

        Livewire::test(ManageCRM::class)
            ->fillForm(['use_auto_assignment' => true, 'auto_assignment_action' => 'manual'])
            ->call('save');

        expect(app(CRMSettings::class)->refresh()->auto_assignment_run_at)->toBeNull();
    });

    it('is forbidden without its page permission', function () {
        CRMHelper::actingAs([]);

        Livewire::test(ManageCRM::class)->assertForbidden();
    });
});

describe('team members', function () {
    it('syncs crm members with the sales team members', function () {
        CRMHelper::actingAs();

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
        CRMHelper::actingAs();

        $first = assignmentTeam();
        $second = assignmentTeam();
        $user = assignmentSeller();

        app(TeamMembership::class)->addMember($first, $user->id);
        $removed = app(TeamMembership::class)->addMember($second, $user->id);

        expect($removed)->toBe([$first->name])
            ->and(DB::table('sales_team_members')->where('user_id', $user->id)->pluck('team_id')->all())->toBe([$second->id]);
    });

    it('keeps a salesperson in several teams when multi teams is on', function () {
        CRMHelper::actingAs();

        $settings = app(CRMSettings::class);
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
        CRMHelper::actingAs();

        $lead = assignmentLead(['probability' => 30, 'email' => 'Buyer@Example.com', 'city' => null]);

        $condition = fn (string $field, string $operator, mixed $value = null): array => ['field' => $field, 'operator' => $operator, 'value' => $value];

        expect(AssignmentRules::matches($lead, ['conditions' => [$condition('probability', '>=', 20)]]))->toBeTrue()
            ->and(AssignmentRules::matches($lead, ['conditions' => [$condition('probability', '>=', 40)]]))->toBeFalse()
            ->and(AssignmentRules::matches($lead, ['conditions' => [$condition('email', 'contains', 'example')]]))->toBeTrue()
            ->and(AssignmentRules::matches($lead, ['conditions' => [$condition('email', 'not_contains', 'example')]]))->toBeFalse()
            ->and(AssignmentRules::matches($lead, ['conditions' => [$condition('city', 'not_set')]]))->toBeTrue()
            ->and(AssignmentRules::matches($lead, ['conditions' => [$condition('type', 'in', [LeadType::OPPORTUNITY])]]))->toBeFalse()
            ->and(AssignmentRules::matches($lead, ['conditions' => [$condition('probability', '>=', 40), $condition('city', 'not_set')]]))->toBeFalse()
            ->and(AssignmentRules::matches($lead, ['match' => 'any', 'conditions' => [$condition('probability', '>=', 40), $condition('city', 'not_set')]]))->toBeTrue();
    });

    it('gives the same result in a lead query as for a single lead', function () {
        CRMHelper::actingAs();

        $high = assignmentLead(['probability' => 50]);
        $low = assignmentLead(['probability' => 10]);

        $rules = ['conditions' => [['field' => 'probability', 'operator' => '>=', 'value' => '20']]];

        $matchingIds = AssignmentRules::apply(Lead::query()->whereKey([$high->id, $low->id]), $rules)->pluck('id')->all();

        expect($matchingIds)->toBe([$high->id])
            ->and(AssignmentRules::matches($high, $rules))->toBeTrue()
            ->and(AssignmentRules::matches($low, $rules))->toBeFalse();
    });

    it('drops incomplete conditions and unknown fields when saved', function () {
        CRMHelper::actingAs();

        $team = assignmentTeam();

        $crmTeam = CRMTeam::query()->where('team_id', $team->id)->first();
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
        CRMHelper::actingAs();

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
        CRMHelper::actingAs();

        $us = Country::query()->where('code', 'US')->value('id');

        $lead = assignmentLead(['country_id' => $us]);

        expect(AssignmentRules::matches($lead, null))->toBeTrue()
            ->and(AssignmentRules::matches($lead, ['country_ids' => [$us]]))->toBeTrue()
            ->and(AssignmentRules::matches($lead, ['country_ids' => [$us + 1]]))->toBeFalse()
            ->and(AssignmentRules::matches($lead, ['lead_types' => [LeadType::OPPORTUNITY]]))->toBeFalse();
    });

    it('assigns matching leads to the team and spreads them by member capacity', function () {
        CRMHelper::actingAs();

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
        CRMHelper::actingAs();

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

        CRMTeam::query()->where('team_id', $team->id)->update(['assignment_optout' => true]);

        $third = assignmentLead();

        app(LeadAssignment::class)->run();

        expect($third->fresh()->team_id)->toBeNull();
    });

    it('does not assign lost or already-owned leads to a team', function () {
        $owner = CRMHelper::actingAs();

        $team = assignmentTeam();
        app(TeamMembership::class)->addMember($team, assignmentSeller()->id);

        $lost = assignmentLead();
        $lost->markAsLost(null);

        $owned = CRMHelper::lead(['team_id' => null, 'user_id' => $owner->id]);
        $owned->forceFill(['team_id' => null])->saveQuietly();

        app(LeadAssignment::class)->run();

        expect(Lead::withTrashed()->find($lost->id)->team_id)->toBeNull()
            ->and($owned->fresh()->team_id)->toBeNull();
    });

    it('runs from the command only when repeated assignment is due', function () {
        CRMHelper::actingAs();

        $settings = app(CRMSettings::class);
        $settings->auto_assignment_action = 'auto';
        $settings->auto_assignment_run_at = now()->addHour()->toIso8601String();
        $settings->save();

        Artisan::call('crm:assign-leads', ['--scheduled' => true]);

        expect(app(CRMSettings::class)->refresh()->auto_assignment_run_at)->toBe($settings->auto_assignment_run_at);

        $settings->auto_assignment_run_at = now()->subMinute()->toIso8601String();
        $settings->save();

        Artisan::call('crm:assign-leads', ['--scheduled' => true]);

        expect(Carbon::parse(app(CRMSettings::class)->refresh()->auto_assignment_run_at)->isFuture())->toBeTrue();
    });

    it('plans the next run from the interval', function () {
        $settings = app(CRMSettings::class);
        $settings->auto_assignment_interval_number = 3;
        $settings->auto_assignment_interval_type = 'hours';

        $from = now()->startOfHour();

        expect(AssignLeads::nextRun($settings, $from)->equalTo($from->copy()->addHours(3)))->toBeTrue();
    });
});

it('hides assignment on the sales team and does not assign when rule-based assignment is off', function () {
    CRMHelper::actingAs();

    $settings = app(CRMSettings::class);
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

it('gives a new lead the team whose rules it matches, keeping its salesperson', function () {
    $user = CRMHelper::actingAs();

    $india = Country::query()->where('code', 'IN')->value('id');

    $team = assignmentTeam(['match' => 'all', 'conditions' => [
        ['field' => 'country_id', 'operator' => 'in', 'value' => [$india]],
        ['field' => 'city', 'operator' => 'contains', 'value' => 'Delhi'],
    ]]);
    $team->members()->attach($user->id);
    app(TeamMembership::class)->sync($team);

    $delhi = CRMHelper::lead(['country_id' => $india, 'city' => 'Delhi', 'team_id' => null]);
    $mumbai = CRMHelper::lead(['country_id' => $india, 'city' => 'Mumbai', 'team_id' => null]);

    expect($delhi->fresh()->team_id)->toBe($team->id)
        ->and($delhi->fresh()->user_id)->toBe($user->id)
        ->and($mumbai->fresh()->team_id)->toBeNull();
});

it('assigns a team to leads that already have a salesperson when assignment runs', function () {
    $user = CRMHelper::actingAs();

    $team = assignmentTeam();
    $team->members()->attach($user->id);
    app(TeamMembership::class)->sync($team);

    $lead = CRMHelper::lead();
    $lead->forceFill(['team_id' => null, 'user_id' => $user->id])->saveQuietly();

    app(LeadAssignment::class)->run();

    expect($lead->fresh()->team_id)->toBe($team->id)
        ->and($lead->fresh()->user_id)->toBe($user->id);
});

describe('assign leads modal', function () {
    it('lists the unassigned records of the team and the team-less ones matching its rules', function () {
        $country = Country::query()->firstOrFail();
        $otherCountry = Country::query()->whereKeyNot($country->id)->firstOrFail();

        $team = assignmentTeam(['match' => 'all', 'conditions' => [['field' => 'country_id', 'operator' => 'in', 'value' => [$country->id]]]]);

        $inTeam = assignmentLead();
        $inTeam->forceFill(['team_id' => $team->id])->saveQuietly();
        $matching = assignmentLead(['country_id' => $country->id]);
        $notMatching = assignmentLead(['country_id' => $otherCountry->id]);
        $alreadyAssigned = assignmentLead(['country_id' => $country->id]);
        $alreadyAssigned->forceFill(['user_id' => assignmentSeller()->id])->saveQuietly();

        $ids = app(LeadAssignment::class)->candidates($team)->pluck('id')->all();

        expect($ids)->toContain($inTeam->id, $matching->id)
            ->not->toContain($notMatching->id)
            ->not->toContain($alreadyAssigned->id);
    });

    it('assigns only the selected records', function () {
        $team = assignmentTeam();
        $seller = assignmentSeller();
        app(TeamMembership::class)->addMember($team, $seller->id, ['assignment_max' => 30]);

        $selected = assignmentLead();
        $skipped = assignmentLead();
        $selected->forceFill(['team_id' => $team->id])->saveQuietly();
        $skipped->forceFill(['team_id' => $team->id])->saveQuietly();

        $assigned = app(LeadAssignment::class)->assignSelected($team->fresh(), [$selected->id]);

        expect($assigned)->toBe(1)
            ->and(Lead::withTrashed()->find($selected->id)->user_id)->toBe($seller->id)
            ->and(Lead::withTrashed()->find($skipped->id)->user_id)->toBeNull();
    });
});
