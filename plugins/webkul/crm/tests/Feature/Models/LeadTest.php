<?php

use Webkul\Crm\Models\Activity;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\LostReason;
use Webkul\Crm\Models\Stage;
use Webkul\Crm\Models\Tag;
use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Models\Partner;
use Webkul\Sale\Models\Team;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../Helpers/CrmTestHelper.php';

beforeEach(fn () => CrmTestHelper::bootstrap());

it('fills type, first stage, creator, salesperson and assignment date when created', function () {
    $user = CrmTestHelper::actingAs();

    $lead = Lead::query()->create([
        'name'       => 'Website redesign',
        'company_id' => CrmTestHelper::companyId(),
    ]);

    expect($lead->type)->toBe(Lead::TYPE_OPPORTUNITY)
        ->and($lead->stage_id)->toBe(CrmTestHelper::firstStage()->id)
        ->and($lead->creator_id)->toBe($user->id)
        ->and($lead->user_id)->toBe($user->id)
        ->and($lead->assigned_at)->not->toBeNull();
});

it('keeps the given type, stage and salesperson when created', function () {
    CrmTestHelper::actingAs();

    $otherUser = User::query()->where('email', 'admin@example.com')->firstOrFail();
    $stage = Stage::query()->where('is_won', false)->orderByDesc('sort')->firstOrFail();

    $lead = Lead::query()->create([
        'name'       => 'Explicit lead',
        'type'       => Lead::TYPE_LEAD,
        'stage_id'   => $stage->id,
        'user_id'    => $otherUser->id,
        'company_id' => CrmTestHelper::companyId(),
    ]);

    expect($lead->type)->toBe(Lead::TYPE_LEAD)
        ->and($lead->stage_id)->toBe($stage->id)
        ->and($lead->user_id)->toBe($otherUser->id);
});

it('resets the assignment date when the salesperson changes', function () {
    $user = CrmTestHelper::actingAs();

    $otherUser = User::query()->whereKeyNot($user->id)->firstOrFail();

    $opportunity = CrmTestHelper::opportunity(['assigned_at' => now()->subMonth()]);

    $opportunity->update(['user_id' => $otherUser->id]);

    expect($opportunity->fresh()->assigned_at->greaterThan(now()->subMinute()))->toBeTrue();
});

it('keeps the assignment date when the salesperson does not change', function () {
    CrmTestHelper::actingAs();

    $assignedAt = now()->subMonth()->startOfSecond();

    $opportunity = CrmTestHelper::opportunity(['assigned_at' => $assignedAt]);

    $opportunity->update(['name' => 'Renamed opportunity']);

    expect($opportunity->fresh()->assigned_at->equalTo($assignedAt))->toBeTrue();
});

it('clears the assignment date when the salesperson is removed', function () {
    CrmTestHelper::actingAs();

    $opportunity = CrmTestHelper::opportunity();

    $opportunity->update(['user_id' => null]);

    expect($opportunity->fresh()->assigned_at)->toBeNull();
});

it('sets probability to 100 and the closing date when moved to a won stage', function () {
    CrmTestHelper::actingAs();

    $opportunity = CrmTestHelper::opportunity([
        'stage_id'    => CrmTestHelper::firstStage()->id,
        'probability' => 30,
        'closed_at'   => null,
    ]);

    $opportunity->update(['stage_id' => CrmTestHelper::wonStage()->id]);

    $opportunity->refresh();

    expect((float) $opportunity->probability)->toBe(100.0)
        ->and($opportunity->closed_at)->not->toBeNull()
        ->and($opportunity->isWon())->toBeTrue();
});

it('clears the closing date when moved back out of a won stage', function () {
    CrmTestHelper::actingAs();

    $opportunity = CrmTestHelper::opportunity(['stage_id' => CrmTestHelper::firstStage()->id]);

    $opportunity->update(['stage_id' => CrmTestHelper::wonStage()->id]);

    $opportunity->update(['stage_id' => CrmTestHelper::firstStage()->id]);

    $opportunity->refresh();

    expect($opportunity->closed_at)->toBeNull()
        ->and($opportunity->isWon())->toBeFalse();
});

it('does not touch the closing date when moving between open stages', function () {
    CrmTestHelper::actingAs();

    $openStages = Stage::query()->where('is_won', false)->orderBy('sort')->take(2)->get();

    if ($openStages->count() < 2) {
        $openStages->push(Stage::factory()->create());
    }

    $opportunity = CrmTestHelper::opportunity([
        'stage_id'    => $openStages[0]->id,
        'probability' => 40,
    ]);

    $opportunity->update(['stage_id' => $openStages[1]->id]);

    $opportunity->refresh();

    expect((float) $opportunity->probability)->toBe(40.0)
        ->and($opportunity->closed_at)->toBeNull();
});

it('marks an opportunity as won by moving it to the first won stage', function () {
    CrmTestHelper::actingAs();

    $wonStage = CrmTestHelper::wonStage();

    $opportunity = CrmTestHelper::opportunity(['stage_id' => CrmTestHelper::firstStage()->id]);

    expect($opportunity->markAsWon())->toBeTrue();

    $opportunity->refresh();

    expect($opportunity->stage_id)->toBe($wonStage->id)
        ->and((float) $opportunity->probability)->toBe(100.0)
        ->and($opportunity->closed_at)->not->toBeNull();
});

it('does not mark an opportunity as won when no won stage exists', function () {
    CrmTestHelper::actingAs();

    $firstStage = CrmTestHelper::firstStage();

    $opportunity = CrmTestHelper::opportunity(['stage_id' => $firstStage->id]);

    Stage::query()->where('is_won', true)->update(['is_won' => false]);

    expect($opportunity->markAsWon())->toBeFalse()
        ->and($opportunity->fresh()->stage_id)->toBe($firstStage->id);
});

it('marks an opportunity as lost with a reason and feedback and archives it', function () {
    CrmTestHelper::actingAs();

    $lostReason = LostReason::factory()->create();

    $opportunity = CrmTestHelper::opportunity(['probability' => 60]);

    $opportunity->markAsLost($lostReason->id, 'Went with a competitor');

    expect(Lead::query()->find($opportunity->id))->toBeNull();

    $lost = Lead::withTrashed()->findOrFail($opportunity->id);

    expect($lost->trashed())->toBeTrue()
        ->and($lost->isLost())->toBeTrue()
        ->and($lost->lost_reason_id)->toBe($lostReason->id)
        ->and($lost->lost_feedback)->toBe('Went with a competitor')
        ->and((float) $lost->probability)->toBe(0.0)
        ->and($lost->closed_at)->not->toBeNull();
});

it('clears the lost reason, feedback and closing date when restored', function () {
    CrmTestHelper::actingAs();

    $opportunity = CrmTestHelper::opportunity();

    $opportunity->markAsLost(LostReason::factory()->create()->id, 'Budget cut');

    $lost = Lead::withTrashed()->findOrFail($opportunity->id);

    $lost->restore();

    $restored = Lead::query()->findOrFail($opportunity->id);

    expect($restored->trashed())->toBeFalse()
        ->and($restored->lost_reason_id)->toBeNull()
        ->and($restored->lost_feedback)->toBeNull()
        ->and($restored->closed_at)->toBeNull();
});

it('converts a lead into an opportunity and creates its customer', function () {
    $user = CrmTestHelper::actingAs();

    $otherUser = User::query()->whereKeyNot($user->id)->firstOrFail();
    $team = Team::factory()->create(['company_id' => CrmTestHelper::companyId()]);

    $lead = CrmTestHelper::lead([
        'company_name' => 'Acme Industries',
        'email'        => 'sales@acme.test',
    ]);

    $converted = $lead->convertToOpportunity([
        'customer' => 'create',
        'user_id'  => $otherUser->id,
        'team_id'  => $team->id,
    ]);

    $converted->refresh();

    expect($converted->type)->toBe(Lead::TYPE_OPPORTUNITY)
        ->and($converted->isLead())->toBeFalse()
        ->and($converted->partner_id)->not->toBeNull()
        ->and($converted->partner->name)->toBe('Acme Industries')
        ->and($converted->partner->email)->toBe('sales@acme.test')
        ->and($converted->user_id)->toBe($otherUser->id)
        ->and($converted->team_id)->toBe($team->id)
        ->and($converted->converted_at)->not->toBeNull();
});

it('converts a lead into an opportunity linked to an existing customer', function () {
    CrmTestHelper::actingAs();

    $partner = Partner::factory()->create();

    $lead = CrmTestHelper::lead();

    $partnerCount = Partner::query()->count();

    $lead->convertToOpportunity(['customer' => 'link', 'partner_id' => $partner->id]);

    $lead->refresh();

    expect($lead->type)->toBe(Lead::TYPE_OPPORTUNITY)
        ->and($lead->partner_id)->toBe($partner->id)
        ->and($lead->converted_at)->not->toBeNull()
        ->and(Partner::query()->count())->toBe($partnerCount);
});

it('converts a lead into an opportunity without a customer and keeps its salesperson and team', function () {
    $user = CrmTestHelper::actingAs();

    $team = Team::factory()->create(['company_id' => CrmTestHelper::companyId()]);

    $lead = CrmTestHelper::lead(['team_id' => $team->id, 'partner_id' => null]);

    $partnerCount = Partner::query()->count();

    $lead->convertToOpportunity(['customer' => 'none']);

    $lead->refresh();

    expect($lead->type)->toBe(Lead::TYPE_OPPORTUNITY)
        ->and($lead->partner_id)->toBeNull()
        ->and($lead->user_id)->toBe($user->id)
        ->and($lead->team_id)->toBe($team->id)
        ->and($lead->converted_at)->not->toBeNull()
        ->and(Partner::query()->count())->toBe($partnerCount);
});

it('creates a company customer when the lead has a company name', function () {
    $user = CrmTestHelper::actingAs();

    $lead = CrmTestHelper::lead([
        'company_name' => 'Globex Corporation',
        'contact_name' => 'Hank Scorpio',
        'job_title'    => 'CEO',
        'email'        => 'info@globex.test',
        'phone'        => '+1 555 0100',
        'website'      => 'https://globex.test',
        'city'         => 'Cypress Creek',
        'zip'          => '12345',
    ]);

    $partner = $lead->createCustomer();

    expect($partner->exists)->toBeTrue()
        ->and($partner->account_type)->toBe(AccountType::COMPANY)
        ->and($partner->name)->toBe('Globex Corporation')
        ->and($partner->job_title)->toBeNull()
        ->and($partner->email)->toBe('info@globex.test')
        ->and($partner->phone)->toBe('+1 555 0100')
        ->and($partner->website)->toBe('https://globex.test')
        ->and($partner->city)->toBe('Cypress Creek')
        ->and($partner->zip)->toBe('12345')
        ->and($partner->company_id)->toBe($lead->company_id)
        ->and($partner->creator_id)->toBe($user->id);
});

it('creates an individual customer when the lead has no company name', function () {
    CrmTestHelper::actingAs();

    $lead = CrmTestHelper::lead([
        'company_name' => null,
        'contact_name' => 'Jane Roe',
        'job_title'    => 'CTO',
        'email'        => 'jane@roe.test',
    ]);

    $partner = $lead->createCustomer();

    expect($partner->account_type)->toBe(AccountType::INDIVIDUAL)
        ->and($partner->name)->toBe('Jane Roe')
        ->and($partner->job_title)->toBe('CTO')
        ->and($partner->email)->toBe('jane@roe.test');
});

it('names an individual customer after the lead when there is no contact name', function () {
    CrmTestHelper::actingAs();

    $lead = CrmTestHelper::lead([
        'name'         => 'Inbound enquiry',
        'company_name' => null,
        'contact_name' => null,
    ]);

    expect($lead->createCustomer()->name)->toBe('Inbound enquiry');
});

it('filters leads, opportunities, won and open records with scopes', function () {
    CrmTestHelper::actingAs();

    $lead = CrmTestHelper::lead(['stage_id' => CrmTestHelper::firstStage()->id]);
    $openOpportunity = CrmTestHelper::opportunity(['stage_id' => CrmTestHelper::firstStage()->id]);
    $wonOpportunity = CrmTestHelper::opportunity(['stage_id' => CrmTestHelper::wonStage()->id]);

    $ids = [$lead->id, $openOpportunity->id, $wonOpportunity->id];

    $scoped = fn (string $scope): array => Lead::query()->whereKey($ids)->{$scope}()->pluck('id')->sort()->values()->all();

    expect($scoped('leads'))->toBe([$lead->id])
        ->and($scoped('opportunities'))->toBe([$openOpportunity->id, $wonOpportunity->id])
        ->and($scoped('won'))->toBe([$wonOpportunity->id])
        ->and($scoped('open'))->toBe([$lead->id, $openOpportunity->id]);
});

it('reports whether a record is a lead, won or lost', function () {
    CrmTestHelper::actingAs();

    $lead = CrmTestHelper::lead(['stage_id' => CrmTestHelper::firstStage()->id]);
    $wonOpportunity = CrmTestHelper::opportunity(['stage_id' => CrmTestHelper::wonStage()->id]);
    $lostOpportunity = CrmTestHelper::opportunity();

    $lostOpportunity->markAsLost(null);

    expect($lead->isLead())->toBeTrue()
        ->and($lead->isWon())->toBeFalse()
        ->and($lead->isLost())->toBeFalse()
        ->and($wonOpportunity->isLead())->toBeFalse()
        ->and($wonOpportunity->isWon())->toBeTrue()
        ->and($wonOpportunity->isLost())->toBeFalse()
        ->and($lostOpportunity->isLost())->toBeTrue();
});

it('attaches tags through the crm_tag_rel table', function () {
    CrmTestHelper::actingAs();

    $opportunity = CrmTestHelper::opportunity();
    $tags = Tag::factory()->count(2)->create();

    $opportunity->tags()->attach($tags->pluck('id'));

    expect($opportunity->tags()->pluck('crm_tags.id')->sort()->values()->all())
        ->toBe($tags->pluck('id')->sort()->values()->all());

    $this->assertDatabaseHas('crm_tag_rel', ['lead_id' => $opportunity->id, 'tag_id' => $tags->first()->id]);
});

it('lists only scheduled activities as crm activities', function () {
    CrmTestHelper::actingAs();

    $opportunity = CrmTestHelper::opportunity();

    $activity = $opportunity->addActivity([
        'summary'       => 'Call the customer',
        'date_deadline' => now()->addDay(),
    ]);

    $opportunity->addMessage(['type' => 'comment', 'body' => 'Just a note']);

    $activities = $opportunity->crmActivities()->get();

    expect($activities)->toHaveCount(1)
        ->and($activities->first())->toBeInstanceOf(Activity::class)
        ->and($activities->first()->id)->toBe($activity->id);
});
