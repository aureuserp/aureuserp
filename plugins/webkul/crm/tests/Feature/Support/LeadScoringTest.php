<?php

use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;
use Webkul\Crm\Filament\Clusters\Configurations\Pages\ManageSettings;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages\ViewLead;
use Webkul\Crm\Models\CrmTeam;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\LeadConversion;
use Webkul\Crm\Services\LeadAssignment;
use Webkul\Crm\Services\LeadScoring;
use Webkul\Crm\Services\TeamMembership;
use Webkul\Crm\Settings\CrmSettings;
use Webkul\Sale\Models\Team;

require_once __DIR__.'/../../Helpers/CrmTestHelper.php';

beforeEach(function () {
    CrmTestHelper::bootstrap();

    $settings = app(CrmSettings::class);
    $settings->scoring_variables = ['phone', 'email'];
    $settings->scoring_start_date = null;
    $settings->save();
});

function scoringTeam(): Team
{
    return Team::factory()->create(['company_id' => CrmTestHelper::companyId(), 'is_active' => true]);
}

function scoringHistory(Team $team, int $won = 6, int $lost = 6): void
{
    $wonStage = CrmTestHelper::wonStage();
    $firstStage = CrmTestHelper::firstStage();

    for ($i = 0; $i < $won; $i++) {
        CrmTestHelper::opportunity(['team_id' => $team->id, 'stage_id' => $wonStage->id, 'phone' => '+1 555 010 00'.$i, 'email' => "won{$i}@customer.test"]);
    }

    for ($i = 0; $i < $lost; $i++) {
        CrmTestHelper::opportunity(['team_id' => $team->id, 'stage_id' => $firstStage->id, 'phone' => null, 'email' => null])->markAsLost(null);
    }

    app(LeadScoring::class)->rebuild();
}

it('learns won and lost counts per team', function () {
    CrmTestHelper::actingAs();

    $team = scoringTeam();
    scoringHistory($team, 3, 2);

    $total = DB::table('crm_lead_scoring_frequencies')->where('team_id', $team->id)->where('variable', LeadScoring::TOTAL)->first();

    expect((float) $total->won_count)->toBe(3.0)
        ->and((float) $total->lost_count)->toBe(2.0)
        ->and((float) DB::table('crm_lead_scoring_frequencies')->where('team_id', $team->id)->where('variable', 'phone')->where('value', 'valid')->value('won_count'))->toBe(3.0);
});

it('scores records like the won ones higher and explains why', function () {
    CrmTestHelper::actingAs();

    $team = scoringTeam();
    scoringHistory($team);

    $scoring = app(LeadScoring::class);

    $good = CrmTestHelper::opportunity(['team_id' => $team->id, 'phone' => '+1 555 999 1234', 'email' => 'good@customer.test', 'probability' => null]);
    $bad = CrmTestHelper::opportunity(['team_id' => $team->id, 'phone' => null, 'email' => null, 'probability' => null]);

    $goodScore = $scoring->score($good->fresh())['probability'];
    $badScore = $scoring->score($bad->fresh())['probability'];

    expect($goodScore)->toBeGreaterThan($badScore)
        ->and($goodScore)->toBeLessThan(100.0)
        ->and($scoring->explain($good->fresh())['positives'])->toContain(__('crm::filament/lead-scoring.factors.phone-valid'))
        ->and($scoring->explain($bad->fresh())['negatives'])->toContain(__('crm::filament/lead-scoring.factors.email-invalid'));
});

it('gives no score without won or lost history', function () {
    CrmTestHelper::actingAs();

    DB::table('crm_lead_scoring_frequencies')->delete();

    $lead = CrmTestHelper::lead(['team_id' => scoringTeam()->id]);

    expect(app(LeadScoring::class)->score($lead)['probability'])->toBeNull();
});

it('follows the computed probability until it is set by hand, and can go back to it', function () {
    CrmTestHelper::actingAs();

    $team = scoringTeam();
    scoringHistory($team);

    $lead = CrmTestHelper::opportunity(['team_id' => $team->id, 'phone' => '+1 555 999 1234', 'email' => 'auto@customer.test', 'probability' => null])->fresh();

    expect($lead->is_automated_probability)->toBeTrue()
        ->and((float) $lead->probability)->toBe((float) $lead->automated_probability);

    $lead->update(['probability' => 40]);

    expect($lead->fresh()->is_automated_probability)->toBeFalse();

    $lead->fresh()->update(['email' => 'still-manual@customer.test']);

    expect((float) $lead->fresh()->probability)->toBe(40.0);

    $lead->fresh()->update(['is_automated_probability' => true]);

    expect((float) $lead->fresh()->probability)->toBe((float) $lead->fresh()->automated_probability);
});

it('keeps a probability given when the record is created', function () {
    CrmTestHelper::actingAs();

    $lead = CrmTestHelper::lead(['probability' => 25])->fresh();

    expect($lead->is_automated_probability)->toBeFalse()
        ->and((float) $lead->probability)->toBe(25.0);
});

it('relearns when a record is won or lost', function () {
    CrmTestHelper::actingAs();

    $team = scoringTeam();

    $opportunity = CrmTestHelper::opportunity(['team_id' => $team->id]);
    $opportunity->update(['stage_id' => CrmTestHelper::wonStage()->id]);

    expect((float) DB::table('crm_lead_scoring_frequencies')->where('team_id', $team->id)->where('variable', LeadScoring::TOTAL)->value('won_count'))->toBe(1.0);
});

it('updates probabilities from the settings page', function () {
    CrmTestHelper::actingAs(['page_crm_manage_settings']);

    Livewire::test(ManageSettings::class)
        ->assertFormFieldExists('scoring_variables')
        ->assertFormFieldExists('scoring_start_date')
        ->fillForm(['scoring_variables' => ['country_id', 'tags']])
        ->callAction(TestAction::make('updateProbabilities')->schemaComponent('form'))
        ->assertNotified();

    expect(app(CrmSettings::class)->refresh()->scoring_variables)->toBe(['country_id', 'tags']);
});

it('converts a lead to an opportunity when assignment gives it a salesperson', function () {
    $user = CrmTestHelper::actingAs();

    $settings = app(CrmSettings::class);
    $settings->use_auto_assignment = true;
    $settings->save();

    $team = scoringTeam();
    $team->members()->attach($user->id);
    app(TeamMembership::class)->sync($team);
    CrmTeam::query()->where('team_id', $team->id)->update(['assignment_optout' => false]);

    $lead = CrmTestHelper::lead();
    $lead->forceFill(['user_id' => null, 'team_id' => $team->id])->saveQuietly();

    app(LeadAssignment::class)->run($team);

    expect($lead->fresh()->type)->toBe(Lead::TYPE_OPPORTUNITY)
        ->and($lead->fresh()->user_id)->toBe($user->id)
        ->and(LeadConversion::query()->where('original_lead_id', $lead->id)->exists())->toBeTrue();
});

it('refuses to convert a lead at 100 percent', function () {
    CrmTestHelper::actingAsCrmUser(['lead', 'opportunity']);

    $lead = CrmTestHelper::lead();
    DB::table('crm_leads')->where('id', $lead->id)->update(['probability' => 100]);

    Livewire::test(ViewLead::class, ['record' => $lead->getRouteKey()])
        ->callAction('convertToOpportunity', data: ['customer' => 'none']);

    expect($lead->fresh()->type)->toBe(Lead::TYPE_LEAD);
});
