<?php

use Livewire\Livewire;
use Webkul\Crm\Enums\LeadType;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\CreateOpportunity;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\EditOpportunity;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\ListOpportunities;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\ManageActivities;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\ViewOpportunity;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Schemas\OpportunityForm;
use Webkul\Crm\Models\Activity;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\LostReason;
use Webkul\Crm\Models\Stage;
use Webkul\Sale\Models\Team;
use Webkul\Security\Models\User;
use Webkul\Support\Models\ActivityType;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../Helpers/CRMHelper.php';

beforeEach(fn () => CRMHelper::bootstrap());

function actingAsOpportunityManager(): User
{
    return CRMHelper::actingAsCRMUser(['opportunity', 'lost::reason']);
}

function opportunityOtherUserId(): int
{
    return (int) User::query()->whereKeyNot(auth()->id())->value('id');
}

/**
 * An active user whose only company is the given one.
 */
function opportunitySalespersonIn(Company $company, bool $isActive = true): User
{
    $user = User::withoutEvents(fn (): User => User::factory()->create());

    $user->forceFill([
        'default_company_id' => $company->id,
        'is_active'          => $isActive,
    ])->saveQuietly();

    return $user;
}

function opportunityActivity(Lead $lead, array $attributes = []): Activity
{
    $message = $lead->addMessage([
        'type'             => 'activity',
        'activity_type_id' => ActivityType::query()->where('plugin', 'crm')->orderBy('sort')->value('id'),
        'date_deadline'    => now()->addDays(3)->toDateString(),
        'summary'          => 'Opportunity page activity',
        'assigned_to'      => auth()->id(),
        ...$attributes,
    ]);

    return Activity::query()->findOrFail($message->getKey());
}

it('lists the current user\'s open opportunities by default', function () {
    actingAsOpportunityManager();

    $mine = CRMHelper::opportunity();
    $someoneElses = CRMHelper::opportunity(['user_id' => opportunityOtherUserId()]);
    $lost = CRMHelper::opportunity();
    $lost->markAsLost(null);

    Livewire::test(ListOpportunities::class)
        ->assertOk()
        ->assertSet('activeTableView', 'my_pipeline')
        ->assertCanSeeTableRecords([$mine])
        ->assertCanNotSeeTableRecords([$someoneElses, $lost]);
});

it('does not list leads among the opportunities', function () {
    actingAsOpportunityManager();

    $lead = CRMHelper::lead(['name' => 'Not an opportunity lead']);

    Livewire::test(ListOpportunities::class)
        ->call('loadView', 'open')
        ->searchTable('Not an opportunity lead')
        ->assertCanNotSeeTableRecords([$lead]);
});

it('filters the list through its table views', function (string $view, string $visible, array $hidden) {
    actingAsOpportunityManager();

    $records = [
        'open' => CRMHelper::opportunity(['name' => 'Views test open']),
        'won'  => CRMHelper::opportunity(['name' => 'Views test won', 'stage_id' => CRMHelper::wonStage()->id]),
        'lost' => CRMHelper::opportunity(['name' => 'Views test lost']),
    ];

    $records['unassigned'] = CRMHelper::opportunity(['name' => 'Views test unassigned']);
    $records['unassigned']->forceFill(['user_id' => null])->saveQuietly();

    $records['lost']->markAsLost(null);

    Livewire::test(ListOpportunities::class)
        ->call('loadView', $view)
        ->searchTable('Views test')
        ->assertCanSeeTableRecords([$records[$visible]])
        ->assertCanNotSeeTableRecords(array_map(fn (string $key): Lead => $records[$key], $hidden));
})->with([
    'open'       => ['open', 'open', ['won', 'lost']],
    'won'        => ['won', 'won', ['open', 'lost', 'unassigned']],
    'lost'       => ['lost', 'lost', ['open', 'won', 'unassigned']],
    'unassigned' => ['unassigned', 'unassigned', ['open', 'won', 'lost']],
]);

it('forbids the list without the view any permission', function () {
    CRMHelper::actingAs(['view_any_crm_lead']);

    Livewire::test(ListOpportunities::class)->assertForbidden();
});

it('creates an opportunity', function () {
    $user = actingAsOpportunityManager();

    $team = Team::factory()->create(['company_id' => CRMHelper::companyId(), 'is_active' => true]);

    Livewire::test(CreateOpportunity::class)
        ->assertOk()
        ->fillForm([
            'name'             => 'Created from the form',
            'expected_revenue' => 4200,
            'probability'      => 40,
            'team_id'          => $team->id,
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertRedirect();

    $lead = Lead::query()->where('name', 'Created from the form')->firstOrFail();

    expect($lead->type)->toBe(LeadType::OPPORTUNITY)
        ->and($lead->user_id)->toBe($user->id)
        ->and($lead->team_id)->toBe($team->id)
        ->and($lead->company_id)->toBe(CRMHelper::companyId())
        ->and($lead->stage_id)->toBe(Stage::query()->orderBy('sort')->value('id'));
});

it('requires a name to create an opportunity', function () {
    actingAsOpportunityManager();

    Livewire::test(CreateOpportunity::class)
        ->fillForm(['name' => ''])
        ->call('create')
        ->assertHasFormErrors(['name' => 'required']);
});

it('rejects a sales team of another company', function () {
    actingAsOpportunityManager();

    $foreignTeam = Team::factory()->create(['company_id' => Company::factory()->create()->id, 'is_active' => true]);

    Livewire::test(CreateOpportunity::class)
        ->fillForm([
            'name'    => 'Foreign team deal',
            'team_id' => $foreignTeam->id,
        ])
        ->call('create')
        ->assertHasFormErrors(['team_id']);

    expect(Lead::query()->where('name', 'Foreign team deal')->exists())->toBeFalse();
});

it('rejects a salesperson who may not work in the company', function (bool $sameCompany, bool $isActive) {
    actingAsOpportunityManager();

    $company = $sameCompany ? Company::query()->findOrFail(CRMHelper::companyId()) : Company::factory()->create();

    $salesperson = opportunitySalespersonIn($company, $isActive);

    Livewire::test(CreateOpportunity::class)
        ->fillForm([
            'name'    => 'Foreign salesperson deal',
            'user_id' => $salesperson->id,
        ])
        ->call('create')
        ->assertHasFormErrors(['user_id']);

    expect(Lead::query()->where('name', 'Foreign salesperson deal')->exists())->toBeFalse();
})->with([
    'another company'        => [false, true],
    'inactive, same company' => [true, false],
]);

it('accepts a salesperson allowed in the company', function () {
    actingAsOpportunityManager();

    $salesperson = opportunitySalespersonIn(Company::factory()->create());

    $salesperson->allowedCompanies()->attach(CRMHelper::companyId());

    Livewire::test(CreateOpportunity::class)
        ->fillForm([
            'name'    => 'Allowed salesperson deal',
            'user_id' => $salesperson->id,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Lead::query()->where('name', 'Allowed salesperson deal')->value('user_id'))->toBe($salesperson->id);
});

it('scopes salespeople to active users of the company', function () {
    actingAsOpportunityManager();

    $companyId = CRMHelper::companyId();

    $inCompany = opportunitySalespersonIn(Company::query()->findOrFail($companyId));
    $inactive = opportunitySalespersonIn(Company::query()->findOrFail($companyId), isActive: false);
    $elsewhere = opportunitySalespersonIn(Company::factory()->create());

    $ids = OpportunityForm::scopeSalespeople(User::query(), $companyId)->pluck('id')->all();

    expect($ids)->toContain($inCompany->id)
        ->not->toContain($inactive->id)
        ->not->toContain($elsewhere->id);
});

it('forbids the create page without the create permission', function () {
    CRMHelper::actingAs(['view_any_crm_opportunity', 'view_crm_opportunity']);

    Livewire::test(CreateOpportunity::class)->assertForbidden();
});

it('shows an opportunity', function () {
    actingAsOpportunityManager();

    $opportunity = CRMHelper::opportunity(['name' => 'Visible opportunity']);

    Livewire::test(ViewOpportunity::class, ['record' => $opportunity->id])
        ->assertOk()
        ->assertSee('Visible opportunity');
});

it('forbids viewing an opportunity without the view permission', function () {
    CRMHelper::actingAs(['view_any_crm_opportunity']);

    $opportunity = CRMHelper::opportunity();

    Livewire::test(ViewOpportunity::class, ['record' => $opportunity->id])->assertForbidden();
});

it('edits an opportunity', function () {
    actingAsOpportunityManager();

    $opportunity = CRMHelper::opportunity(['name' => 'Before edit']);

    Livewire::test(EditOpportunity::class, ['record' => $opportunity->id])
        ->assertOk()
        ->fillForm([
            'name'             => 'After edit',
            'expected_revenue' => 9000,
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified();

    $opportunity->refresh();

    expect($opportunity->name)->toBe('After edit')
        ->and((float) $opportunity->expected_revenue)->toBe(9000.0);
});

it('forbids editing an opportunity without the update permission', function () {
    CRMHelper::actingAs(['view_any_crm_opportunity', 'view_crm_opportunity']);

    $opportunity = CRMHelper::opportunity();

    Livewire::test(EditOpportunity::class, ['record' => $opportunity->id])->assertForbidden();
});

it('marks an opportunity as won', function () {
    actingAsOpportunityManager();

    $opportunity = CRMHelper::opportunity(['probability' => 30]);

    CRMHelper::wonStage();

    Livewire::test(ViewOpportunity::class, ['record' => $opportunity->id])
        ->callAction('markAsWon')
        ->assertNotified(__('crm::filament/clusters/sales/resources/opportunity.actions.won.notification.success.title'))
        ->assertActionHidden('markAsWon');

    $opportunity->refresh();

    expect($opportunity->stage_id)->toBe(Stage::query()->where('is_won', true)->orderBy('sort')->value('id'))
        ->and($opportunity->stage->is_won)->toBeTrue()
        ->and((float) $opportunity->probability)->toBe(100.0)
        ->and($opportunity->closed_at)->not->toBeNull();
});

it('warns instead of winning when no won stage exists', function () {
    actingAsOpportunityManager();

    $opportunity = CRMHelper::opportunity();

    $stageId = $opportunity->stage_id;

    Stage::query()->where('is_won', true)->update(['is_won' => false]);

    Livewire::test(ViewOpportunity::class, ['record' => $opportunity->id])
        ->callAction('markAsWon')
        ->assertNotified(__('crm::filament/clusters/sales/resources/opportunity.actions.won.notification.no-won-stage.title'));

    expect($opportunity->refresh()->stage_id)->toBe($stageId);
});

it('marks an opportunity as lost with a reason and feedback', function () {
    actingAsOpportunityManager();

    $opportunity = CRMHelper::opportunity(['probability' => 60]);

    $reason = LostReason::factory()->create(['name' => 'Chose a competitor']);

    Livewire::test(ViewOpportunity::class, ['record' => $opportunity->id])
        ->callAction('markAsLost', data: [
            'lost_reason_id' => $reason->id,
            'lost_feedback'  => 'Price was too high',
        ])
        ->assertHasNoActionErrors()
        ->assertNotified(__('crm::filament/clusters/sales/resources/opportunity.actions.lost.notification.title'));

    $lost = Lead::withTrashed()->findOrFail($opportunity->id);

    expect($lost->trashed())->toBeTrue()
        ->and($lost->lost_reason_id)->toBe($reason->id)
        ->and($lost->lost_feedback)->toBe('Price was too high')
        ->and((float) $lost->probability)->toBe(0.0)
        ->and($lost->closed_at)->not->toBeNull();
});

it('goes back to the view page after marking an opportunity as lost from the edit page', function () {
    actingAsOpportunityManager();

    $opportunity = CRMHelper::opportunity();

    Livewire::test(EditOpportunity::class, ['record' => $opportunity->id])
        ->callAction('markAsLost', data: ['lost_feedback' => 'No budget'])
        ->assertRedirect(OpportunityResource::getUrl('view', ['record' => $opportunity]));

    expect(Lead::withTrashed()->findOrFail($opportunity->id)->trashed())->toBeTrue();
});

it('restores a lost opportunity and clears the lost reason', function () {
    actingAsOpportunityManager();

    $opportunity = CRMHelper::opportunity();
    $opportunity->markAsLost(LostReason::factory()->create()->id, 'Lost for the test');

    Livewire::test(ViewOpportunity::class, ['record' => $opportunity->id])
        ->assertActionVisible('restore')
        ->callAction('restore')
        ->assertActionHidden('restore');

    $restored = Lead::query()->findOrFail($opportunity->id);

    expect($restored->trashed())->toBeFalse()
        ->and($restored->lost_reason_id)->toBeNull()
        ->and($restored->lost_feedback)->toBeNull()
        ->and($restored->closed_at)->toBeNull();
});

it('force deletes a lost opportunity', function () {
    actingAsOpportunityManager();

    $opportunity = CRMHelper::opportunity();
    $opportunity->markAsLost(null);

    Livewire::test(ViewOpportunity::class, ['record' => $opportunity->id])
        ->callAction('forceDelete')
        ->assertRedirect();

    expect(Lead::withTrashed()->find($opportunity->id))->toBeNull();
});

it('hides restore and force delete without their permissions', function () {
    CRMHelper::actingAs(['view_any_crm_opportunity', 'view_crm_opportunity']);

    $opportunity = CRMHelper::opportunity();
    $opportunity->markAsLost(null);

    Livewire::test(ViewOpportunity::class, ['record' => $opportunity->id])
        ->assertActionHidden('restore')
        ->assertActionHidden('forceDelete');
});

it('shows Won and Lost as header actions on the view and edit pages', function (string $page) {
    actingAsOpportunityManager();

    $opportunity = CRMHelper::opportunity();

    Livewire::test($page, ['record' => $opportunity->id])
        ->assertActionVisible('markAsWon')
        ->assertActionVisible('markAsLost');
})->with([
    'view' => [ViewOpportunity::class],
    'edit' => [EditOpportunity::class],
]);

it('only offers the header actions that apply to the record', function (string $state, bool $wonVisible, bool $lostVisible) {
    actingAsOpportunityManager();

    $opportunity = CRMHelper::opportunity();

    match ($state) {
        'won'  => $opportunity->markAsWon(),
        'lost' => $opportunity->markAsLost(null),
    };

    Livewire::test(ViewOpportunity::class, ['record' => $opportunity->id])
        ->{$wonVisible ? 'assertActionVisible' : 'assertActionHidden'}('markAsWon')
        ->{$lostVisible ? 'assertActionVisible' : 'assertActionHidden'}('markAsLost');
})->with([
    'won'  => ['won', false, true],
    'lost' => ['lost', false, false],
]);

it('hides Won and Lost without the update and delete permissions', function () {
    CRMHelper::actingAs(['view_any_crm_opportunity', 'view_crm_opportunity']);

    $opportunity = CRMHelper::opportunity();

    Livewire::test(ViewOpportunity::class, ['record' => $opportunity->id])
        ->assertActionHidden('markAsWon')
        ->assertActionHidden('markAsLost');
});

it('shows activities as a record tab on opportunities', function () {
    CRMHelper::actingAsCRMUser(['opportunity', 'activity']);

    $opportunity = CRMHelper::opportunity();

    expect(OpportunityResource::getRelations())->toBe([])
        ->and(OpportunityResource::getUrl('activities', ['record' => $opportunity]))->toEndWith("/{$opportunity->id}/activities");
});

it('lists the opportunity\'s own activities on its page', function () {
    CRMHelper::actingAsCRMUser(['opportunity', 'activity']);

    $opportunity = CRMHelper::opportunity();
    $other = CRMHelper::opportunity();

    $activity = opportunityActivity($opportunity);
    $otherActivity = opportunityActivity($other);

    $tab = collect(ManageActivities::navigationItems(Livewire::test(ViewOpportunity::class, ['record' => $opportunity->id])->instance()))->first();

    expect($tab?->getBadge())->toBe('1');

    Livewire::test(ManageActivities::class, ['record' => $opportunity->id])
        ->assertOk()
        ->assertCanSeeTableRecords([$activity])
        ->assertCanNotSeeTableRecords([$otherActivity]);
});

it('hides the activities section without the activity permission', function () {
    actingAsOpportunityManager();

    $opportunity = CRMHelper::opportunity();

    expect(ManageActivities::canAccess(['record' => $opportunity]))->toBeFalse()
        ->and(ManageActivities::navigationItems(Livewire::test(ViewOpportunity::class, ['record' => $opportunity->id])->instance()))->toBe([]);
});
