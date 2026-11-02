<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Enums\PaymentStatus;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions\CancelAction;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions\ConfirmAction;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions\PayAction;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions\ResetToDraftAction;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions\ReverseAction;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions\SetAsCheckedAction;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\CreateInvoice;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\EditInvoice;
use Webkul\Account\Filament\Resources\InvoiceResource\Pages\ListInvoices;
use Webkul\Account\Models\Move;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../Helpers/AccountHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');

    DB::table('plugins')->updateOrInsert(
        ['name' => 'accounts'],
        ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
    );

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');
});

afterEach(fn () => Model::preventSilentlyDiscardingAttributes(false));

it('forbids listing invoices without permission', function () {
    FilamentHelper::actingAs([]);

    Livewire::test(ListInvoices::class)->assertForbidden();
});

it('lists invoices with their key columns for authorized users', function () {
    FilamentHelper::actingAs(['view_any_account_invoice']);

    Livewire::test(ListInvoices::class)
        ->assertOk()
        ->assertCanRenderTableColumn('name')
        ->assertCanRenderTableColumn('state');
});

it('renders the invoice create page', function () {
    FilamentHelper::actingAs(['view_any_account_invoice', 'create_account_invoice']);

    Livewire::test(CreateInvoice::class)->assertOk();
});

it('posts a draft invoice through the confirm action', function () {
    FilamentHelper::actingAs(['view_any_account_invoice', 'update_account_invoice']);

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE);
    AccountHelper::productLine($invoice, AccountHelper::account('income'), qty: 2, priceUnit: 100);
    AccountHelper::compute($invoice);

    Livewire::test(EditInvoice::class, ['record' => $invoice->id])
        ->assertOk()
        ->assertActionExists(ConfirmAction::class)
        ->callAction(ConfirmAction::class);

    expect($invoice->refresh()->state)->toBe(MoveState::POSTED);
});

it('cancels a draft invoice through the cancel action', function () {
    FilamentHelper::actingAs(['view_any_account_invoice', 'update_account_invoice']);

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE);

    Livewire::test(EditInvoice::class, ['record' => $invoice->id])
        ->assertOk()
        ->callAction(CancelAction::class);

    expect($invoice->refresh()->state)->toBe(MoveState::CANCEL);
});

function postedInvoiceRecord(): Move
{
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE);

    AccountHelper::productLine($invoice, AccountHelper::account('income'), qty: 2, priceUnit: 100);

    return AccountHelper::post($invoice);
}

it('reverses a posted invoice into a credit note through the action', function () {
    FilamentHelper::actingAs(['view_any_account_invoice', 'update_account_invoice']);

    $invoice = postedInvoiceRecord();

    Livewire::test(EditInvoice::class, ['record' => $invoice->id])
        ->assertOk()
        ->callAction(ReverseAction::class, data: [
            'reason'     => 'Test reversal',
            'journal_id' => $invoice->journal_id,
            'date'       => now(),
        ]);

    expect(
        Move::query()
            ->where('reversed_entry_id', $invoice->id)
            ->where('move_type', MoveType::OUT_REFUND)
            ->exists()
    )->toBeTrue();
});

it('resets a posted invoice to draft through the action', function () {
    FilamentHelper::actingAs(['view_any_account_invoice', 'update_account_invoice']);

    $invoice = postedInvoiceRecord();

    Livewire::test(EditInvoice::class, ['record' => $invoice->id])
        ->assertOk()
        ->callAction(ResetToDraftAction::class);

    expect($invoice->refresh()->state)->toBe(MoveState::DRAFT);
});

it('marks a posted invoice as checked through the action', function () {
    FilamentHelper::actingAs(['view_any_account_invoice', 'update_account_invoice']);

    $invoice = postedInvoiceRecord();

    Livewire::test(EditInvoice::class, ['record' => $invoice->id])
        ->assertOk()
        ->callAction(SetAsCheckedAction::class);

    expect($invoice->refresh()->checked)->toBeTrue();
});

it('registers a full payment and marks the invoice paid through the action', function () {
    FilamentHelper::actingAs(['view_any_account_invoice', 'update_account_invoice']);

    AccountHelper::bankJournal();

    $invoice = postedInvoiceRecord();

    Livewire::test(EditInvoice::class, ['record' => $invoice->id])
        ->assertOk()
        ->assertActionVisible(PayAction::class)
        ->callAction(PayAction::class)
        ->assertActionHidden(PayAction::class);

    expect($invoice->refresh()->payment_state)->toBe(PaymentState::PAID);

    expect($invoice->matchedPayments)->toHaveCount(1);

    expect($invoice->matchedPayments->first()->state)->toBe(PaymentStatus::PAID);
});

it('marks all payments paid once the invoice is fully paid in installments', function () {
    FilamentHelper::actingAs(['view_any_account_invoice', 'update_account_invoice']);

    AccountHelper::bankJournal();

    $invoice = postedInvoiceRecord();

    Livewire::test(EditInvoice::class, ['record' => $invoice->id])
        ->assertOk()
        ->callAction(PayAction::class, data: ['amount' => 100]);

    $invoice->refresh();

    expect($invoice->payment_state)->toBe(PaymentState::PARTIAL);

    expect($invoice->matchedPayments->first()->state)->toBe(PaymentStatus::IN_PROCESS);

    Livewire::test(EditInvoice::class, ['record' => $invoice->id])
        ->assertOk()
        ->callAction(PayAction::class, data: ['amount' => 100]);

    $invoice->refresh();

    expect($invoice->payment_state)->toBe(PaymentState::PAID);

    expect($invoice->matchedPayments)->toHaveCount(2);

    $invoice->matchedPayments->each(
        fn ($payment) => expect($payment->state)->toBe(PaymentStatus::PAID)
    );
});

it('creates an invoice with a taxed product line through the create form with strict model writes', function () {
    FilamentHelper::actingAs(['view_any_account_invoice', 'create_account_invoice']);

    $partner = AccountHelper::partner();

    $product = AccountHelper::product();

    $tax = AccountHelper::tax(type: TypeTaxUse::SALE);

    Model::preventSilentlyDiscardingAttributes();

    Livewire::test(CreateInvoice::class)
        ->fillForm([
            'partner_id'   => $partner->id,
            'invoice_date' => now(),
            'journal_id'   => AccountHelper::saleJournal()->id,
            'currency_id'  => AccountHelper::currency()->id,
            'products'     => [
                [
                    'product_id' => $product->id,
                    'quantity'   => 2,
                    'uom_id'     => AccountHelper::unitsUom()->id,
                    'price_unit' => 100,
                    'discount'   => 0,
                    'taxes'      => [$tax->id],
                ],
            ],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $line = Move::query()
        ->where('partner_id', $partner->id)
        ->where('move_type', MoveType::OUT_INVOICE)
        ->firstOrFail()
        ->invoiceLines()
        ->firstOrFail();

    expect($line->product_id)->toBe($product->id)
        ->and((float) $line->quantity)->toBe(2.0)
        ->and($line->taxes->pluck('id')->all())->toBe([$tax->id]);
});
