<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Account\Models\PaymentTerm;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Actions\BackToQuotationAction;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Actions\CancelQuotationAction;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Actions\ConfirmAction;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Actions\LockAndUnlockAction;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages\CreateQuotation;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages\EditQuotation;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages\ListQuotations;
use Webkul\Sale\Models\Order;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../Helpers/SaleHelper.php';

beforeEach(function () {
    foreach (['inventories', 'sales', 'accounts'] as $plugin) {
        TestBootstrapHelper::ensurePluginInstalled($plugin);

        DB::table('plugins')->updateOrInsert(
            ['name' => $plugin],
            ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
        );
    }

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');
});

function quotationRecord(?int $userId = null, OrderState $state = OrderState::DRAFT): Order
{
    $order = SaleHelper::order([
        'state'   => $state,
        'user_id' => $userId,
    ]);

    SaleHelper::line($order, SaleHelper::product(), qty: 2, priceUnit: 100);

    return $order->refresh();
}

it('forbids listing quotations without permission', function () {
    FilamentHelper::actingAs([]);

    Livewire::test(ListQuotations::class)->assertForbidden();
});

it('lists quotations with their key columns for authorized users', function () {
    $user = FilamentHelper::actingAs(['view_any_sale_quotation']);

    $quotation = quotationRecord($user->id);

    Livewire::test(ListQuotations::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$quotation])
        ->assertCanRenderTableColumn('name')
        ->assertCanRenderTableColumn('partner.name')
        ->assertCanRenderTableColumn('state');
});

it('finds a quotation by searching its number', function () {
    $user = FilamentHelper::actingAs(['view_any_sale_quotation']);

    $quotation = quotationRecord($user->id);

    Livewire::test(ListQuotations::class)
        ->searchTable($quotation->name)
        ->assertCanSeeTableRecords([$quotation]);
});

it('renders the quotation create page', function () {
    FilamentHelper::actingAs(['view_any_sale_quotation', 'create_sale_quotation']);

    Livewire::test(CreateQuotation::class)->assertOk();
});

it('creates a quotation through the create form', function () {
    FilamentHelper::actingAs(['view_any_sale_quotation', 'create_sale_quotation']);

    $partner = SaleHelper::partner();

    Livewire::test(CreateQuotation::class)
        ->fillForm([
            'partner_id'      => $partner->id,
            'date_order'      => now(),
            'validity_date'   => now()->addDays(30),
            'payment_term_id' => PaymentTerm::query()->value('id'),
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Order::query()->where('partner_id', $partner->id)->exists())->toBeTrue();
});

it('requires a customer to create a quotation', function () {
    FilamentHelper::actingAs(['view_any_sale_quotation', 'create_sale_quotation']);

    Livewire::test(CreateQuotation::class)
        ->fillForm([
            'partner_id' => null,
            'date_order' => now(),
        ])
        ->call('create')
        ->assertHasFormErrors(['partner_id']);
});

it('renders the quotation edit page', function () {
    $user = FilamentHelper::actingAs(['view_any_sale_quotation', 'update_sale_quotation']);

    $quotation = quotationRecord($user->id);

    Livewire::test(EditQuotation::class, ['record' => $quotation->id])->assertOk();
});

it('offers the confirm action on a draft quotation', function () {
    $user = FilamentHelper::actingAs(['view_any_sale_quotation', 'update_sale_quotation']);

    $quotation = quotationRecord($user->id);

    Livewire::test(EditQuotation::class, ['record' => $quotation->id])
        ->assertOk()
        ->assertActionVisible(ConfirmAction::class)
        ->assertActionHidden(LockAndUnlockAction::class);
});

it('confirms a draft quotation into a sale order through the action', function () {
    $user = FilamentHelper::actingAs(['view_any_sale_quotation', 'update_sale_quotation']);

    $quotation = quotationRecord($user->id);

    Livewire::test(EditQuotation::class, ['record' => $quotation->id])
        ->assertOk()
        ->callAction(ConfirmAction::class);

    expect($quotation->refresh()->state)->toBe(OrderState::SALE);
});

it('hides the confirm action and offers lock once the quotation is confirmed', function () {
    $user = FilamentHelper::actingAs(['view_any_sale_quotation', 'update_sale_quotation']);

    $quotation = quotationRecord($user->id, OrderState::SALE);

    Livewire::test(EditQuotation::class, ['record' => $quotation->id])
        ->assertOk()
        ->assertActionHidden(ConfirmAction::class)
        ->assertActionVisible(LockAndUnlockAction::class);
});

it('offers the cancel action on a draft quotation', function () {
    $user = FilamentHelper::actingAs(['view_any_sale_quotation', 'update_sale_quotation']);

    $quotation = quotationRecord($user->id);

    Livewire::test(EditQuotation::class, ['record' => $quotation->id])
        ->assertOk()
        ->assertActionVisible(CancelQuotationAction::class)
        ->assertActionHidden(BackToQuotationAction::class);
});

it('offers the back to quotation action only on a cancelled quotation', function () {
    $user = FilamentHelper::actingAs(['view_any_sale_quotation', 'update_sale_quotation']);

    $quotation = quotationRecord($user->id, OrderState::CANCEL);

    Livewire::test(EditQuotation::class, ['record' => $quotation->id])
        ->assertOk()
        ->assertActionVisible(BackToQuotationAction::class)
        ->assertActionHidden(CancelQuotationAction::class)
        ->assertActionHidden(ConfirmAction::class);
});

it('returns a cancelled quotation to draft through the back to quotation action', function () {
    $user = FilamentHelper::actingAs(['view_any_sale_quotation', 'update_sale_quotation']);

    $quotation = quotationRecord($user->id, OrderState::CANCEL);

    Livewire::test(EditQuotation::class, ['record' => $quotation->id])
        ->assertOk()
        ->callAction(BackToQuotationAction::class);

    expect($quotation->refresh()->state)->toBe(OrderState::DRAFT);
});

it('lists a quotation to a user of the owning company', function () {
    FilamentHelper::actingAsCompanyUser(SaleHelper::company(), ['view_any_sale_quotation']);

    $quotation = quotationRecord();

    Livewire::test(ListQuotations::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$quotation]);
});

it('hides a quotation owned by another company from the list', function () {
    FilamentHelper::actingAsCompanyUser(SaleHelper::company(), ['view_any_sale_quotation']);

    $quotation = quotationRecord();

    FilamentHelper::actingAsCompanyUser(CompanyHelper::company(), ['view_any_sale_quotation']);

    Livewire::test(ListQuotations::class)
        ->assertOk()
        ->assertCanNotSeeTableRecords([$quotation]);
});

it('refuses to open a quotation owned by another company', function () {
    FilamentHelper::actingAsCompanyUser(SaleHelper::company(), ['view_any_sale_quotation']);

    $quotation = quotationRecord();

    FilamentHelper::actingAsCompanyUser(CompanyHelper::company(), [
        'view_any_sale_quotation',
        'update_sale_quotation',
    ]);

    expect(fn () => Livewire::test(EditQuotation::class, ['record' => $quotation->id]))
        ->toThrow(ModelNotFoundException::class);
});
