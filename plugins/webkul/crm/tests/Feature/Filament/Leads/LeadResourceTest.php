<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;
use Webkul\Crm\Enums\LeadType;
use Webkul\Crm\Enums\OpportunityPriority;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages\CreateLead;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages\EditLead;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages\ListLeads;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages\ViewLead;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\ListOpportunities;
use Webkul\Crm\Filament\Support\LeadResourceResolver;
use Webkul\Crm\Models\Lead;
use Webkul\Crm\Models\LostReason;
use Webkul\Sale\Models\Team;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;
use Webkul\Security\Support\OwnerSource;
use Webkul\Security\Traits\HasOwnershipScope;
use Webkul\Support\Models\Company;
use Webkul\TableViews\Models\TableView;
use Webkul\TableViews\Models\TableViewFavorite;

require_once __DIR__.'/../../../Helpers/CRMHelper.php';

beforeEach(fn () => CRMHelper::bootstrap());

/**
 * An active user whose only company is the given one (the current company by default).
 */
function leadResourceUser(?int $companyId = null): User
{
    return User::withoutEvents(fn (): User => User::factory()->create([
        'default_company_id' => $companyId ?? CRMHelper::companyId(),
        'is_active'          => true,
    ]));
}

/**
 * A lead with no salesperson (the model assigns the signed-in user on create).
 */
function leadResourceUnassignedLead(): Lead
{
    $lead = CRMHelper::lead();

    Lead::query()->whereKey($lead->id)->update(['user_id' => null]);

    return $lead->fresh();
}

describe('list', function () {
    it('renders the list with the lead permission', function () {
        CRMHelper::actingAsCRMUser(['lead']);

        Livewire::test(ListLeads::class)->assertOk();
    });

    it('opens on the "My Leads" view with only the current user leads', function () {
        CRMHelper::actingAsCRMUser(['lead']);

        $mine = CRMHelper::lead();
        $theirs = CRMHelper::lead(['user_id' => leadResourceUser()->id]);
        $unassigned = leadResourceUnassignedLead();
        $opportunity = CRMHelper::opportunity();

        Livewire::test(ListLeads::class)
            ->assertSet('activeTableView', 'my_leads')
            ->assertCanSeeTableRecords([$mine])
            ->assertCanNotSeeTableRecords([$theirs, $unassigned, $opportunity]);
    });

    it('shows only leads without a salesperson on the "Unassigned" view', function () {
        CRMHelper::actingAsCRMUser(['lead']);

        $mine = CRMHelper::lead();
        $unassigned = leadResourceUnassignedLead();

        Livewire::test(ListLeads::class)
            ->call('loadView', 'unassigned')
            ->assertCanSeeTableRecords([$unassigned])
            ->assertCanNotSeeTableRecords([$mine]);
    });

    it('shows every active lead, but no opportunity or lost lead, on the "All" view', function () {
        CRMHelper::actingAsCRMUser(['lead']);

        $mine = CRMHelper::lead();
        $theirs = CRMHelper::lead(['user_id' => leadResourceUser()->id]);
        $unassigned = leadResourceUnassignedLead();
        $lost = CRMHelper::lead();
        $lost->markAsLost(null);
        $opportunity = CRMHelper::opportunity();

        Livewire::test(ListLeads::class)
            ->call('loadView', 'all')
            ->assertCanSeeTableRecords([$mine, $theirs, $unassigned])
            ->assertCanNotSeeTableRecords([$lost, $opportunity]);
    });

    it('shows only lost leads on the "Lost" view', function () {
        CRMHelper::actingAsCRMUser(['lead']);

        $active = CRMHelper::lead();
        $lost = CRMHelper::lead();
        $lost->markAsLost(null);
        $lostOpportunity = CRMHelper::opportunity();
        $lostOpportunity->markAsLost(null);

        Livewire::test(ListLeads::class)
            ->call('loadView', 'lost')
            ->assertCanSeeTableRecords([$lost])
            ->assertCanNotSeeTableRecords([$active, $lostOpportunity]);
    });

    it('offers the four preset views', function () {
        CRMHelper::actingAsCRMUser(['lead']);

        expect(array_keys(Livewire::test(ListLeads::class)->instance()->getPresetTableViews()))
            ->toBe(['my_leads', 'unassigned', 'all', 'lost']);
    });

    it('searches leads by contact name', function () {
        CRMHelper::actingAsCRMUser(['lead']);

        $match = CRMHelper::lead(['contact_name' => 'Quentin Searchable']);
        $other = CRMHelper::lead(['contact_name' => 'Somebody Else']);

        Livewire::test(ListLeads::class)
            ->searchTable('Quentin Searchable')
            ->assertCanSeeTableRecords([$match])
            ->assertCanNotSeeTableRecords([$other]);
    });
});

describe('create', function () {
    it('creates a lead with the form fields', function () {
        $user = CRMHelper::actingAsCRMUser(['lead']);

        $team = Team::factory()->create(['company_id' => CRMHelper::companyId()]);

        Livewire::test(CreateLead::class)
            ->fillForm([
                'name'         => 'Website redesign enquiry',
                'probability'  => 30,
                'company_name' => 'Globex Leads Ltd',
                'street1'      => '1 Main Street',
                'street2'      => 'Suite 5',
                'city'         => 'Springfield',
                'zip'          => '12345',
                'website'      => 'https://globex.example.com',
                'user_id'      => $user->id,
                'team_id'      => $team->id,
                'contact_name' => 'Hank Scorpio',
                'email'        => 'hank@globex.example.com',
                'job_title'    => 'CEO',
                'phone'        => '555-0100',
                'referred_by'  => 'Trade show',
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertRedirect();

        $lead = Lead::query()->where('name', 'Website redesign enquiry')->firstOrFail();

        expect($lead->type)->toBe(LeadType::LEAD)
            ->and((float) $lead->probability)->toBe(30.0)
            ->and($lead->company_name)->toBe('Globex Leads Ltd')
            ->and($lead->street1)->toBe('1 Main Street')
            ->and($lead->city)->toBe('Springfield')
            ->and($lead->contact_name)->toBe('Hank Scorpio')
            ->and($lead->email)->toBe('hank@globex.example.com')
            ->and($lead->job_title)->toBe('CEO')
            ->and($lead->referred_by)->toBe('Trade show')
            ->and($lead->user_id)->toBe($user->id)
            ->and($lead->team_id)->toBe($team->id)
            ->and($lead->company_id)->toBe(CRMHelper::companyId())
            ->and($lead->assigned_at)->not->toBeNull();
    });

    it('defaults the salesperson to the current user and the company to the current company', function () {
        $user = CRMHelper::actingAsCRMUser(['lead']);

        Livewire::test(CreateLead::class)
            ->assertSchemaStateSet([
                'user_id'     => $user->id,
                'company_id'  => CRMHelper::companyId(),
                'probability' => 10,
            ]);
    });

    it('validates the lead form', function (array $data, array $errors) {
        CRMHelper::actingAsCRMUser(['lead']);

        Livewire::test(CreateLead::class)
            ->fillForm(['name' => 'Valid lead name', ...$data])
            ->call('create')
            ->assertHasFormErrors($errors);
    })->with([
        'name is required'          => [['name' => null], ['name' => 'required']],
        'probability above 100'     => [['probability' => 150], ['probability' => 'max']],
        'probability below 0'       => [['probability' => -1], ['probability' => 'min']],
        'email must be an email'    => [['email' => 'not-an-email'], ['email' => 'email']],
        'website must be a url'     => [['website' => 'not a url'], ['website' => 'url']],
    ]);

    it('rejects a sales team of another company', function () {
        CRMHelper::actingAsCRMUser(['lead']);

        $foreignTeam = Team::factory()->create(['company_id' => Company::factory()->create()->id]);

        Livewire::test(CreateLead::class)
            ->fillForm(['name' => 'Lead with foreign team', 'team_id' => $foreignTeam->id])
            ->call('create')
            ->assertHasFormErrors(['team_id']);

        expect(Lead::query()->where('name', 'Lead with foreign team')->exists())->toBeFalse();
    });

    it('rejects a salesperson who is not allowed in the lead company', function () {
        CRMHelper::actingAsCRMUser(['lead']);

        $foreignUser = leadResourceUser(Company::factory()->create()->id);

        Livewire::test(CreateLead::class)
            ->fillForm(['name' => 'Lead with foreign salesperson', 'user_id' => $foreignUser->id])
            ->call('create')
            ->assertHasFormErrors(['user_id']);

        expect(Lead::query()->where('name', 'Lead with foreign salesperson')->exists())->toBeFalse();
    });

    it('rejects an inactive salesperson', function () {
        CRMHelper::actingAsCRMUser(['lead']);

        $inactiveUser = leadResourceUser();
        $inactiveUser->forceFill(['is_active' => false])->saveQuietly();

        Livewire::test(CreateLead::class)
            ->fillForm(['name' => 'Lead with inactive salesperson', 'user_id' => $inactiveUser->id])
            ->call('create')
            ->assertHasFormErrors(['user_id']);
    });

    it('forbids creating a lead without the lead create permission', function () {
        CRMHelper::actingAs(['view_any_crm_lead', 'view_crm_lead', 'create_crm_opportunity']);

        Livewire::test(CreateLead::class)->assertForbidden();
    });
});

describe('view', function () {
    it('shows a lead', function () {
        CRMHelper::actingAsCRMUser(['lead']);

        $lead = CRMHelper::lead(['name' => 'Viewable lead name', 'contact_name' => 'Viewable Contact']);

        Livewire::test(ViewLead::class, ['record' => $lead->id])
            ->assertOk()
            ->assertSee('Viewable lead name')
            ->assertSee('Viewable Contact')
            ->assertActionVisible('convertToOpportunity')
            ->assertActionVisible('markAsLost');
    });

    it('does not open an opportunity on the lead screen', function () {
        CRMHelper::actingAsCRMUser(['lead']);

        $opportunity = CRMHelper::opportunity();

        expect(fn () => Livewire::test(ViewLead::class, ['record' => $opportunity->id]))
            ->toThrow(ModelNotFoundException::class);
    });

    it('forbids viewing a lead without the lead view permission', function () {
        CRMHelper::actingAs(['view_any_crm_lead', 'view_crm_opportunity']);

        $lead = CRMHelper::lead();

        Livewire::test(ViewLead::class, ['record' => $lead->id])->assertForbidden();
    });

    it('shows the leads menu on the left of record pages', function () {
        CRMHelper::actingAsCRMUser(['lead']);

        $lead = CRMHelper::lead();

        expect(Livewire::test(ViewLead::class, ['record' => $lead->id])->instance()->getSubNavigation())->toHaveCount(1);
    });
});

describe('edit', function () {
    it('updates a lead', function () {
        CRMHelper::actingAsCRMUser(['lead']);

        $lead = CRMHelper::lead(['name' => 'Lead before edit']);

        Livewire::test(EditLead::class, ['record' => $lead->id])
            ->assertSchemaStateSet(['name' => 'Lead before edit'])
            ->fillForm([
                'name'         => 'Lead after edit',
                'company_name' => 'Edited Company',
                'probability'  => 45,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $lead->refresh();

        expect($lead->name)->toBe('Lead after edit')
            ->and($lead->company_name)->toBe('Edited Company')
            ->and((float) $lead->probability)->toBe(45.0)
            ->and($lead->type)->toBe(LeadType::LEAD);
    });

    it('sets the assignment date when the salesperson changes', function () {
        CRMHelper::actingAsCRMUser(['lead']);

        $lead = leadResourceUnassignedLead();
        Lead::query()->whereKey($lead->id)->update(['assigned_at' => null]);

        $salesperson = leadResourceUser();

        Livewire::test(EditLead::class, ['record' => $lead->id])
            ->fillForm(['user_id' => $salesperson->id])
            ->call('save')
            ->assertHasNoFormErrors();

        $lead->refresh();

        expect($lead->user_id)->toBe($salesperson->id)
            ->and($lead->assigned_at)->not->toBeNull();
    });

    it('forbids editing a lead without the lead update permission', function () {
        CRMHelper::actingAs(['view_any_crm_lead', 'view_crm_lead', 'update_crm_opportunity']);

        $lead = CRMHelper::lead();

        Livewire::test(EditLead::class, ['record' => $lead->id])->assertForbidden();
    });
});

describe('lost', function () {
    it('marks a lead as lost with a reason', function () {
        CRMHelper::actingAsCRMUser(['lead']);

        $lead = CRMHelper::lead();
        $reason = LostReason::factory()->create();

        Livewire::test(ViewLead::class, ['record' => $lead->id])
            ->callAction('markAsLost', ['lost_reason_id' => $reason->id, 'lost_feedback' => 'Went with a competitor'])
            ->assertHasNoFormErrors();

        $lead = Lead::withTrashed()->findOrFail($lead->id);

        expect($lead->trashed())->toBeTrue()
            ->and($lead->lost_reason_id)->toBe($reason->id)
            ->and($lead->lost_feedback)->toBe('Went with a competitor')
            ->and((float) $lead->probability)->toBe(0.0)
            ->and($lead->type)->toBe(LeadType::LEAD);
    });

    it('hides "Lost" and "Convert" on a lost lead and restores it', function () {
        CRMHelper::actingAsCRMUser(['lead']);

        $lead = CRMHelper::lead();
        $lead->markAsLost(LostReason::factory()->create()->id);

        Livewire::test(ViewLead::class, ['record' => $lead->id])
            ->assertActionHidden('markAsLost')
            ->assertActionHidden('convertToOpportunity')
            ->callAction('restore');

        $lead = Lead::withTrashed()->findOrFail($lead->id);

        expect($lead->trashed())->toBeFalse()
            ->and($lead->lost_reason_id)->toBeNull();
    });

    it('hides "Lost" without the lead delete permission', function () {
        CRMHelper::actingAs(['view_any_crm_lead', 'view_crm_lead', 'delete_crm_opportunity']);

        $lead = CRMHelper::lead();

        Livewire::test(ViewLead::class, ['record' => $lead->id])->assertActionHidden('markAsLost');
    });
});

describe('authorization', function () {
    it('forbids every lead page with only the opportunity permissions', function () {
        CRMHelper::actingAsCRMUser(['opportunity']);

        $lead = CRMHelper::lead();

        Livewire::test(ListLeads::class)->assertForbidden();
        Livewire::test(CreateLead::class)->assertForbidden();
        Livewire::test(ViewLead::class, ['record' => $lead->id])->assertForbidden();
        Livewire::test(EditLead::class, ['record' => $lead->id])->assertForbidden();
    });

    it('authorizes the leads screen with the lead policy, not the opportunity policy', function () {
        CRMHelper::actingAsCRMUser(['opportunity']);

        $lead = CRMHelper::lead();

        expect(LeadResource::canViewAny())->toBeFalse()
            ->and(LeadResource::canCreate())->toBeFalse()
            ->and(LeadResource::canEdit($lead))->toBeFalse()
            ->and(OpportunityResource::canViewAny())->toBeTrue();

        CRMHelper::actingAsCRMUser(['lead']);

        expect(LeadResource::canViewAny())->toBeTrue()
            ->and(LeadResource::canCreate())->toBeTrue()
            ->and(LeadResource::canEdit($lead))->toBeTrue()
            ->and(OpportunityResource::canViewAny())->toBeFalse();
    });

    it('does not open My Pipeline with only the lead permissions', function () {
        CRMHelper::actingAsCRMUser(['lead']);

        Livewire::withQueryParams(['layout' => 'list'])->test(ListOpportunities::class)->assertForbidden();
    });
});

describe('resource resolver', function () {
    it('picks the resource that owns the record', function () {
        CRMHelper::actingAsCRMUser(['lead', 'opportunity']);

        $lead = CRMHelper::lead();
        $opportunity = CRMHelper::opportunity();

        expect(LeadResourceResolver::resourceFor($lead))->toBe(LeadResource::class)
            ->and(LeadResourceResolver::resourceFor($opportunity))->toBe(OpportunityResource::class);
    });

    it('builds the view url of the owning resource', function () {
        CRMHelper::actingAsCRMUser(['lead', 'opportunity']);

        $lead = CRMHelper::lead();
        $opportunity = CRMHelper::opportunity();

        expect(LeadResourceResolver::viewUrl($lead))->toBe(LeadResource::getUrl('view', ['record' => $lead]))
            ->and(LeadResourceResolver::viewUrl($opportunity))->toBe(OpportunityResource::getUrl('view', ['record' => $opportunity]))
            ->and(LeadResourceResolver::viewUrl(null))->toBeNull();
    });

    it('follows a lead once it is converted', function () {
        CRMHelper::actingAsCRMUser(['lead', 'opportunity']);

        $lead = CRMHelper::lead();
        $lead->convertToOpportunity();

        expect(LeadResourceResolver::resourceFor($lead->fresh()))->toBe(OpportunityResource::class);
    });
});

it('lists only the current company\'s teams in the leads team filter', function () {
    CRMHelper::actingAsCRMUser(['lead']);

    $ownTeam = Team::factory()->create(['name' => 'Own company team (filter test)', 'company_id' => CRMHelper::companyId()]);
    $foreignTeam = Team::factory()->create(['name' => 'Other company team (filter test)', 'company_id' => Company::factory()->create()->id]);

    $filter = Livewire::test(ListLeads::class)
        ->instance()
        ->getTable()
        ->getFilter('team_id');

    $modifyQuery = (fn () => $this->modifyRelationshipQueryUsing)->call($filter);

    $teamIds = $modifyQuery(Team::query())->pluck('id');

    expect($teamIds)->toContain($ownTeam->id)
        ->and($teamIds)->not->toContain($foreignTeam->id);
});

it('shows the number of records on each view tab of the opportunity list', function () {
    $user = CRMHelper::actingAsCRMUser(['opportunity']);

    $views = Livewire::withQueryParams(['layout' => 'list'])->test(ListOpportunities::class)->instance()->getCachedFavoriteTableViews();

    $expected = fn (string $key): int => $views[$key]->modifyQuery(OpportunityResource::getEloquentQuery())->count();

    expect($views['default']->getBadge())->toBe(OpportunityResource::getEloquentQuery()->count())
        ->and($views['my_pipeline']->getBadge())->toBe($expected('my_pipeline'))
        ->and($views['open']->getBadge())->toBe($expected('open'))
        ->and($views['lost']->getBadge())->toBe($expected('lost'));

    CRMHelper::opportunity(['user_id' => $user->id]);

    $views = Livewire::withQueryParams(['layout' => 'list'])->test(ListOpportunities::class)->instance()->getCachedFavoriteTableViews();

    expect($views['my_pipeline']->getBadge())->toBe(Lead::query()->withoutTrashed()->where('type', LeadType::OPPORTUNITY)->where('user_id', $user->id)->count());
});

it('counts a saved view with its own filters and keeps the current filters', function () {
    $user = CRMHelper::actingAsCRMUser(['lead']);

    CRMHelper::lead(['priority' => OpportunityPriority::HIGH]);
    CRMHelper::lead(['priority' => OpportunityPriority::LOW]);

    $view = TableView::query()->create([
        'name'            => 'High priority leads',
        'filterable_type' => ListLeads::class,
        'user_id'         => $user->id,
        'is_public'       => false,
        'filters'         => ['tableFilters' => ['queryBuilder' => ['rules' => ['r1' => ['type' => 'priority', 'data' => ['operator' => 'is', 'settings' => ['values' => [OpportunityPriority::HIGH->value]]]]]]]],
    ]);

    TableViewFavorite::query()->create(['view_type' => 'saved', 'view_key' => $view->id, 'filterable_type' => ListLeads::class, 'user_id' => $user->id, 'is_favorite' => true]);

    $component = Livewire::test(ListLeads::class);
    $tabs = $component->instance()->getCachedFavoriteTableViews();

    expect($tabs[$view->id]->getBadge())->toBe(Lead::query()->where('type', LeadType::LEAD)->where('priority', OpportunityPriority::HIGH)->count())
        ->and($component->get('tableFilters.queryBuilder.rules') ?? [])->toBeEmpty();
});

it('opens the save view form with add to favorites switched on', function () {
    CRMHelper::actingAsCRMUser(['lead']);

    Livewire::test(ListLeads::class)
        ->mountAction('createTableView')
        ->assertSchemaStateSet(['is_favorite' => true]);
});

it('limits leads and opportunities to their owners for users without global access', function () {
    $sources = collect((new Lead)->ownershipSources());

    expect(class_uses_recursive(Lead::class))->toContain(HasOwnershipScope::class)
        ->and($sources->where('kind', OwnerSource::KIND_COLUMN)->pluck('params.name')->all())->toBe(['creator_id', 'user_id'])
        ->and($sources->pluck('kind')->all())->toContain(OwnerSource::KIND_FOLLOWERS);
});

it('lets users without global access edit and delete only the records assigned to them', function (string $resource, string $permissions, LeadType $type) {
    $user = CRMHelper::actingAsCRMUser([$permissions]);

    $user->forceFill(['resource_permission' => PermissionType::INDIVIDUAL])->saveQuietly();

    $own = Lead::factory()->create(['type' => $type, 'user_id' => $user->id]);
    $other = Lead::factory()->create(['type' => $type, 'user_id' => User::factory()->create()->id]);

    expect($resource::canEdit($own))->toBeTrue()
        ->and($resource::canDelete($own))->toBeTrue()
        ->and($resource::canEdit($other))->toBeFalse()
        ->and($resource::canDelete($other))->toBeFalse();
})->with([
    'lead'        => [LeadResource::class, 'lead', LeadType::LEAD],
    'opportunity' => [OpportunityResource::class, 'opportunity', LeadType::OPPORTUNITY],
]);

it('filters the leads list by the sales team in the url', function () {
    CRMHelper::actingAsCRMUser(['lead']);

    $team = Team::factory()->create(['company_id' => CRMHelper::companyId()]);
    $inTeam = CRMHelper::lead(['team_id' => $team->id]);
    $outside = CRMHelper::lead();

    Livewire::withQueryParams(['team' => $team->id])
        ->test(ListLeads::class)
        ->assertCanSeeTableRecords([$inTeam])
        ->assertCanNotSeeTableRecords([$outside])
        ->assertSee(__('crm::filament/clusters/leads/resources/lead/pages/list-leads.team-filter.label', ['team' => $team->name]));
});

it('deletes a lead from its page as a soft delete and returns to the leads list', function () {
    CRMHelper::actingAsCRMUser(['lead']);

    $lead = CRMHelper::lead();

    Livewire::test(ViewLead::class, ['record' => $lead->getKey()])
        ->callAction('delete')
        ->assertRedirect(LeadResource::getUrl('index'));

    expect(Lead::withTrashed()->find($lead->id)?->trashed())->toBeTrue();
});
