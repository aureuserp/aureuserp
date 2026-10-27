<?php

use Livewire\Livewire;
use Webkul\Crm\Enums\LeadType;
use Webkul\Crm\Filament\Actions\MergeLeadsBulkAction;
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

require_once __DIR__.'/../../../Helpers/CRMHelper.php';

beforeEach(fn () => CRMHelper::bootstrap());

describe('finding similar records', function () {
    it('stores a normalized email and a digits-only phone', function () {
        CRMHelper::actingAs();

        $lead = CRMHelper::lead(['email' => '  Robin@Example.COM ', 'phone' => '+1 (555) 010-1999']);

        expect($lead->email_normalized)->toBe('robin@example.com')
            ->and($lead->phone_sanitized)->toBe('15550101999');
    });

    it('ignores phone numbers that are too short to compare', function () {
        expect(Lead::sanitizePhone('12-34'))->toBeNull()
            ->and(Lead::sanitizePhone(null))->toBeNull()
            ->and(Lead::normalizeEmail('  '))->toBeNull();
    });

    it('matches leads and opportunities by email, phone or customer', function () {
        CRMHelper::actingAs();

        $partner = Partner::factory()->create(['company_id' => CRMHelper::companyId()]);

        $record = CRMHelper::lead(['email' => 'robin@acme.test', 'phone' => '+1 555 777 0001', 'partner_id' => $partner->id]);

        $sameEmail = CRMHelper::opportunity(['email' => 'ROBIN@acme.test']);
        $samePhone = CRMHelper::lead(['email' => null, 'phone' => '15557770001']);
        $sameCustomer = CRMHelper::opportunity(['email' => null, 'phone' => null, 'partner_id' => $partner->id]);
        $sameDomainOtherPerson = CRMHelper::lead(['email' => 'sam@acme.test', 'phone' => null]);

        $ids = $record->similarRecordsQuery()->pluck('id');

        expect($ids)->toContain($sameEmail->id, $samePhone->id, $sameCustomer->id)
            ->not->toContain($sameDomainOtherPerson->id, $record->id);
    });

    it('includes lost records and leaves out other companies', function () {
        CRMHelper::actingAs();

        $record = CRMHelper::lead(['email' => 'lost-match@acme.test']);

        $lost = CRMHelper::opportunity(['email' => 'lost-match@acme.test']);
        $lost->markAsLost(null);

        $otherCompany = Lead::withoutGlobalScopes()->forceCreate([
            'type'       => LeadType::LEAD,
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
        CRMHelper::actingAsCRMUser(['lead', 'opportunity']);

        $record = CRMHelper::lead(['email' => 'tab@acme.test']);
        CRMHelper::opportunity(['email' => 'tab@acme.test']);

        $page = Livewire::test(LeadResource\Pages\ViewLead::class, ['record' => $record->id])->instance();

        $items = SimilarLeads::navigationItems($page, SimilarLeadsPage::class);

        expect($items)->toHaveCount(1)
            ->and($items[0]->getBadge())->toBe('1')
            ->and($items[0]->getUrl())->toContain('/'.$record->id.'/similar');
    });

    it('has no similar leads tab when nothing matches', function () {
        CRMHelper::actingAsCRMUser(['lead']);

        $record = CRMHelper::lead(['email' => 'unique-'.uniqid().'@acme.test', 'phone' => null]);

        $page = Livewire::test(LeadResource\Pages\ViewLead::class, ['record' => $record->id])->instance();

        expect(SimilarLeads::navigationItems($page, SimilarLeadsPage::class))->toBeEmpty();
    });

    it('shows the record and its matches on the similar board, lost ones included', function () {
        CRMHelper::actingAsCRMUser(['lead', 'opportunity']);

        $record = CRMHelper::opportunity(['email' => 'board@acme.test']);
        $lead = CRMHelper::lead(['email' => 'board@acme.test']);
        $lost = CRMHelper::opportunity(['email' => 'board@acme.test']);
        $lost->markAsLost(null);

        $ids = Livewire::test(SimilarOpportunities::class, ['record' => $record->id])
            ->assertSuccessful()
            ->instance()
            ->getSimilarGroup()
            ->modelKeys();

        expect($ids)->toContain($record->id, $lead->id, $lost->id);
    });

    it('does not move cards on the similar board', function () {
        CRMHelper::actingAsCRMUser(['opportunity']);

        $record = CRMHelper::opportunity(['email' => 'nomove@acme.test']);
        CRMHelper::opportunity(['email' => 'nomove@acme.test']);

        $stageBefore = $record->stage_id;
        $otherStage = Stage::query()->whereKeyNot($stageBefore)->value('id');

        Livewire::test(SimilarOpportunities::class, ['record' => $record->id])
            ->call('moveRecord', (string) $record->id, (string) $otherStage, []);

        expect($record->fresh()->stage_id)->toBe($stageBefore);
    });

    it('only counts and shows similar records the user may view', function () {
        CRMHelper::actingAsCRMUser(['lead', 'opportunity']);

        $record = CRMHelper::opportunity(['email' => 'visible@acme.test']);
        $lead = CRMHelper::lead(['email' => 'visible@acme.test']);
        $opportunity = CRMHelper::opportunity(['email' => 'visible@acme.test']);

        CRMHelper::actingAsCRMUser(['opportunity']);

        $visible = SimilarLeads::visibleRecords($record)->modelKeys();

        expect($visible)->toContain($opportunity->id)->not->toContain($lead->id)
            ->and(SimilarLeads::count($record))->toBe(1);

        $board = Livewire::test(SimilarOpportunities::class, ['record' => $record->id])->instance()->getSimilarGroup()->modelKeys();

        expect($board)->not->toContain($lead->id);
    });

    it('shows the similar records under the record tabs with kanban, list and graph tabs', function () {
        CRMHelper::actingAsCRMUser(['lead', 'opportunity']);

        $record = CRMHelper::lead(['email' => 'views@acme.test']);
        $match = CRMHelper::opportunity(['email' => 'views@acme.test']);

        $kanban = Livewire::test(SimilarLeadsPage::class, ['record' => $record->id])
            ->assertSuccessful()
            ->assertActionDoesNotExist('mergeLeads');

        expect(collect($kanban->instance()->getRecordNavigationTabs())->map->getLabel()->all())
            ->toContain(__('crm::filament/similar-leads.navigation.label'))
            ->and(collect($kanban->instance()->getSimilarViewTabs())->map(fn ($tab): bool => $tab->isActive())->all())
            ->toBe([true, false, false]);

        Livewire::withQueryParams(['view' => 'list'])
            ->test(SimilarLeadsPage::class, ['record' => $record->id])
            ->assertCanSeeTableRecords([$record, $match])
            ->assertTableBulkActionExists('mergeLeads');

        Livewire::withQueryParams(['view' => 'list'])
            ->test(SimilarLeadsPage::class, ['record' => $record->id])
            ->searchTable('views-no-match')
            ->assertCanNotSeeTableRecords([$record, $match]);

        Livewire::withQueryParams(['view' => 'graph'])
            ->test(SimilarLeadsPage::class, ['record' => $record->id])
            ->assertSuccessful()
            ->assertSet('similarView', 'graph');
    });

    it('forbids the similar board without permission to view the record', function () {
        CRMHelper::actingAsCRMUser(['opportunity']);

        $record = CRMHelper::opportunity(['email' => 'forbidden@acme.test']);

        CRMHelper::actingAs(['view_any_crm_opportunity']);

        Livewire::test(SimilarOpportunities::class, ['record' => $record->id])->assertForbidden();
    });
});

describe('merging', function () {
    it('keeps the most advanced opportunity by default', function () {
        CRMHelper::actingAs();

        $lead = CRMHelper::lead(['email' => 'head@acme.test']);
        $opportunity = CRMHelper::opportunity(['email' => 'head@acme.test']);

        expect(app(LeadMerger::class)->pickHead(collect([$lead, $opportunity]))->id)->toBe($opportunity->id);
    });

    it('merges fields, notes, tags, chatter and history into the kept record and deletes the others', function () {
        $user = CRMHelper::actingAsCRMUser(['lead', 'opportunity']);

        $tagA = Tag::factory()->create();
        $tagB = Tag::factory()->create();

        $kept = CRMHelper::opportunity(['email' => 'merge@acme.test', 'city' => null, 'contact_name' => null, 'description' => '<p>Budget approved</p>']);
        $kept->tags()->sync([$tagA->id]);

        $other = CRMHelper::lead(['email' => 'merge@acme.test', 'city' => 'Austin', 'contact_name' => 'Robin', 'description' => '<p>Wants desks</p>']);
        $other->tags()->sync([$tagB->id]);

        $message = $other->addMessage(['type' => 'comment', 'body' => 'Note on the duplicate']);
        $log = ActivityLog::query()->create(['lead_id' => $other->id, 'lead_type' => LeadType::LEAD, 'done_at' => now(), 'company_id' => CRMHelper::companyId()]);

        $merged = app(LeadMerger::class)->merge(collect([$kept, $other]), $kept);

        expect($merged->id)->toBe($kept->id)
            ->and($merged->type)->toBe(LeadType::OPPORTUNITY)
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
        CRMHelper::actingAs();

        $lost = CRMHelper::opportunity(['email' => 'revive@acme.test']);
        $lost->markAsLost(null, 'Too expensive');

        $active = CRMHelper::lead(['email' => 'revive@acme.test']);

        $merged = app(LeadMerger::class)->merge(collect([Lead::withTrashed()->find($lost->id), $active]), Lead::withTrashed()->find($lost->id));

        expect($merged->trashed())->toBeFalse()
            ->and($merged->lost_reason_id)->toBeNull();
    });

    it('turns the result into an opportunity when one of the records is an opportunity', function () {
        CRMHelper::actingAs();

        $lead = CRMHelper::lead(['email' => 'type@acme.test']);
        $opportunity = CRMHelper::opportunity(['email' => 'type@acme.test']);

        $merged = app(LeadMerger::class)->merge(collect([$lead, $opportunity]), $lead);

        expect($merged->type)->toBe(LeadType::OPPORTUNITY)
            ->and($merged->converted_at)->not->toBeNull();
    });

    it('refuses to merge a single record or records of different companies', function () {
        CRMHelper::actingAs();

        $lead = CRMHelper::lead();

        $foreign = Lead::withoutGlobalScopes()->forceCreate([
            'type'       => LeadType::LEAD,
            'name'       => 'Foreign',
            'company_id' => Company::factory()->create()->id,
        ]);

        expect(fn () => app(LeadMerger::class)->merge(collect([$lead])))->toThrow(InvalidArgumentException::class)
            ->and(fn () => app(LeadMerger::class)->merge(collect([$lead, $foreign])))->toThrow(InvalidArgumentException::class);
    });

    it('requires edit permission on the kept record and force delete permission on the others', function () {
        CRMHelper::actingAsCRMUser(['opportunity']);

        $kept = CRMHelper::opportunity(['email' => 'perm@acme.test']);
        $other = CRMHelper::opportunity(['email' => 'perm@acme.test']);

        expect(MergeLeadsBulkAction::canMerge($kept, collect([$other])))->toBeTrue();

        CRMHelper::actingAs(CRMHelper::permissionsFor(['opportunity'], ['view_any', 'view', 'update', 'delete', 'delete_any']));

        expect(MergeLeadsBulkAction::canMerge($kept, collect([$other])))->toBeFalse();
    });

    it('requires restore permission when the kept record is lost', function () {
        CRMHelper::actingAsCRMUser(['opportunity']);

        $kept = CRMHelper::opportunity(['email' => 'perm-lost@acme.test']);
        $kept->markAsLost(null);
        $kept = Lead::withTrashed()->find($kept->id);

        $other = CRMHelper::opportunity(['email' => 'perm-lost@acme.test']);

        expect(MergeLeadsBulkAction::canMerge($kept, collect([$other])))->toBeTrue();

        CRMHelper::actingAs(CRMHelper::permissionsFor(['opportunity'], ['view_any', 'view', 'update', 'force_delete', 'force_delete_any']));

        expect(MergeLeadsBulkAction::canMerge($kept, collect([$other])))->toBeFalse();
    });

    it('merges from the similar records list', function () {
        CRMHelper::actingAsCRMUser(['lead', 'opportunity']);

        $record = CRMHelper::opportunity(['email' => 'board-merge@acme.test']);
        $duplicate = CRMHelper::lead(['email' => 'board-merge@acme.test']);

        Livewire::withQueryParams(['view' => 'list'])
            ->test(SimilarOpportunities::class, ['record' => $record->id])
            ->callTableBulkAction('mergeLeads', [$record, $duplicate], ['head' => $record->id])
            ->assertHasNoTableBulkActionErrors();

        expect(Lead::withTrashed()->find($duplicate->id))->toBeNull()
            ->and(Lead::query()->find($record->id))->not->toBeNull();
    });

    it('ignores records outside the similar group when merging from the list', function () {
        CRMHelper::actingAsCRMUser(['lead', 'opportunity']);

        $record = CRMHelper::opportunity(['email' => 'scope@acme.test']);
        $duplicate = CRMHelper::lead(['email' => 'scope@acme.test']);
        $unrelated = CRMHelper::lead(['email' => 'unrelated@acme.test']);

        Livewire::withQueryParams(['view' => 'list'])
            ->test(SimilarOpportunities::class, ['record' => $record->id])
            ->assertCanNotSeeTableRecords([$unrelated])
            ->callTableBulkAction('mergeLeads', [$record, $duplicate, $unrelated], ['head' => $record->id]);

        expect(Lead::query()->find($unrelated->id))->not->toBeNull();
    });

    it('offers a salesperson and team choice only when the records differ', function () {
        $user = CRMHelper::actingAsCRMUser(['opportunity']);

        $otherUser = User::withoutEvents(fn () => User::factory()->create(['default_company_id' => CRMHelper::companyId(), 'is_active' => true]));

        $a = CRMHelper::opportunity(['email' => 'conflict@acme.test', 'user_id' => $user->id]);
        $b = CRMHelper::opportunity(['email' => 'conflict@acme.test', 'user_id' => $otherUser->id]);
        $c = CRMHelper::opportunity(['email' => 'conflict@acme.test', 'user_id' => $user->id]);

        expect(MergeLeadsBulkAction::conflictOptions(collect([$a, $b]), 'user_id', 'user'))->toHaveCount(2)
            ->and(MergeLeadsBulkAction::conflictOptions(collect([$a, $c]), 'user_id', 'user'))->toHaveCount(1);
    });

    it('requires choosing the salesperson when the merged records have different ones', function () {
        $user = CRMHelper::actingAsCRMUser(['opportunity']);

        $otherUser = User::withoutEvents(fn () => User::factory()->create(['default_company_id' => CRMHelper::companyId(), 'is_active' => true]));

        $record = CRMHelper::opportunity(['email' => 'choose@acme.test', 'user_id' => $user->id]);
        $duplicate = CRMHelper::opportunity(['email' => 'choose@acme.test', 'user_id' => $otherUser->id]);

        Livewire::withQueryParams(['view' => 'list'])
            ->test(SimilarOpportunities::class, ['record' => $record->id])
            ->callTableBulkAction('mergeLeads', [$record, $duplicate], ['head' => $record->id, 'user_id' => null])
            ->assertHasTableBulkActionErrors(['user_id' => 'required']);

        expect(Lead::query()->find($duplicate->id))->not->toBeNull();

        Livewire::withQueryParams(['view' => 'list'])
            ->test(SimilarOpportunities::class, ['record' => $record->id])
            ->callTableBulkAction('mergeLeads', [$record, $duplicate], ['head' => $record->id, 'user_id' => $otherUser->id])
            ->assertHasNoTableBulkActionErrors();

        expect($record->fresh()->user_id)->toBe($otherUser->id)
            ->and(Lead::withTrashed()->find($duplicate->id))->toBeNull();
    });

    it('refuses a salesperson that none of the merged records has', function () {
        $user = CRMHelper::actingAsCRMUser(['opportunity']);

        $otherUser = User::withoutEvents(fn () => User::factory()->create(['default_company_id' => CRMHelper::companyId(), 'is_active' => true]));

        $a = CRMHelper::opportunity(['email' => 'outsider@acme.test', 'user_id' => $user->id]);
        $b = CRMHelper::opportunity(['email' => 'outsider@acme.test', 'user_id' => $otherUser->id]);

        expect(fn () => app(LeadMerger::class)->merge(collect([$a, $b]), $a, ['user_id' => 99999999]))
            ->toThrow(InvalidArgumentException::class);
    });

    it('merges selected records from the pipeline list', function () {
        CRMHelper::actingAsCRMUser(['opportunity']);

        $first = CRMHelper::opportunity(['email' => 'bulk@acme.test', 'expected_revenue' => 100]);
        $second = CRMHelper::opportunity(['email' => 'bulk@acme.test', 'expected_revenue' => 900]);

        Livewire::test(ListOpportunities::class)
            ->callTableBulkAction('mergeLeads', [$first, $second], ['head' => $second->id]);

        expect(Lead::withTrashed()->find($first->id))->toBeNull()
            ->and(Lead::query()->find($second->id))->not->toBeNull();
    });
});
