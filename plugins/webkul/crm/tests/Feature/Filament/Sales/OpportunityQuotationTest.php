<?php

use Livewire\Livewire;
use Webkul\Crm\Enums\LeadConversionCustomer;
use Webkul\Crm\Enums\QuotationPartnerAction;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Actions\NewQuotationAction;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\EditOpportunity;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\ManageQuotations;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\ViewOpportunity;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Pages\CreateQuotation;
use Webkul\Crm\Models\QuotationPartner;
use Webkul\Partner\Models\Partner;
use Webkul\Sale\Enums\OrderState;

require_once __DIR__.'/../../../Helpers/CRMHelper.php';
require_once __DIR__.'/../../../../../sales/tests/Helpers/SaleHelper.php';

beforeEach(fn () => CRMHelper::bootstrap());

function quotationOpportunityPermissions(): array
{
    return [
        ...CRMHelper::permissionsFor(['opportunity']),
        ...CRMHelper::permissionsFor(['quotation']),
    ];
}

it('offers "New Quotation" on the quotations tab, not in the opportunity page header', function () {
    CRMHelper::actingAs(quotationOpportunityPermissions());

    $opportunity = CRMHelper::opportunity();

    Livewire::test(ViewOpportunity::class, ['record' => $opportunity->getRouteKey()])->assertActionDoesNotExist('newQuotation');
    Livewire::test(EditOpportunity::class, ['record' => $opportunity->getRouteKey()])->assertActionDoesNotExist('newQuotation');
    Livewire::test(ManageQuotations::class, ['record' => $opportunity->getRouteKey()])->assertActionVisible('newQuotation');
});

it('shows "New Quotation" on an open or won opportunity but not on a lost one', function () {
    CRMHelper::actingAs(quotationOpportunityPermissions());

    $won = CRMHelper::opportunity(['stage_id' => CRMHelper::wonStage()->id]);
    $lost = CRMHelper::opportunity();
    $lost->markAsLost(null);

    Livewire::test(ManageQuotations::class, ['record' => $won->getRouteKey()])
        ->assertActionVisible('newQuotation');

    Livewire::test(ManageQuotations::class, ['record' => $lost->getRouteKey()])
        ->assertActionHidden('newQuotation');
});

it('goes straight to the quotation form when the opportunity has a customer', function () {
    CRMHelper::actingAs(quotationOpportunityPermissions());

    $partner = Partner::factory()->create(['company_id' => CRMHelper::companyId()]);
    $opportunity = CRMHelper::opportunity(['partner_id' => $partner->id]);

    Livewire::test(ManageQuotations::class, ['record' => $opportunity->getRouteKey()])
        ->callAction('newQuotation')
        ->assertRedirect(QuotationResource::getUrl('create', ['opportunity' => $opportunity->id]));
});

it('creates the customer first when asked to', function () {
    CRMHelper::actingAs(quotationOpportunityPermissions());

    $opportunity = CRMHelper::opportunity(['partner_id' => null, 'contact_name' => 'New Buyer', 'company_name' => null, 'email' => 'new.buyer@customer.test']);

    Livewire::test(ManageQuotations::class, ['record' => $opportunity->getRouteKey()])
        ->callAction('newQuotation', data: ['customer' => 'create'])
        ->assertHasNoActionErrors();

    expect($opportunity->fresh()->partner?->email)->toBe('new.buyer@customer.test');
});

it('links an existing customer or none', function () {
    CRMHelper::actingAs(quotationOpportunityPermissions());

    $partner = Partner::factory()->create(['company_id' => CRMHelper::companyId()]);
    $linked = CRMHelper::opportunity(['partner_id' => null]);
    $unlinked = CRMHelper::opportunity(['partner_id' => null]);

    Livewire::test(ManageQuotations::class, ['record' => $linked->getRouteKey()])
        ->callAction('newQuotation', data: ['customer' => 'link', 'partner_id' => $partner->id])
        ->assertHasNoActionErrors();

    Livewire::test(ManageQuotations::class, ['record' => $unlinked->getRouteKey()])
        ->callAction('newQuotation', data: ['customer' => 'none'])
        ->assertRedirect(QuotationResource::getUrl('create', ['opportunity' => $unlinked->id]));

    expect($linked->fresh()->partner_id)->toBe($partner->id)
        ->and($unlinked->fresh()->partner_id)->toBeNull();
});

it('only offers "Do not link" to users who cannot edit the opportunity', function () {
    CRMHelper::actingAs([
        'view_any_crm_opportunity', 'view_crm_opportunity',
        ...CRMHelper::permissionsFor(['quotation']),
    ]);

    $opportunity = CRMHelper::opportunity(['partner_id' => null]);

    expect(NewQuotationAction::customerChoices($opportunity))->toBe([LeadConversionCustomer::NONE]);

    Livewire::test(ManageQuotations::class, ['record' => $opportunity->getRouteKey()])
        ->callAction('newQuotation', data: ['customer' => 'create']);

    expect($opportunity->fresh()->partner_id)->toBeNull();
});

it('fills the quotation from the opportunity', function () {
    $user = CRMHelper::actingAs(quotationOpportunityPermissions());

    $partner = Partner::factory()->create(['company_id' => CRMHelper::companyId()]);
    $opportunity = CRMHelper::opportunity(['partner_id' => $partner->id, 'user_id' => $user->id, 'name' => 'Office chairs']);

    Livewire::withQueryParams(['opportunity' => $opportunity->id])
        ->test(CreateQuotation::class)
        ->assertSet('data.partner_id', $partner->id)
        ->assertSet('data.user_id', $user->id)
        ->assertSet('data.origin', 'Office chairs');
});

it('ignores an opportunity that does not exist or cannot be seen', function () {
    CRMHelper::actingAs(quotationOpportunityPermissions());

    $page = Livewire::withQueryParams(['opportunity' => 999999])->test(CreateQuotation::class);

    expect($page->instance()->getOpportunity())->toBeNull();
});

it('lists the quotations of the opportunity in its Quotations tab', function () {
    CRMHelper::actingAs(quotationOpportunityPermissions());

    $opportunity = CRMHelper::opportunity();

    $quotation = SaleHelper::order(['state' => OrderState::DRAFT]);
    $quotation->forceFill(['opportunity_id' => $opportunity->id])->saveQuietly();

    $other = SaleHelper::order(['state' => OrderState::DRAFT]);

    expect($opportunity->quotations()->pluck('id')->all())->toBe([$quotation->id]);

    Livewire::test(ManageQuotations::class, ['record' => $opportunity->getRouteKey()])
        ->assertOk()
        ->assertCanSeeTableRecords([$quotation])
        ->assertCanNotSeeTableRecords([$other]);

    expect(OpportunityResource::getUrl('quotations', ['record' => $opportunity]))->toContain('/quotations');
});

it('records the customer choice of each New Quotation and the quotation then saved', function () {
    CRMHelper::actingAs(quotationOpportunityPermissions());

    $partner = Partner::factory()->create(['company_id' => CRMHelper::companyId()]);
    $withCustomer = CRMHelper::opportunity(['partner_id' => $partner->id]);
    $withoutCustomer = CRMHelper::opportunity(['partner_id' => null, 'contact_name' => 'Fresh Buyer', 'email' => 'fresh.buyer@customer.test']);

    Livewire::test(ManageQuotations::class, ['record' => $withCustomer->getRouteKey()])->callAction('newQuotation');
    Livewire::test(ManageQuotations::class, ['record' => $withoutCustomer->getRouteKey()])->callAction('newQuotation', data: ['customer' => 'create']);

    expect(QuotationPartner::query()->where('opportunity_id', $withCustomer->id)->value('action'))->toBe(QuotationPartnerAction::EXISTING)
        ->and(QuotationPartner::query()->where('opportunity_id', $withoutCustomer->id)->first())
        ->action->toBe(QuotationPartnerAction::CREATE)
        ->partner_id->toBe($withoutCustomer->fresh()->partner_id);

    $quotation = SaleHelper::order(['state' => OrderState::DRAFT]);

    $page = Livewire::withQueryParams(['opportunity' => $withCustomer->id])->test(CreateQuotation::class)->instance();
    $page->record = $quotation;
    (fn () => $this->afterCreate())->call($page);

    expect(QuotationPartner::query()->where('opportunity_id', $withCustomer->id)->value('quotation_id'))->toBe($quotation->id);
});
