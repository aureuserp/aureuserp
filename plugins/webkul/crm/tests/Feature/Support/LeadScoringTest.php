<?php

use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;
use Webkul\Crm\Enums\LeadScoringVariable;
use Webkul\Crm\Enums\LeadType;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages\ViewLead;
use Webkul\Crm\Filament\Clusters\Settings\Pages\ManageLeadScoring;
use Webkul\Crm\Models\CRMTeam;
use Webkul\Crm\Models\LeadConversion;
use Webkul\Crm\Services\LeadAssignment;
use Webkul\Crm\Services\LeadScoring;
use Webkul\Crm\Services\TeamMembership;
use Webkul\Crm\Settings\CRMSettings;
use Webkul\Sale\Models\Team;

require_once __DIR__.'/../../Helpers/CRMHelper.php';

beforeEach(function () {
    CRMHelper::bootstrap();

    $settings = app(CRMSettings::class);
    $settings->scoring_variables = ['phone', 'email'];
    $settings->scoring_start_date = null;
    $settings->save();
});

function scoringTeam(): Team
{
    return Team::factory()->create(['company_id' => CRMHelper::companyId(), 'is_active' => true]);
}

function scoringHistory(Team $team, int $won = 6, int $lost = 6): void
{
    $wonStage = CRMHelper::wonStage();
    $firstStage = CRMHelper::firstStage();

    for ($i = 0; $i < $won; $i++) {
        CRMHelper::opportunity(['team_id' => $team->id, 'stage_id' => $wonStage->id, 'phone' => '+1 555 010 00'.$i, 'email' => "won{$i}@customer.test"]);
    }

    for ($i = 0; $i < $lost; $i++) {
        CRMHelper::opportunity(['team_id' => $team->id, 'stage_id' => $firstStage->id, 'phone' => null, 'email' => null])->markAsLost(null);
    }

    app(LeadScoring::class)->rebuild();
}

it('learns won and lost counts per team', function () {
    CRMHelper::actingAs();

    $team = scoringTeam();
    scoringHistory($team, 3, 2);

    $total = DB::table('crm_lead_scoring_frequencies')->where('team_id', $team->id)->where('variable', LeadScoring::TOTAL)->first();

    expect((float) $total->won_count)->toBe(3.0)
        ->and((float) $total->lost_count)->toBe(2.0)
        ->and((float) DB::table('crm_lead_scoring_frequencies')->where('team_id', $team->id)->where('variable', 'phone')->where('value', 'valid')->value('won_count'))->toBe(3.0);
});

it('scores records like the won ones higher and explains why', function () {
    CRMHelper::actingAs();

    $team = scoringTeam();
    scoringHistory($team);

    $scoring = app(LeadScoring::class);

    $good = CRMHelper::opportunity(['team_id' => $team->id, 'phone' => '+1 555 999 1234', 'email' => 'good@customer.test', 'probability' => null]);
    $bad = CRMHelper::opportunity(['team_id' => $team->id, 'phone' => null, 'email' => null, 'probability' => null]);

    $goodScore = $scoring->score($good->fresh())['probability'];
    $badScore = $scoring->score($bad->fresh())['probability'];

    expect($goodScore)->toBeGreaterThan($badScore)
        ->and($goodScore)->toBeLessThan(100.0)
        ->and($scoring->explain($good->fresh())['positives'])->toContain(__('crm::filament/lead-scoring.factors.phone-valid'))
        ->and($scoring->explain($bad->fresh())['negatives'])->toContain(__('crm::filament/lead-scoring.factors.email-invalid'));
});

it('gives no score without won or lost history', function () {
    CRMHelper::actingAs();

    DB::table('crm_lead_scoring_frequencies')->delete();

    $lead = CRMHelper::lead(['team_id' => scoringTeam()->id]);

    expect(app(LeadScoring::class)->score($lead)['probability'])->toBeNull();
});

it('follows the computed probability until it is set by hand, and can go back to it', function () {
    CRMHelper::actingAs();

    $team = scoringTeam();
    scoringHistory($team);

    $lead = CRMHelper::opportunity(['team_id' => $team->id, 'phone' => '+1 555 999 1234', 'email' => 'auto@customer.test', 'probability' => null])->fresh();

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
    CRMHelper::actingAs();

    $lead = CRMHelper::lead(['probability' => 25])->fresh();

    expect($lead->is_automated_probability)->toBeFalse()
        ->and((float) $lead->probability)->toBe(25.0);
});

it('relearns when a record is won or lost', function () {
    CRMHelper::actingAs();

    $team = scoringTeam();

    $opportunity = CRMHelper::opportunity(['team_id' => $team->id]);
    $opportunity->update(['stage_id' => CRMHelper::wonStage()->id]);

    expect((float) DB::table('crm_lead_scoring_frequencies')->where('team_id', $team->id)->where('variable', LeadScoring::TOTAL)->value('won_count'))->toBe(1.0);
});

it('updates probabilities from the settings page', function () {
    CRMHelper::actingAs(['page_crm_manage_lead_scoring']);

    Livewire::test(ManageLeadScoring::class)
        ->assertFormFieldExists('scoring_variables')
        ->assertFormFieldExists('scoring_start_date')
        ->fillForm(['scoring_variables' => ['country_id', 'tags']])
        ->callAction(TestAction::make('updateProbabilities')->schemaComponent('form'))
        ->assertNotified();

    expect(app(CRMSettings::class)->refresh()->scoring_variables)->toBe(['country_id', 'tags']);
});

it('converts a lead to an opportunity when assignment gives it a salesperson', function () {
    $user = CRMHelper::actingAs();

    $settings = app(CRMSettings::class);
    $settings->use_auto_assignment = true;
    $settings->save();

    $team = scoringTeam();
    $team->members()->attach($user->id);
    app(TeamMembership::class)->sync($team);
    CRMTeam::query()->where('team_id', $team->id)->update(['assignment_optout' => false]);

    $lead = CRMHelper::lead();
    $lead->forceFill(['user_id' => null, 'team_id' => $team->id])->saveQuietly();

    app(LeadAssignment::class)->run($team);

    expect($lead->fresh()->type)->toBe(LeadType::OPPORTUNITY)
        ->and($lead->fresh()->user_id)->toBe($user->id)
        ->and(LeadConversion::query()->where('original_lead_id', $lead->id)->exists())->toBeTrue();
});

it('refuses to convert a lead at 100 percent', function () {
    CRMHelper::actingAsCRMUser(['lead', 'opportunity']);

    $lead = CRMHelper::lead();
    DB::table('crm_leads')->where('id', $lead->id)->update(['probability' => 100]);

    Livewire::test(ViewLead::class, ['record' => $lead->getRouteKey()])
        ->callAction('convertToOpportunity', data: ['customer' => 'none']);

    expect($lead->fresh()->type)->toBe(LeadType::LEAD);
});

it('offers every scoring variable except the always-used stage in the settings', function () {
    expect(LeadScoringVariable::selectableValues())->toBe(['country_id', 'state_id', 'phone', 'email', 'utm_source_id', 'tags'])
        ->and(LeadScoringVariable::options())->toHaveKeys(['country_id', 'state_id', 'phone', 'email', 'utm_source_id', 'tags'])
        ->and(LeadScoringVariable::options())->not->toHaveKey('stage');
});
