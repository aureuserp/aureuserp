<?php

use Filament\Actions\Action;
use Livewire\Livewire;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\CreateOpportunity;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\EditOpportunity;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\ListOpportunities;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\ViewOpportunity;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Schemas\OpportunityForm;
use Webkul\Crm\Filament\RelationManagers\ActivitiesRelationManager;
use Webkul\Crm\Models\Activity;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\LostReason;
use Webkul\Crm\Models\Stage;
use Webkul\Sale\Models\Team;
use Webkul\Security\Models\User;
use Webkul\Support\Models\ActivityType;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../Helpers/CrmTestHelper.php';

beforeEach(fn () => CrmTestHelper::bootstrap());

function actingAsOpportunityManager(): User
{
    return CrmTestHelper::actingAsCrmUser(['opportunity', 'lost::reason']);
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

/**
 * Names of the workflow actions the floating record bar shows.
 *
 * @return array<int, string>
 */
function floatingBarActionNames(object $page): array
{
    return collect($page->getFloatingRecordActions())
        ->filter(fn (?Action $action): bool => (bool) $action?->isVisible())
        ->map(fn (Action $action): string => $action->getName())
        ->values()
        ->all();
}

it('lists the current user\'s open opportunities by default', function () {
    actingAsOpportunityManager();

    $mine = CrmTestHelper::opportunity();
    $someoneElses = CrmTestHelper::opportunity(['user_id' => opportunityOtherUserId()]);
    $lost = CrmTestHelper::opportunity();
    $lost->markAsLost(null);

    Livewire::test(ListOpportunities::class)
        ->assertOk()
        ->assertSet('activeTableView', 'my_pipeline')
        ->assertCanSeeTableRecords([$mine])
        ->assertCanNotSeeTableRecords([$someoneElses, $lost]);
});

it('does not list leads among the opportunities', function () {
    actingAsOpportunityManager();

    $lead = CrmTestHelper::lead(['name' => 'Not an opportunity lead']);

    Livewire::test(ListOpportunities::class)
        ->call('loadView', 'open')
        ->searchTable('Not an opportunity lead')
        ->assertCanNotSeeTableRecords([$lead]);
});

it('filters the list through its table views', function (string $view, string $visible, array $hidden) {
    actingAsOpportunityManager();

    $records = [
        'open' => CrmTestHelper::opportunity(['name' => 'Views test open']),
        'won'  => CrmTestHelper::opportunity(['name' => 'Views test won', 'stage_id' => CrmTestHelper::wonStage()->id]),
        'lost' => CrmTestHelper::opportunity(['name' => 'Views test lost']),
    ];

    $records['unassigned'] = CrmTestHelper::opportunity(['name' => 'Views test unassigned']);
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
    CrmTestHelper::actingAs(['view_any_crm_lead']);

    Livewire::test(ListOpportunities::class)->assertForbidden();
});

it('creates an opportunity', function () {
    $user = actingAsOpportunityManager();

    $team = Team::factory()->create(['company_id' => CrmTestHelper::companyId(), 'is_active' => true]);

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

    expect($lead->type)->toBe(Lead::TYPE_OPPORTUNITY)
        ->and($lead->user_id)->toBe($user->id)
        ->and($lead->team_id)->toBe($team->id)
        ->and($lead->company_id)->toBe(CrmTestHelper::companyId())
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

    $company = $sameCompany ? Company::query()->findOrFail(CrmTestHelper::companyId()) : Company::factory()->create();

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

    $salesperson->allowedCompanies()->attach(CrmTestHelper::companyId());

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

    $companyId = CrmTestHelper::companyId();

    $inCompany = opportunitySalespersonIn(Company::query()->findOrFail($companyId));
    $inactive = opportunitySalespersonIn(Company::query()->findOrFail($companyId), isActive: false);
    $elsewhere = opportunitySalespersonIn(Company::factory()->create());

    $ids = OpportunityForm::scopeSalespeople(User::query(), $companyId)->pluck('id')->all();

    expect($ids)->toContain($inCompany->id)
        ->not->toContain($inactive->id)
        ->not->toContain($elsewhere->id);
});

it('forbids the create page without the create permission', function () {
    CrmTestHelper::actingAs(['view_any_crm_opportunity', 'view_crm_opportunity']);

    Livewire::test(CreateOpportunity::class)->assertForbidden();
});

it('shows an opportunity', function () {
    actingAsOpportunityManager();

    $opportunity = CrmTestHelper::opportunity(['name' => 'Visible opportunity']);

    Livewire::test(ViewOpportunity::class, ['record' => $opportunity->id])
        ->assertOk()
        ->assertSee('Visible opportunity');
});

it('forbids viewing an opportunity without the view permission', function () {
    CrmTestHelper::actingAs(['view_any_crm_opportunity']);

    $opportunity = CrmTestHelper::opportunity();

    Livewire::test(ViewOpportunity::class, ['record' => $opportunity->id])->assertForbidden();
});

it('edits an opportunity', function () {
    actingAsOpportunityManager();

    $opportunity = CrmTestHelper::opportunity(['name' => 'Before edit']);

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
    CrmTestHelper::actingAs(['view_any_crm_opportunity', 'view_crm_opportunity']);

    $opportunity = CrmTestHelper::opportunity();

    Livewire::test(EditOpportunity::class, ['record' => $opportunity->id])->assertForbidden();
});

it('marks an opportunity as won', function () {
    actingAsOpportunityManager();

    $opportunity = CrmTestHelper::opportunity(['probability' => 30]);

    CrmTestHelper::wonStage();

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

    $opportunity = CrmTestHelper::opportunity();

    $stageId = $opportunity->stage_id;

    Stage::query()->where('is_won', true)->update(['is_won' => false]);

    Livewire::test(ViewOpportunity::class, ['record' => $opportunity->id])
        ->callAction('markAsWon')
        ->assertNotified(__('crm::filament/clusters/sales/resources/opportunity.actions.won.notification.no-won-stage.title'));

    expect($opportunity->refresh()->stage_id)->toBe($stageId);
});

it('marks an opportunity as lost with a reason and feedback', function () {
    actingAsOpportunityManager();

    $opportunity = CrmTestHelper::opportunity(['probability' => 60]);

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

    $opportunity = CrmTestHelper::opportunity();

    Livewire::test(EditOpportunity::class, ['record' => $opportunity->id])
        ->callAction('markAsLost', data: ['lost_feedback' => 'No budget'])
        ->assertRedirect(OpportunityResource::getUrl('view', ['record' => $opportunity]));

    expect(Lead::withTrashed()->findOrFail($opportunity->id)->trashed())->toBeTrue();
});

it('restores a lost opportunity and clears the lost reason', function () {
    actingAsOpportunityManager();

    $opportunity = CrmTestHelper::opportunity();
    $opportunity->markAsLost(LostReason::factory()->create()->id, 'Lost for the test');

    Livewire::test(ViewOpportunity::class, ['record' => $opportunity->id])
        ->assertActionHidden('edit')
        ->callAction('restore')
        ->assertActionVisible('edit');

    $restored = Lead::query()->findOrFail($opportunity->id);

    expect($restored->trashed())->toBeFalse()
        ->and($restored->lost_reason_id)->toBeNull()
        ->and($restored->lost_feedback)->toBeNull()
        ->and($restored->closed_at)->toBeNull();
});

it('force deletes a lost opportunity', function () {
    actingAsOpportunityManager();

    $opportunity = CrmTestHelper::opportunity();
    $opportunity->markAsLost(null);

    Livewire::test(ViewOpportunity::class, ['record' => $opportunity->id])
        ->callAction('forceDelete')
        ->assertRedirect();

    expect(Lead::withTrashed()->find($opportunity->id))->toBeNull();
});

it('hides restore and force delete without their permissions', function () {
    CrmTestHelper::actingAs(['view_any_crm_opportunity', 'view_crm_opportunity']);

    $opportunity = CrmTestHelper::opportunity();
    $opportunity->markAsLost(null);

    Livewire::test(ViewOpportunity::class, ['record' => $opportunity->id])
        ->assertActionHidden('restore')
        ->assertActionHidden('forceDelete');
});

it('shows Won and Lost in the floating record bar of the view and edit pages', function (string $page) {
    actingAsOpportunityManager();

    $opportunity = CrmTestHelper::opportunity();

    $component = Livewire::test($page, ['record' => $opportunity->id])->assertOk();

    expect(floatingBarActionNames($component->instance()))->toBe(['markAsWon', 'markAsLost'])
        ->and($component->instance()->getFloatingRecordTabs())->toHaveCount(2);

    $component
        ->assertActionVisible('markAsWon')
        ->assertActionVisible('markAsLost');
})->with([
    'view' => [ViewOpportunity::class],
    'edit' => [EditOpportunity::class],
]);

it('only offers the floating actions that apply to the record', function (string $state, array $expected) {
    actingAsOpportunityManager();

    $opportunity = CrmTestHelper::opportunity();

    match ($state) {
        'won'  => $opportunity->markAsWon(),
        'lost' => $opportunity->markAsLost(null),
    };

    $component = Livewire::test(ViewOpportunity::class, ['record' => $opportunity->id]);

    expect(floatingBarActionNames($component->instance()))->toBe($expected);
})->with([
    'won'  => ['won', ['markAsLost']],
    'lost' => ['lost', []],
]);

it('hides Won and Lost without the update and delete permissions', function () {
    CrmTestHelper::actingAs(['view_any_crm_opportunity', 'view_crm_opportunity']);

    $opportunity = CrmTestHelper::opportunity();

    $component = Livewire::test(ViewOpportunity::class, ['record' => $opportunity->id]);

    expect(floatingBarActionNames($component->instance()))->toBe([]);
});

it('registers the activities relation manager on opportunities', function () {
    expect(OpportunityResource::getRelations())->toContain(ActivitiesRelationManager::class);
});

it('lists the opportunity\'s own activities on its page', function () {
    CrmTestHelper::actingAsCrmUser(['opportunity', 'activity']);

    $opportunity = CrmTestHelper::opportunity();
    $other = CrmTestHelper::opportunity();

    $activity = opportunityActivity($opportunity);
    $otherActivity = opportunityActivity($other);

    expect(ActivitiesRelationManager::canViewForRecord($opportunity, ViewOpportunity::class))->toBeTrue()
        ->and(ActivitiesRelationManager::getBadge($opportunity, ViewOpportunity::class))->toBe('1');

    Livewire::test(ActivitiesRelationManager::class, [
        'ownerRecord' => $opportunity,
        'pageClass'   => ViewOpportunity::class,
    ])
        ->assertOk()
        ->assertCanSeeTableRecords([$activity])
        ->assertCanNotSeeTableRecords([$otherActivity]);
});

it('hides the activities section without the activity permission', function () {
    actingAsOpportunityManager();

    $opportunity = CrmTestHelper::opportunity();

    expect(ActivitiesRelationManager::canViewForRecord($opportunity, ViewOpportunity::class))->toBeFalse()
        ->and(ActivitiesRelationManager::getBadge($opportunity, ViewOpportunity::class))->toBeNull();
});
