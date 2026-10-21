<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;
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
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../Helpers/CrmTestHelper.php';

beforeEach(fn () => CrmTestHelper::bootstrap());

/**
 * An active user whose only company is the given one (the current company by default).
 */
function leadResourceUser(?int $companyId = null): User
{
    return User::withoutEvents(fn (): User => User::factory()->create([
        'default_company_id' => $companyId ?? CrmTestHelper::companyId(),
        'is_active'          => true,
    ]));
}

/**
 * A lead with no salesperson (the model assigns the signed-in user on create).
 */
function leadResourceUnassignedLead(): Lead
{
    $lead = CrmTestHelper::lead();

    Lead::query()->whereKey($lead->id)->update(['user_id' => null]);

    return $lead->fresh();
}

describe('list', function () {
    it('renders the list with the lead permission', function () {
        CrmTestHelper::actingAsCrmUser(['lead']);

        Livewire::test(ListLeads::class)->assertOk();
    });

    it('opens on the "My Leads" view with only the current user leads', function () {
        CrmTestHelper::actingAsCrmUser(['lead']);

        $mine = CrmTestHelper::lead();
        $theirs = CrmTestHelper::lead(['user_id' => leadResourceUser()->id]);
        $unassigned = leadResourceUnassignedLead();
        $opportunity = CrmTestHelper::opportunity();

        Livewire::test(ListLeads::class)
            ->assertSet('activeTableView', 'my_leads')
            ->assertCanSeeTableRecords([$mine])
            ->assertCanNotSeeTableRecords([$theirs, $unassigned, $opportunity]);
    });

    it('shows only leads without a salesperson on the "Unassigned" view', function () {
        CrmTestHelper::actingAsCrmUser(['lead']);

        $mine = CrmTestHelper::lead();
        $unassigned = leadResourceUnassignedLead();

        Livewire::test(ListLeads::class)
            ->call('loadView', 'unassigned')
            ->assertCanSeeTableRecords([$unassigned])
            ->assertCanNotSeeTableRecords([$mine]);
    });

    it('shows every active lead, but no opportunity or lost lead, on the "All" view', function () {
        CrmTestHelper::actingAsCrmUser(['lead']);

        $mine = CrmTestHelper::lead();
        $theirs = CrmTestHelper::lead(['user_id' => leadResourceUser()->id]);
        $unassigned = leadResourceUnassignedLead();
        $lost = CrmTestHelper::lead();
        $lost->markAsLost(null);
        $opportunity = CrmTestHelper::opportunity();

        Livewire::test(ListLeads::class)
            ->call('loadView', 'all')
            ->assertCanSeeTableRecords([$mine, $theirs, $unassigned])
            ->assertCanNotSeeTableRecords([$lost, $opportunity]);
    });

    it('shows only lost leads on the "Lost" view', function () {
        CrmTestHelper::actingAsCrmUser(['lead']);

        $active = CrmTestHelper::lead();
        $lost = CrmTestHelper::lead();
        $lost->markAsLost(null);
        $lostOpportunity = CrmTestHelper::opportunity();
        $lostOpportunity->markAsLost(null);

        Livewire::test(ListLeads::class)
            ->call('loadView', 'lost')
            ->assertCanSeeTableRecords([$lost])
            ->assertCanNotSeeTableRecords([$active, $lostOpportunity]);
    });

    it('offers the four preset views', function () {
        CrmTestHelper::actingAsCrmUser(['lead']);

        expect(array_keys(Livewire::test(ListLeads::class)->instance()->getPresetTableViews()))
            ->toBe(['my_leads', 'unassigned', 'all', 'lost']);
    });

    it('searches leads by contact name', function () {
        CrmTestHelper::actingAsCrmUser(['lead']);

        $match = CrmTestHelper::lead(['contact_name' => 'Quentin Searchable']);
        $other = CrmTestHelper::lead(['contact_name' => 'Somebody Else']);

        Livewire::test(ListLeads::class)
            ->searchTable('Quentin Searchable')
            ->assertCanSeeTableRecords([$match])
            ->assertCanNotSeeTableRecords([$other]);
    });
});

describe('create', function () {
    it('creates a lead with the form fields', function () {
        $user = CrmTestHelper::actingAsCrmUser(['lead']);

        $team = Team::factory()->create(['company_id' => CrmTestHelper::companyId()]);

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

        expect($lead->type)->toBe(Lead::TYPE_LEAD)
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
            ->and($lead->company_id)->toBe(CrmTestHelper::companyId())
            ->and($lead->assigned_at)->not->toBeNull();
    });

    it('defaults the salesperson to the current user and the company to the current company', function () {
        $user = CrmTestHelper::actingAsCrmUser(['lead']);

        Livewire::test(CreateLead::class)
            ->assertSchemaStateSet([
                'user_id'     => $user->id,
                'company_id'  => CrmTestHelper::companyId(),
                'probability' => 10,
            ]);
    });

    it('validates the lead form', function (array $data, array $errors) {
        CrmTestHelper::actingAsCrmUser(['lead']);

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
        CrmTestHelper::actingAsCrmUser(['lead']);

        $foreignTeam = Team::factory()->create(['company_id' => Company::factory()->create()->id]);

        Livewire::test(CreateLead::class)
            ->fillForm(['name' => 'Lead with foreign team', 'team_id' => $foreignTeam->id])
            ->call('create')
            ->assertHasFormErrors(['team_id']);

        expect(Lead::query()->where('name', 'Lead with foreign team')->exists())->toBeFalse();
    });

    it('rejects a salesperson who is not allowed in the lead company', function () {
        CrmTestHelper::actingAsCrmUser(['lead']);

        $foreignUser = leadResourceUser(Company::factory()->create()->id);

        Livewire::test(CreateLead::class)
            ->fillForm(['name' => 'Lead with foreign salesperson', 'user_id' => $foreignUser->id])
            ->call('create')
            ->assertHasFormErrors(['user_id']);

        expect(Lead::query()->where('name', 'Lead with foreign salesperson')->exists())->toBeFalse();
    });

    it('rejects an inactive salesperson', function () {
        CrmTestHelper::actingAsCrmUser(['lead']);

        $inactiveUser = leadResourceUser();
        $inactiveUser->forceFill(['is_active' => false])->saveQuietly();

        Livewire::test(CreateLead::class)
            ->fillForm(['name' => 'Lead with inactive salesperson', 'user_id' => $inactiveUser->id])
            ->call('create')
            ->assertHasFormErrors(['user_id']);
    });

    it('forbids creating a lead without the lead create permission', function () {
        CrmTestHelper::actingAs(['view_any_crm_lead', 'view_crm_lead', 'create_crm_opportunity']);

        Livewire::test(CreateLead::class)->assertForbidden();
    });
});

describe('view', function () {
    it('shows a lead', function () {
        CrmTestHelper::actingAsCrmUser(['lead']);

        $lead = CrmTestHelper::lead(['name' => 'Viewable lead name', 'contact_name' => 'Viewable Contact']);

        Livewire::test(ViewLead::class, ['record' => $lead->id])
            ->assertOk()
            ->assertSee('Viewable lead name')
            ->assertSee('Viewable Contact')
            ->assertActionVisible('convertToOpportunity')
            ->assertActionVisible('markAsLost');
    });

    it('does not open an opportunity on the lead screen', function () {
        CrmTestHelper::actingAsCrmUser(['lead']);

        $opportunity = CrmTestHelper::opportunity();

        expect(fn () => Livewire::test(ViewLead::class, ['record' => $opportunity->id]))
            ->toThrow(ModelNotFoundException::class);
    });

    it('forbids viewing a lead without the lead view permission', function () {
        CrmTestHelper::actingAs(['view_any_crm_lead', 'view_crm_opportunity']);

        $lead = CrmTestHelper::lead();

        Livewire::test(ViewLead::class, ['record' => $lead->id])->assertForbidden();
    });

    it('shows no left sub-navigation on record pages', function () {
        CrmTestHelper::actingAsCrmUser(['lead']);

        $lead = CrmTestHelper::lead();

        expect(Livewire::test(ViewLead::class, ['record' => $lead->id])->instance()->getSubNavigation())->toBe([]);
    });
});

describe('edit', function () {
    it('updates a lead', function () {
        CrmTestHelper::actingAsCrmUser(['lead']);

        $lead = CrmTestHelper::lead(['name' => 'Lead before edit']);

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
            ->and($lead->type)->toBe(Lead::TYPE_LEAD);
    });

    it('sets the assignment date when the salesperson changes', function () {
        CrmTestHelper::actingAsCrmUser(['lead']);

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
        CrmTestHelper::actingAs(['view_any_crm_lead', 'view_crm_lead', 'update_crm_opportunity']);

        $lead = CrmTestHelper::lead();

        Livewire::test(EditLead::class, ['record' => $lead->id])->assertForbidden();
    });
});

describe('lost', function () {
    it('marks a lead as lost with a reason', function () {
        CrmTestHelper::actingAsCrmUser(['lead']);

        $lead = CrmTestHelper::lead();
        $reason = LostReason::factory()->create();

        Livewire::test(ViewLead::class, ['record' => $lead->id])
            ->callAction('markAsLost', ['lost_reason_id' => $reason->id, 'lost_feedback' => 'Went with a competitor'])
            ->assertHasNoFormErrors();

        $lead = Lead::withTrashed()->findOrFail($lead->id);

        expect($lead->trashed())->toBeTrue()
            ->and($lead->lost_reason_id)->toBe($reason->id)
            ->and($lead->lost_feedback)->toBe('Went with a competitor')
            ->and((float) $lead->probability)->toBe(0.0)
            ->and($lead->type)->toBe(Lead::TYPE_LEAD);
    });

    it('hides "Lost" and "Convert" on a lost lead and restores it', function () {
        CrmTestHelper::actingAsCrmUser(['lead']);

        $lead = CrmTestHelper::lead();
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
        CrmTestHelper::actingAs(['view_any_crm_lead', 'view_crm_lead', 'delete_crm_opportunity']);

        $lead = CrmTestHelper::lead();

        Livewire::test(ViewLead::class, ['record' => $lead->id])->assertActionHidden('markAsLost');
    });
});

describe('authorization', function () {
    it('forbids every lead page with only the opportunity permissions', function () {
        CrmTestHelper::actingAsCrmUser(['opportunity']);

        $lead = CrmTestHelper::lead();

        Livewire::test(ListLeads::class)->assertForbidden();
        Livewire::test(CreateLead::class)->assertForbidden();
        Livewire::test(ViewLead::class, ['record' => $lead->id])->assertForbidden();
        Livewire::test(EditLead::class, ['record' => $lead->id])->assertForbidden();
    });

    it('authorizes the leads screen with the lead policy, not the opportunity policy', function () {
        CrmTestHelper::actingAsCrmUser(['opportunity']);

        $lead = CrmTestHelper::lead();

        expect(LeadResource::canViewAny())->toBeFalse()
            ->and(LeadResource::canCreate())->toBeFalse()
            ->and(LeadResource::canEdit($lead))->toBeFalse()
            ->and(OpportunityResource::canViewAny())->toBeTrue();

        CrmTestHelper::actingAsCrmUser(['lead']);

        expect(LeadResource::canViewAny())->toBeTrue()
            ->and(LeadResource::canCreate())->toBeTrue()
            ->and(LeadResource::canEdit($lead))->toBeTrue()
            ->and(OpportunityResource::canViewAny())->toBeFalse();
    });

    it('does not open My Pipeline with only the lead permissions', function () {
        CrmTestHelper::actingAsCrmUser(['lead']);

        Livewire::test(ListOpportunities::class)->assertForbidden();
    });
});

describe('resource resolver', function () {
    it('picks the resource that owns the record', function () {
        CrmTestHelper::actingAsCrmUser(['lead', 'opportunity']);

        $lead = CrmTestHelper::lead();
        $opportunity = CrmTestHelper::opportunity();

        expect(LeadResourceResolver::resourceFor($lead))->toBe(LeadResource::class)
            ->and(LeadResourceResolver::resourceFor($opportunity))->toBe(OpportunityResource::class);
    });

    it('builds the view url of the owning resource', function () {
        CrmTestHelper::actingAsCrmUser(['lead', 'opportunity']);

        $lead = CrmTestHelper::lead();
        $opportunity = CrmTestHelper::opportunity();

        expect(LeadResourceResolver::viewUrl($lead))->toBe(LeadResource::getUrl('view', ['record' => $lead]))
            ->and(LeadResourceResolver::viewUrl($opportunity))->toBe(OpportunityResource::getUrl('view', ['record' => $opportunity]))
            ->and(LeadResourceResolver::viewUrl(null))->toBeNull();
    });

    it('follows a lead once it is converted', function () {
        CrmTestHelper::actingAsCrmUser(['lead', 'opportunity']);

        $lead = CrmTestHelper::lead();
        $lead->convertToOpportunity();

        expect(LeadResourceResolver::resourceFor($lead->fresh()))->toBe(OpportunityResource::class);
    });
});

it('lists only the current company\'s teams in the leads team filter', function () {
    CrmTestHelper::actingAsCrmUser(['lead']);

    $ownTeam = Team::factory()->create(['name' => 'Own company team (filter test)', 'company_id' => CrmTestHelper::companyId()]);
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
