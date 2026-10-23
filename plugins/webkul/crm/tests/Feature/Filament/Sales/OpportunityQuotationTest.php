<?php

use Livewire\Livewire;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Actions\NewQuotationAction;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\ManageQuotations;
use Webkul\Crm\Filament\Clusters\Sales\Resources\OpportunityResource\Pages\ViewOpportunity;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource;
use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Pages\CreateQuotation;
use Webkul\Crm\Models\QuotationPartner;
use Webkul\Partner\Models\Partner;
use Webkul\Sale\Enums\OrderState;

require_once __DIR__.'/../../../Helpers/CrmTestHelper.php';
require_once __DIR__.'/../../../../../sales/tests/Helpers/SaleHelper.php';

beforeEach(fn () => CrmTestHelper::bootstrap());

function quotationOpportunityPermissions(): array
{
    return [
        ...CrmTestHelper::permissionsFor(['opportunity']),
        ...CrmTestHelper::permissionsFor(['quotation']),
    ];
}

it('shows "New Quotation" on an open or won opportunity but not on a lost one', function () {
    CrmTestHelper::actingAs(quotationOpportunityPermissions());

    $won = CrmTestHelper::opportunity(['stage_id' => CrmTestHelper::wonStage()->id]);
    $lost = CrmTestHelper::opportunity();
    $lost->markAsLost(null);

    Livewire::test(ViewOpportunity::class, ['record' => $won->getRouteKey()])
        ->assertActionVisible('newQuotation');

    Livewire::test(ViewOpportunity::class, ['record' => $lost->getRouteKey()])
        ->assertActionHidden('newQuotation');
});

it('goes straight to the quotation form when the opportunity has a customer', function () {
    CrmTestHelper::actingAs(quotationOpportunityPermissions());

    $partner = Partner::factory()->create(['company_id' => CrmTestHelper::companyId()]);
    $opportunity = CrmTestHelper::opportunity(['partner_id' => $partner->id]);

    Livewire::test(ViewOpportunity::class, ['record' => $opportunity->getRouteKey()])
        ->callAction('newQuotation')
        ->assertRedirect(QuotationResource::getUrl('create', ['opportunity' => $opportunity->id]));
});

it('creates the customer first when asked to', function () {
    CrmTestHelper::actingAs(quotationOpportunityPermissions());

    $opportunity = CrmTestHelper::opportunity(['partner_id' => null, 'contact_name' => 'New Buyer', 'company_name' => null, 'email' => 'new.buyer@customer.test']);

    Livewire::test(ViewOpportunity::class, ['record' => $opportunity->getRouteKey()])
        ->callAction('newQuotation', data: ['customer' => 'create'])
        ->assertHasNoActionErrors();

    expect($opportunity->fresh()->partner?->email)->toBe('new.buyer@customer.test');
});

it('links an existing customer or none', function () {
    CrmTestHelper::actingAs(quotationOpportunityPermissions());

    $partner = Partner::factory()->create(['company_id' => CrmTestHelper::companyId()]);
    $linked = CrmTestHelper::opportunity(['partner_id' => null]);
    $unlinked = CrmTestHelper::opportunity(['partner_id' => null]);

    Livewire::test(ViewOpportunity::class, ['record' => $linked->getRouteKey()])
        ->callAction('newQuotation', data: ['customer' => 'link', 'partner_id' => $partner->id])
        ->assertHasNoActionErrors();

    Livewire::test(ViewOpportunity::class, ['record' => $unlinked->getRouteKey()])
        ->callAction('newQuotation', data: ['customer' => 'none'])
        ->assertRedirect(QuotationResource::getUrl('create', ['opportunity' => $unlinked->id]));

    expect($linked->fresh()->partner_id)->toBe($partner->id)
        ->and($unlinked->fresh()->partner_id)->toBeNull();
});

it('only offers "Do not link" to users who cannot edit the opportunity', function () {
    CrmTestHelper::actingAs([
        'view_any_crm_opportunity', 'view_crm_opportunity',
        ...CrmTestHelper::permissionsFor(['quotation']),
    ]);

    $opportunity = CrmTestHelper::opportunity(['partner_id' => null]);

    expect(NewQuotationAction::customerChoices($opportunity))->toBe(['none']);

    Livewire::test(ViewOpportunity::class, ['record' => $opportunity->getRouteKey()])
        ->callAction('newQuotation', data: ['customer' => 'create']);

    expect($opportunity->fresh()->partner_id)->toBeNull();
});

it('fills the quotation from the opportunity', function () {
    $user = CrmTestHelper::actingAs(quotationOpportunityPermissions());

    $partner = Partner::factory()->create(['company_id' => CrmTestHelper::companyId()]);
    $opportunity = CrmTestHelper::opportunity(['partner_id' => $partner->id, 'user_id' => $user->id, 'name' => 'Office chairs']);

    Livewire::withQueryParams(['opportunity' => $opportunity->id])
        ->test(CreateQuotation::class)
        ->assertSet('data.partner_id', $partner->id)
        ->assertSet('data.user_id', $user->id)
        ->assertSet('data.origin', 'Office chairs');
});

it('ignores an opportunity that does not exist or cannot be seen', function () {
    CrmTestHelper::actingAs(quotationOpportunityPermissions());

    $page = Livewire::withQueryParams(['opportunity' => 999999])->test(CreateQuotation::class);

    expect($page->instance()->getOpportunity())->toBeNull();
});

it('lists the quotations of the opportunity in its Quotations tab', function () {
    CrmTestHelper::actingAs(quotationOpportunityPermissions());

    $opportunity = CrmTestHelper::opportunity();

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
    CrmTestHelper::actingAs(quotationOpportunityPermissions());

    $partner = Partner::factory()->create(['company_id' => CrmTestHelper::companyId()]);
    $withCustomer = CrmTestHelper::opportunity(['partner_id' => $partner->id]);
    $withoutCustomer = CrmTestHelper::opportunity(['partner_id' => null, 'contact_name' => 'Fresh Buyer', 'email' => 'fresh.buyer@customer.test']);

    Livewire::test(ViewOpportunity::class, ['record' => $withCustomer->getRouteKey()])->callAction('newQuotation');
    Livewire::test(ViewOpportunity::class, ['record' => $withoutCustomer->getRouteKey()])->callAction('newQuotation', data: ['customer' => 'create']);

    expect(QuotationPartner::query()->where('opportunity_id', $withCustomer->id)->value('action'))->toBe(QuotationPartner::ACTION_EXISTING)
        ->and(QuotationPartner::query()->where('opportunity_id', $withoutCustomer->id)->first())
        ->action->toBe(QuotationPartner::ACTION_CREATE)
        ->partner_id->toBe($withoutCustomer->fresh()->partner_id);

    $quotation = SaleHelper::order(['state' => OrderState::DRAFT]);

    $page = Livewire::withQueryParams(['opportunity' => $withCustomer->id])->test(CreateQuotation::class)->instance();
    $page->record = $quotation;
    (fn () => $this->afterCreate())->call($page);

    expect(QuotationPartner::query()->where('opportunity_id', $withCustomer->id)->value('quotation_id'))->toBe($quotation->id);
});
