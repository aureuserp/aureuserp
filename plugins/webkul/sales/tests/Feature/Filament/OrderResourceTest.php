<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Account\Models\PaymentTerm;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Sale\Enums\AdvancedPayment;
use Webkul\Sale\Enums\InvoiceStatus;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderResource\Pages\CreateOrder;
use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderResource\Pages\ListOrders;
use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderResource\Pages\ViewOrder;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Actions\ConfirmAction;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Actions\CreateInvoiceAction;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages\EditQuotation;
use Webkul\Sale\Models\Order;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
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

function saleOrderRecord(?int $userId = null): Order
{
    $product = SaleHelper::product();

    $order = SaleHelper::order([
        'state'          => OrderState::SALE,
        'invoice_status' => InvoiceStatus::TO_INVOICE,
        'user_id'        => $userId,
    ]);

    SaleHelper::line($order, $product, qty: 2, priceUnit: 100);

    return $order->refresh();
}

it('forbids listing sale orders without permission', function () {
    FilamentHelper::actingAs([]);

    Livewire::test(ListOrders::class)->assertForbidden();
});

it('lists sale orders with their key columns for authorized users', function () {
    $user = FilamentHelper::actingAs(['view_any_sale_order']);

    $order = saleOrderRecord($user->id);

    Livewire::test(ListOrders::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$order])
        ->assertCanRenderTableColumn('name')
        ->assertCanRenderTableColumn('partner.name')
        ->assertCanRenderTableColumn('state')
        ->assertCanRenderTableColumn('amount_total');
});

it('finds a sale order by searching its number', function () {
    $user = FilamentHelper::actingAs(['view_any_sale_order']);

    $order = saleOrderRecord($user->id);

    Livewire::test(ListOrders::class)
        ->searchTable($order->name)
        ->assertCanSeeTableRecords([$order]);
});

it('renders the sale order create page', function () {
    FilamentHelper::actingAs(['view_any_sale_order', 'create_sale_order']);

    Livewire::test(CreateOrder::class)->assertOk();
});

it('creates a quotation through the create form', function () {
    FilamentHelper::actingAs(['view_any_sale_order', 'create_sale_order']);

    $partner = SaleHelper::partner();

    Livewire::test(CreateOrder::class)
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

it('confirms a draft quotation into a sale order through the action', function () {
    $user = FilamentHelper::actingAs(['view_any_sale_quotation', 'update_sale_quotation']);

    $product = SaleHelper::product();
    $order = SaleHelper::order(['user_id' => $user->id]);
    SaleHelper::line($order, $product, qty: 2, priceUnit: 100);

    Livewire::test(EditQuotation::class, ['record' => $order->id])
        ->assertOk()
        ->callAction(ConfirmAction::class);

    expect($order->refresh()->state)->toBe(OrderState::SALE);
});

it('creates an invoice from a confirmed sale order through the action', function () {
    $user = FilamentHelper::actingAs(['view_any_sale_order', 'view_sale_order']);

    $order = saleOrderRecord($user->id);

    Livewire::test(ViewOrder::class, ['record' => $order->id])
        ->assertOk()
        ->assertActionExists(CreateInvoiceAction::class)
        ->callAction(CreateInvoiceAction::class, data: [
            'advance_payment_method' => AdvancedPayment::DELIVERED->value,
        ]);

    expect($order->refresh()->accountMoves()->count())->toBeGreaterThan(0);
});
