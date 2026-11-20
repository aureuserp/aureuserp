<?php

use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;
use Webkul\Crm\Enums\LeadConversionCustomer;
use Webkul\Crm\Enums\LeadType;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Actions\ConvertToOpportunityAction;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages\EditLead;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages\ListLeads;
use Webkul\Crm\Filament\Clusters\Leads\Resources\LeadResource\Pages\ViewLead;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Models\Lead;
use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Models\Partner;
use Webkul\Sale\Models\Team;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../Helpers/CRMHelper.php';

beforeEach(fn () => CRMHelper::bootstrap());

/**
 * An active user whose only company is the given one (the current company by default).
 */
function convertLeadUser(?int $companyId = null): User
{
    return User::withoutEvents(fn (): User => User::factory()->create([
        'default_company_id' => $companyId ?? CRMHelper::companyId(),
        'is_active'          => true,
    ]));
}

function convertLeadOtherCompany(): Company
{
    return Company::factory()->create();
}

it('converts a lead and creates a company customer from the company name', function () {
    $user = CRMHelper::actingAsCRMUser(['lead', 'opportunity']);

    $team = Team::factory()->create(['company_id' => CRMHelper::companyId()]);

    $lead = CRMHelper::lead([
        'company_name' => 'Initech Conversion Ltd',
        'contact_name' => 'Bill Lumbergh',
        'email'        => 'bill@initech.example.com',
        'phone'        => '555-0199',
        'website'      => 'https://initech.example.com',
        'street1'      => '4120 Freidrich Lane',
        'city'         => 'Austin',
    ]);

    Livewire::test(ViewLead::class, ['record' => $lead->id])
        ->callAction('convertToOpportunity', [
            'customer' => 'create',
            'user_id'  => $user->id,
            'team_id'  => $team->id,
        ])
        ->assertHasNoFormErrors()
        ->assertRedirect(OpportunityResource::getUrl('view', ['record' => $lead]));

    $lead->refresh();

    expect($lead->type)->toBe(LeadType::OPPORTUNITY)
        ->and($lead->converted_at)->not->toBeNull()
        ->and($lead->user_id)->toBe($user->id)
        ->and($lead->team_id)->toBe($team->id)
        ->and($lead->partner_id)->not->toBeNull();

    $partner = Partner::query()->findOrFail($lead->partner_id);

    expect($partner->name)->toBe('Initech Conversion Ltd')
        ->and($partner->account_type)->toBe(AccountType::COMPANY)
        ->and($partner->email)->toBe('bill@initech.example.com')
        ->and($partner->phone)->toBe('555-0199')
        ->and($partner->website)->toBe('https://initech.example.com')
        ->and($partner->street1)->toBe('4120 Freidrich Lane')
        ->and($partner->city)->toBe('Austin')
        ->and($partner->company_id)->toBe(CRMHelper::companyId());
});

it('creates an individual customer from the contact name when there is no company name', function () {
    CRMHelper::actingAsCRMUser(['lead', 'opportunity']);

    $lead = CRMHelper::lead([
        'company_name' => null,
        'contact_name' => 'Milton Waddams',
        'job_title'    => 'Collator',
    ]);

    Livewire::test(ViewLead::class, ['record' => $lead->id])
        ->callAction('convertToOpportunity', ['customer' => 'create'])
        ->assertHasNoFormErrors();

    $partner = Partner::query()->findOrFail($lead->fresh()->partner_id);

    expect($partner->name)->toBe('Milton Waddams')
        ->and($partner->account_type)->toBe(AccountType::INDIVIDUAL)
        ->and($partner->job_title)->toBe('Collator');
});

it('pre-selects the customer option from the lead', function (array $attributes, LeadConversionCustomer $expected) {
    CRMHelper::actingAsCRMUser(['lead', 'opportunity']);

    if (($attributes['partner_id'] ?? null) === 'partner') {
        $attributes['partner_id'] = Partner::factory()->create(['company_id' => CRMHelper::companyId()])->id;
    }

    $lead = CRMHelper::lead($attributes);

    Livewire::test(ViewLead::class, ['record' => $lead->id])
        ->mountAction('convertToOpportunity')
        ->assertSchemaStateSet(['customer' => $expected]);
})->with([
    'linked partner'        => [['partner_id' => 'partner'], LeadConversionCustomer::LINK],
    'company or contact'    => [['company_name' => 'Has Company'], LeadConversionCustomer::CREATE],
    'nothing to create'     => [['company_name' => null, 'contact_name' => null], LeadConversionCustomer::NONE],
]);

it('converts a lead and links an existing customer', function () {
    CRMHelper::actingAsCRMUser(['lead', 'opportunity']);

    $partner = Partner::factory()->create(['company_id' => CRMHelper::companyId(), 'name' => 'Existing Linked Customer']);
    $lead = CRMHelper::lead();
    $partnerCount = Partner::query()->count();

    Livewire::test(ViewLead::class, ['record' => $lead->id])
        ->callAction('convertToOpportunity', [
            'customer'   => 'link',
            'partner_id' => $partner->id,
        ])
        ->assertHasNoFormErrors();

    $lead->refresh();

    expect($lead->type)->toBe(LeadType::OPPORTUNITY)
        ->and($lead->partner_id)->toBe($partner->id)
        ->and(Partner::query()->count())->toBe($partnerCount);
});

it('requires a customer when linking an existing one', function () {
    CRMHelper::actingAsCRMUser(['lead', 'opportunity']);

    $lead = CRMHelper::lead();

    Livewire::test(ViewLead::class, ['record' => $lead->id])
        ->callAction('convertToOpportunity', ['customer' => 'link', 'partner_id' => null])
        ->assertHasFormErrors(['partner_id' => 'required']);

    expect($lead->fresh()->type)->toBe(LeadType::LEAD);
});

it('refuses to link a customer of another company', function () {
    CRMHelper::actingAsCRMUser(['lead', 'opportunity']);

    $foreignPartner = Partner::factory()->create(['company_id' => convertLeadOtherCompany()->id]);
    $lead = CRMHelper::lead();

    Livewire::test(ViewLead::class, ['record' => $lead->id])
        ->callAction('convertToOpportunity', [
            'customer'   => 'link',
            'partner_id' => $foreignPartner->id,
        ]);

    $lead->refresh();

    expect($lead->type)->toBe(LeadType::LEAD)
        ->and($lead->partner_id)->toBeNull()
        ->and($lead->converted_at)->toBeNull();
});

it('converts a lead without a customer', function () {
    CRMHelper::actingAsCRMUser(['lead', 'opportunity']);

    $lead = CRMHelper::lead(['company_name' => 'No Customer Created Co']);
    $partnerCount = Partner::query()->count();

    Livewire::test(ViewLead::class, ['record' => $lead->id])
        ->callAction('convertToOpportunity', ['customer' => 'none'])
        ->assertHasNoFormErrors()
        ->assertRedirect(OpportunityResource::getUrl('view', ['record' => $lead]));

    $lead->refresh();

    expect($lead->type)->toBe(LeadType::OPPORTUNITY)
        ->and($lead->partner_id)->toBeNull()
        ->and($lead->converted_at)->not->toBeNull()
        ->and(Partner::query()->count())->toBe($partnerCount);
});

it('clears the salesperson and team when they are emptied in the convert form', function () {
    CRMHelper::actingAsCRMUser(['lead', 'opportunity']);

    $team = Team::factory()->create(['company_id' => CRMHelper::companyId()]);
    $lead = CRMHelper::lead(['team_id' => $team->id]);

    Livewire::test(ViewLead::class, ['record' => $lead->id])
        ->callAction('convertToOpportunity', ['customer' => 'none', 'user_id' => null, 'team_id' => null])
        ->assertHasNoFormErrors();

    $lead->refresh();

    expect($lead->type)->toBe(LeadType::OPPORTUNITY)
        ->and($lead->user_id)->toBeNull()
        ->and($lead->team_id)->toBeNull()
        ->and($lead->assigned_at)->toBeNull();
});

it('keeps the lead salesperson and team when the convert form keeps them', function () {
    $user = CRMHelper::actingAsCRMUser(['lead', 'opportunity']);

    $team = Team::factory()->create(['company_id' => CRMHelper::companyId()]);
    $lead = CRMHelper::lead(['team_id' => $team->id]);

    Livewire::test(ViewLead::class, ['record' => $lead->id])
        ->callAction('convertToOpportunity', ['customer' => 'none'])
        ->assertHasNoFormErrors();

    $lead->refresh();

    expect($lead->type)->toBe(LeadType::OPPORTUNITY)
        ->and($lead->user_id)->toBe($user->id)
        ->and($lead->team_id)->toBe($team->id);
});

it('keeps the salesperson and team when convertToOpportunity is called without them', function () {
    $user = CRMHelper::actingAsCRMUser(['lead', 'opportunity']);

    $team = Team::factory()->create(['company_id' => CRMHelper::companyId()]);
    $lead = CRMHelper::lead(['team_id' => $team->id]);

    $lead->convertToOpportunity(['customer' => LeadConversionCustomer::NONE]);

    expect($lead->fresh()->user_id)->toBe($user->id)
        ->and($lead->fresh()->team_id)->toBe($team->id);
});

it('refuses a salesperson who is not allowed in the lead company', function () {
    CRMHelper::actingAsCRMUser(['lead', 'opportunity']);

    $foreignUser = convertLeadUser(convertLeadOtherCompany()->id);
    $lead = CRMHelper::lead();

    Livewire::test(ViewLead::class, ['record' => $lead->id])
        ->callAction('convertToOpportunity', ['customer' => 'none', 'user_id' => $foreignUser->id]);

    $lead->refresh();

    expect($lead->type)->toBe(LeadType::LEAD)
        ->and($lead->user_id)->not->toBe($foreignUser->id);
});

it('refuses a sales team of another company', function () {
    CRMHelper::actingAsCRMUser(['lead', 'opportunity']);

    $foreignTeam = Team::factory()->create(['company_id' => convertLeadOtherCompany()->id]);
    $lead = CRMHelper::lead(['team_id' => null]);

    Livewire::test(ViewLead::class, ['record' => $lead->id])
        ->callAction('convertToOpportunity', ['customer' => 'none', 'team_id' => $foreignTeam->id]);

    $lead->refresh();

    expect($lead->type)->toBe(LeadType::LEAD)
        ->and($lead->team_id)->toBeNull();
});

it('accepts a salesperson of the lead company', function () {
    CRMHelper::actingAsCRMUser(['lead', 'opportunity']);

    $salesperson = convertLeadUser();
    $lead = CRMHelper::lead();

    Livewire::test(ViewLead::class, ['record' => $lead->id])
        ->callAction('convertToOpportunity', ['customer' => 'none', 'user_id' => $salesperson->id])
        ->assertHasNoFormErrors();

    $lead->refresh();

    expect($lead->type)->toBe(LeadType::OPPORTUNITY)
        ->and($lead->user_id)->toBe($salesperson->id);
});

it('converts a lead from the edit page', function () {
    CRMHelper::actingAsCRMUser(['lead', 'opportunity']);

    $lead = CRMHelper::lead();

    Livewire::test(EditLead::class, ['record' => $lead->id])
        ->callAction('convertToOpportunity', ['customer' => 'none'])
        ->assertHasNoFormErrors()
        ->assertRedirect(OpportunityResource::getUrl('view', ['record' => $lead]));

    expect($lead->fresh()->type)->toBe(LeadType::OPPORTUNITY);
});

it('converts a lead from the list row menu', function () {
    CRMHelper::actingAsCRMUser(['lead', 'opportunity']);

    $lead = CRMHelper::lead();

    Livewire::test(ListLeads::class)
        ->callAction(TestAction::make('convertToOpportunity')->table($lead), ['customer' => 'none'])
        ->assertHasNoFormErrors();

    expect($lead->fresh()->type)->toBe(LeadType::OPPORTUNITY);
});

it('hides the action without the lead update permission', function () {
    CRMHelper::actingAs(['view_any_crm_lead', 'view_crm_lead', 'update_crm_opportunity']);

    $lead = CRMHelper::lead();

    Livewire::test(ViewLead::class, ['record' => $lead->id])
        ->assertActionHidden('convertToOpportunity');

    expect($lead->fresh()->type)->toBe(LeadType::LEAD);
});

it('does not convert a lead without the lead update permission, even when the action is called directly', function () {
    CRMHelper::actingAs(['view_any_crm_lead', 'view_crm_lead']);

    $lead = CRMHelper::lead();

    Livewire::test(ViewLead::class, ['record' => $lead->id])
        ->call('mountAction', 'convertToOpportunity')
        ->call('callMountedAction');

    expect($lead->fresh()->type)->toBe(LeadType::LEAD)
        ->and($lead->fresh()->converted_at)->toBeNull();
});

it('cannot convert a lost lead', function () {
    CRMHelper::actingAsCRMUser(['lead', 'opportunity']);

    $lead = CRMHelper::lead();
    $lead->markAsLost(null);

    Livewire::test(ViewLead::class, ['record' => $lead->id])
        ->assertActionHidden('convertToOpportunity')
        ->call('mountAction', 'convertToOpportunity')
        ->call('callMountedAction');

    $lead = Lead::withTrashed()->findOrFail($lead->id);

    expect($lead->type)->toBe(LeadType::LEAD)
        ->and($lead->converted_at)->toBeNull();
});

it('is not offered for an existing opportunity', function () {
    CRMHelper::actingAsCRMUser(['lead', 'opportunity']);

    $lead = CRMHelper::lead();
    $opportunity = CRMHelper::opportunity();

    expect(ConvertToOpportunityAction::make()->record($lead)->isVisible())->toBeTrue()
        ->and(ConvertToOpportunityAction::make()->record($opportunity)->isVisible())->toBeFalse();
});
