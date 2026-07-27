<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions\CancelAction;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions\ConfirmAction;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions\PayAction;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions\ResetToDraftAction;
use Webkul\Account\Filament\Resources\InvoiceResource\Actions\SetAsCheckedAction;
use Webkul\Account\Filament\Resources\RefundResource\Pages\CreateRefund;
use Webkul\Account\Filament\Resources\RefundResource\Pages\EditRefund;
use Webkul\Account\Filament\Resources\RefundResource\Pages\ListRefunds;
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

it('forbids listing refunds without permission', function () {
    FilamentHelper::actingAs([]);

    Livewire::test(ListRefunds::class)->assertForbidden();
});

it('lists refunds with their key columns for authorized users', function () {
    FilamentHelper::actingAs(['view_any_account_refund']);

    Livewire::test(ListRefunds::class)
        ->assertOk()
        ->assertCanRenderTableColumn('name')
        ->assertCanRenderTableColumn('state');
});

it('renders the refund create page', function () {
    FilamentHelper::actingAs(['view_any_account_refund', 'create_account_refund']);

    Livewire::test(CreateRefund::class)->assertOk();
});

it('posts a draft refund through the confirm action', function () {
    FilamentHelper::actingAs(['view_any_account_refund', 'update_account_refund']);

    $refund = AccountHelper::invoice(MoveType::IN_REFUND, null, null, ['invoice_date' => now()]);
    AccountHelper::productLine($refund, AccountHelper::account('expense'), qty: 2, priceUnit: 100);
    AccountHelper::compute($refund);

    Livewire::test(EditRefund::class, ['record' => $refund->id])
        ->assertOk()
        ->assertActionExists(ConfirmAction::class)
        ->callAction(ConfirmAction::class);

    expect($refund->refresh()->state)->toBe(MoveState::POSTED);
});

it('cancels a draft refund through the cancel action', function () {
    FilamentHelper::actingAs(['view_any_account_refund', 'update_account_refund']);

    $refund = AccountHelper::invoice(MoveType::IN_REFUND, null, null, ['invoice_date' => now()]);

    Livewire::test(EditRefund::class, ['record' => $refund->id])
        ->assertOk()
        ->callAction(CancelAction::class);

    expect($refund->refresh()->state)->toBe(MoveState::CANCEL);
});

function postedRefundRecord(): Move
{
    $refund = AccountHelper::invoice(MoveType::IN_REFUND, null, null, ['invoice_date' => now()]);

    AccountHelper::productLine($refund, AccountHelper::account('expense'), qty: 2, priceUnit: 100);

    return AccountHelper::post($refund);
}

it('resets a posted refund to draft through the action', function () {
    FilamentHelper::actingAs(['view_any_account_refund', 'update_account_refund']);

    $refund = postedRefundRecord();

    Livewire::test(EditRefund::class, ['record' => $refund->id])
        ->assertOk()
        ->callAction(ResetToDraftAction::class);

    expect($refund->refresh()->state)->toBe(MoveState::DRAFT);
});

it('marks a posted refund as checked through the action', function () {
    FilamentHelper::actingAs(['view_any_account_refund', 'update_account_refund']);

    $refund = postedRefundRecord();

    Livewire::test(EditRefund::class, ['record' => $refund->id])
        ->assertOk()
        ->callAction(SetAsCheckedAction::class);

    expect($refund->refresh()->checked)->toBeTrue();
});

it('registers a full payment and marks the refund paid through the action', function () {
    FilamentHelper::actingAs(['view_any_account_refund', 'update_account_refund']);

    AccountHelper::bankJournal();

    $refund = postedRefundRecord();

    Livewire::test(EditRefund::class, ['record' => $refund->id])
        ->assertOk()
        ->callAction(PayAction::class);

    expect($refund->refresh()->payment_state)->toBe(PaymentState::PAID);
});

