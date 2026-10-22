<?php

use Livewire\Livewire;
use Webkul\Crm\Filament\Actions\MergeLeadsAction;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages\SimilarLeads as SimilarLeadsPage;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\ListOpportunities;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\SimilarOpportunities;
use Webkul\Crm\Filament\Support\SimilarLeads;
use Webkul\Crm\Models\ActivityLog;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\LeadMerge;
use Webkul\Crm\Models\Stage;
use Webkul\Crm\Models\Tag;
use Webkul\Crm\Services\LeadMerger;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../Helpers/CrmTestHelper.php';

beforeEach(fn () => CrmTestHelper::bootstrap());

describe('finding similar records', function () {
    it('stores a normalized email and a digits-only phone', function () {
        CrmTestHelper::actingAs();

        $lead = CrmTestHelper::lead(['email' => '  Robin@Example.COM ', 'phone' => '+1 (555) 010-1999']);

        expect($lead->email_normalized)->toBe('robin@example.com')
            ->and($lead->phone_sanitized)->toBe('15550101999');
    });

    it('ignores phone numbers that are too short to compare', function () {
        expect(Lead::sanitizePhone('12-34'))->toBeNull()
            ->and(Lead::sanitizePhone(null))->toBeNull()
            ->and(Lead::normalizeEmail('  '))->toBeNull();
    });

    it('matches leads and opportunities by email, phone or customer', function () {
        CrmTestHelper::actingAs();

        $partner = Partner::factory()->create(['company_id' => CrmTestHelper::companyId()]);

        $record = CrmTestHelper::lead(['email' => 'robin@acme.test', 'phone' => '+1 555 777 0001', 'partner_id' => $partner->id]);

        $sameEmail = CrmTestHelper::opportunity(['email' => 'ROBIN@acme.test']);
        $samePhone = CrmTestHelper::lead(['email' => null, 'phone' => '15557770001']);
        $sameCustomer = CrmTestHelper::opportunity(['email' => null, 'phone' => null, 'partner_id' => $partner->id]);
        $sameDomainOtherPerson = CrmTestHelper::lead(['email' => 'sam@acme.test', 'phone' => null]);

        $ids = $record->similarRecordsQuery()->pluck('id');

        expect($ids)->toContain($sameEmail->id, $samePhone->id, $sameCustomer->id)
            ->not->toContain($sameDomainOtherPerson->id, $record->id);
    });

    it('includes lost records and leaves out other companies', function () {
        CrmTestHelper::actingAs();

        $record = CrmTestHelper::lead(['email' => 'lost-match@acme.test']);

        $lost = CrmTestHelper::opportunity(['email' => 'lost-match@acme.test']);
        $lost->markAsLost(null);

        $otherCompany = Lead::withoutGlobalScopes()->forceCreate([
            'type'       => Lead::TYPE_LEAD,
            'name'       => 'Other company duplicate',
            'email'      => 'lost-match@acme.test',
            'company_id' => Company::factory()->create()->id,
        ]);

        $ids = $record->similarRecordsQuery()->pluck('id');

        expect($ids)->toContain($lost->id)->not->toContain($otherCompany->id);
    });
});

describe('similar leads tab and board', function () {
    it('shows the similar leads tab with the number of matches', function () {
        CrmTestHelper::actingAsCrmUser(['lead', 'opportunity']);

        $record = CrmTestHelper::lead(['email' => 'tab@acme.test']);
        CrmTestHelper::opportunity(['email' => 'tab@acme.test']);

        $page = Livewire::test(LeadResource\Pages\ViewLead::class, ['record' => $record->id])->instance();

        $items = SimilarLeads::navigationItems($page, SimilarLeadsPage::class);

        expect($items)->toHaveCount(1)
            ->and($items[0]->getBadge())->toBe('1')
            ->and($items[0]->getUrl())->toContain('/'.$record->id.'/similar');
    });

    it('has no similar leads tab when nothing matches', function () {
        CrmTestHelper::actingAsCrmUser(['lead']);

        $record = CrmTestHelper::lead(['email' => 'unique-'.uniqid().'@acme.test', 'phone' => null]);

        $page = Livewire::test(LeadResource\Pages\ViewLead::class, ['record' => $record->id])->instance();

        expect(SimilarLeads::navigationItems($page, SimilarLeadsPage::class))->toBeEmpty();
    });

    it('shows the record and its matches on the similar board, lost ones included', function () {
        CrmTestHelper::actingAsCrmUser(['lead', 'opportunity']);

        $record = CrmTestHelper::opportunity(['email' => 'board@acme.test']);
        $lead = CrmTestHelper::lead(['email' => 'board@acme.test']);
        $lost = CrmTestHelper::opportunity(['email' => 'board@acme.test']);
        $lost->markAsLost(null);

        $ids = Livewire::test(SimilarOpportunities::class, ['record' => $record->id])
            ->assertSuccessful()
            ->assertActionVisible('mergeLeads')
            ->instance()
            ->getSimilarGroup()
            ->modelKeys();

        expect($ids)->toContain($record->id, $lead->id, $lost->id);
    });

    it('does not move cards on the similar board', function () {
        CrmTestHelper::actingAsCrmUser(['opportunity']);

        $record = CrmTestHelper::opportunity(['email' => 'nomove@acme.test']);
        CrmTestHelper::opportunity(['email' => 'nomove@acme.test']);

        $stageBefore = $record->stage_id;
        $otherStage = Stage::query()->whereKeyNot($stageBefore)->value('id');

        Livewire::test(SimilarOpportunities::class, ['record' => $record->id])
            ->call('moveRecord', (string) $record->id, (string) $otherStage, []);

        expect($record->fresh()->stage_id)->toBe($stageBefore);
    });

    it('only counts and shows similar records the user may view', function () {
        CrmTestHelper::actingAsCrmUser(['lead', 'opportunity']);

        $record = CrmTestHelper::opportunity(['email' => 'visible@acme.test']);
        $lead = CrmTestHelper::lead(['email' => 'visible@acme.test']);
        $opportunity = CrmTestHelper::opportunity(['email' => 'visible@acme.test']);

        CrmTestHelper::actingAsCrmUser(['opportunity']);

        $visible = SimilarLeads::visibleRecords($record)->modelKeys();

        expect($visible)->toContain($opportunity->id)->not->toContain($lead->id)
            ->and(SimilarLeads::count($record))->toBe(1);

        $board = Livewire::test(SimilarOpportunities::class, ['record' => $record->id])->instance()->getSimilarGroup()->modelKeys();

        expect($board)->not->toContain($lead->id);
    });

    it('forbids the similar board without permission to view the record', function () {
        CrmTestHelper::actingAsCrmUser(['opportunity']);

        $record = CrmTestHelper::opportunity(['email' => 'forbidden@acme.test']);

        CrmTestHelper::actingAs(['view_any_crm_opportunity']);

        Livewire::test(SimilarOpportunities::class, ['record' => $record->id])->assertForbidden();
    });
});

describe('merging', function () {
    it('keeps the most advanced opportunity by default', function () {
        CrmTestHelper::actingAs();

        $lead = CrmTestHelper::lead(['email' => 'head@acme.test']);
        $opportunity = CrmTestHelper::opportunity(['email' => 'head@acme.test']);

        expect(app(LeadMerger::class)->pickHead(collect([$lead, $opportunity]))->id)->toBe($opportunity->id);
    });

    it('merges fields, notes, tags, chatter and history into the kept record and deletes the others', function () {
        $user = CrmTestHelper::actingAsCrmUser(['lead', 'opportunity']);

        $tagA = Tag::factory()->create();
        $tagB = Tag::factory()->create();

        $kept = CrmTestHelper::opportunity(['email' => 'merge@acme.test', 'city' => null, 'contact_name' => null, 'description' => '<p>Budget approved</p>']);
        $kept->tags()->sync([$tagA->id]);

        $other = CrmTestHelper::lead(['email' => 'merge@acme.test', 'city' => 'Austin', 'contact_name' => 'Robin', 'description' => '<p>Wants desks</p>']);
        $other->tags()->sync([$tagB->id]);

        $message = $other->addMessage(['type' => 'comment', 'body' => 'Note on the duplicate']);
        $log = ActivityLog::query()->create(['lead_id' => $other->id, 'lead_type' => Lead::TYPE_LEAD, 'done_at' => now(), 'company_id' => CrmTestHelper::companyId()]);

        $merged = app(LeadMerger::class)->merge(collect([$kept, $other]), $kept);

        expect($merged->id)->toBe($kept->id)
            ->and($merged->type)->toBe(Lead::TYPE_OPPORTUNITY)
            ->and($merged->city)->toBe('Austin')
            ->and($merged->contact_name)->toBe('Robin')
            ->and($merged->description)->toContain('Budget approved')->toContain('Wants desks')
            ->and($merged->tags()->pluck('crm_tags.id')->all())->toContain($tagA->id, $tagB->id)
            ->and(Lead::withTrashed()->find($other->id))->toBeNull()
            ->and($message->fresh()->messageable_id)->toBe($kept->id)
            ->and($log->fresh()->lead_id)->toBe($kept->id);

        $mergeRecord = LeadMerge::query()->where('lead_id', $kept->id)->sole();

        expect($mergeRecord->merged_by)->toBe($user->id)
            ->and($mergeRecord->merged_records[0]['id'])->toBe($other->id)
            ->and($mergeRecord->merged_records[0]['name'])->toBe($other->name);
    });

    it('makes the result active when the kept record was lost', function () {
        CrmTestHelper::actingAs();

        $lost = CrmTestHelper::opportunity(['email' => 'revive@acme.test']);
        $lost->markAsLost(null, 'Too expensive');

        $active = CrmTestHelper::lead(['email' => 'revive@acme.test']);

        $merged = app(LeadMerger::class)->merge(collect([Lead::withTrashed()->find($lost->id), $active]), Lead::withTrashed()->find($lost->id));

        expect($merged->trashed())->toBeFalse()
            ->and($merged->lost_reason_id)->toBeNull();
    });

    it('turns the result into an opportunity when one of the records is an opportunity', function () {
        CrmTestHelper::actingAs();

        $lead = CrmTestHelper::lead(['email' => 'type@acme.test']);
        $opportunity = CrmTestHelper::opportunity(['email' => 'type@acme.test']);

        $merged = app(LeadMerger::class)->merge(collect([$lead, $opportunity]), $lead);

        expect($merged->type)->toBe(Lead::TYPE_OPPORTUNITY)
            ->and($merged->converted_at)->not->toBeNull();
    });

    it('refuses to merge a single record or records of different companies', function () {
        CrmTestHelper::actingAs();

        $lead = CrmTestHelper::lead();

        $foreign = Lead::withoutGlobalScopes()->forceCreate([
            'type'       => Lead::TYPE_LEAD,
            'name'       => 'Foreign',
            'company_id' => Company::factory()->create()->id,
        ]);

        expect(fn () => app(LeadMerger::class)->merge(collect([$lead])))->toThrow(InvalidArgumentException::class)
            ->and(fn () => app(LeadMerger::class)->merge(collect([$lead, $foreign])))->toThrow(InvalidArgumentException::class);
    });

    it('requires edit permission on the kept record and force delete permission on the others', function () {
        CrmTestHelper::actingAsCrmUser(['opportunity']);

        $kept = CrmTestHelper::opportunity(['email' => 'perm@acme.test']);
        $other = CrmTestHelper::opportunity(['email' => 'perm@acme.test']);

        expect(MergeLeadsAction::canMerge($kept, collect([$other])))->toBeTrue();

        CrmTestHelper::actingAs(CrmTestHelper::permissionsFor(['opportunity'], ['view_any', 'view', 'update', 'delete', 'delete_any']));

        expect(MergeLeadsAction::canMerge($kept, collect([$other])))->toBeFalse();
    });

    it('requires restore permission when the kept record is lost', function () {
        CrmTestHelper::actingAsCrmUser(['opportunity']);

        $kept = CrmTestHelper::opportunity(['email' => 'perm-lost@acme.test']);
        $kept->markAsLost(null);
        $kept = Lead::withTrashed()->find($kept->id);

        $other = CrmTestHelper::opportunity(['email' => 'perm-lost@acme.test']);

        expect(MergeLeadsAction::canMerge($kept, collect([$other])))->toBeTrue();

        CrmTestHelper::actingAs(CrmTestHelper::permissionsFor(['opportunity'], ['view_any', 'view', 'update', 'force_delete', 'force_delete_any']));

        expect(MergeLeadsAction::canMerge($kept, collect([$other])))->toBeFalse();
    });

    it('merges from the similar board', function () {
        CrmTestHelper::actingAsCrmUser(['lead', 'opportunity']);

        $record = CrmTestHelper::opportunity(['email' => 'board-merge@acme.test']);
        $duplicate = CrmTestHelper::lead(['email' => 'board-merge@acme.test']);

        Livewire::test(SimilarOpportunities::class, ['record' => $record->id])
            ->callAction('mergeLeads', ['records' => [$record->id, $duplicate->id], 'head' => $record->id])
            ->assertHasNoActionErrors();

        expect(Lead::withTrashed()->find($duplicate->id))->toBeNull()
            ->and(Lead::query()->find($record->id))->not->toBeNull();
    });

    it('ignores records outside the similar group when merging from the board', function () {
        CrmTestHelper::actingAsCrmUser(['lead', 'opportunity']);

        $record = CrmTestHelper::opportunity(['email' => 'scope@acme.test']);
        $duplicate = CrmTestHelper::lead(['email' => 'scope@acme.test']);
        $unrelated = CrmTestHelper::lead(['email' => 'unrelated@acme.test']);

        Livewire::test(SimilarOpportunities::class, ['record' => $record->id])
            ->callAction('mergeLeads', ['records' => [$record->id, $duplicate->id, $unrelated->id], 'head' => $record->id]);

        expect(Lead::query()->find($unrelated->id))->not->toBeNull();
    });

    it('offers a salesperson and team choice only when the records differ', function () {
        $user = CrmTestHelper::actingAsCrmUser(['opportunity']);

        $otherUser = User::withoutEvents(fn () => User::factory()->create(['default_company_id' => CrmTestHelper::companyId(), 'is_active' => true]));

        $a = CrmTestHelper::opportunity(['email' => 'conflict@acme.test', 'user_id' => $user->id]);
        $b = CrmTestHelper::opportunity(['email' => 'conflict@acme.test', 'user_id' => $otherUser->id]);
        $c = CrmTestHelper::opportunity(['email' => 'conflict@acme.test', 'user_id' => $user->id]);

        expect(MergeLeadsAction::conflictOptions(collect([$a, $b]), 'user_id', 'user'))->toHaveCount(2)
            ->and(MergeLeadsAction::conflictOptions(collect([$a, $c]), 'user_id', 'user'))->toHaveCount(1);
    });

    it('requires choosing the salesperson when the merged records have different ones', function () {
        $user = CrmTestHelper::actingAsCrmUser(['opportunity']);

        $otherUser = User::withoutEvents(fn () => User::factory()->create(['default_company_id' => CrmTestHelper::companyId(), 'is_active' => true]));

        $record = CrmTestHelper::opportunity(['email' => 'choose@acme.test', 'user_id' => $user->id]);
        $duplicate = CrmTestHelper::opportunity(['email' => 'choose@acme.test', 'user_id' => $otherUser->id]);

        Livewire::test(SimilarOpportunities::class, ['record' => $record->id])
            ->callAction('mergeLeads', ['records' => [$record->id, $duplicate->id], 'head' => $record->id, 'user_id' => null])
            ->assertHasActionErrors(['user_id' => 'required']);

        expect(Lead::query()->find($duplicate->id))->not->toBeNull();

        Livewire::test(SimilarOpportunities::class, ['record' => $record->id])
            ->callAction('mergeLeads', ['records' => [$record->id, $duplicate->id], 'head' => $record->id, 'user_id' => $otherUser->id])
            ->assertHasNoActionErrors();

        expect($record->fresh()->user_id)->toBe($otherUser->id)
            ->and(Lead::withTrashed()->find($duplicate->id))->toBeNull();
    });

    it('refuses a salesperson that none of the merged records has', function () {
        $user = CrmTestHelper::actingAsCrmUser(['opportunity']);

        $otherUser = User::withoutEvents(fn () => User::factory()->create(['default_company_id' => CrmTestHelper::companyId(), 'is_active' => true]));

        $a = CrmTestHelper::opportunity(['email' => 'outsider@acme.test', 'user_id' => $user->id]);
        $b = CrmTestHelper::opportunity(['email' => 'outsider@acme.test', 'user_id' => $otherUser->id]);

        expect(fn () => app(LeadMerger::class)->merge(collect([$a, $b]), $a, ['user_id' => 99999999]))
            ->toThrow(InvalidArgumentException::class);
    });

    it('merges selected records from the pipeline list', function () {
        CrmTestHelper::actingAsCrmUser(['opportunity']);

        $first = CrmTestHelper::opportunity(['email' => 'bulk@acme.test', 'expected_revenue' => 100]);
        $second = CrmTestHelper::opportunity(['email' => 'bulk@acme.test', 'expected_revenue' => 900]);

        Livewire::test(ListOpportunities::class)
            ->callTableBulkAction('mergeLeads', [$first, $second], ['head' => $second->id]);

        expect(Lead::withTrashed()->find($first->id))->toBeNull()
            ->and(Lead::query()->find($second->id))->not->toBeNull();
    });
});
