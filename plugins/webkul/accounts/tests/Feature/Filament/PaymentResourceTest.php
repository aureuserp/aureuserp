<?php

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Account\Contracts\PostingValidator;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentStatus;
use Webkul\Account\Enums\PostingOperation;
use Webkul\Account\Exceptions\PostingRejectedException;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Filament\Resources\PaymentResource\Actions\ResetToDraftAction;
use Webkul\Account\Filament\Resources\PaymentResource\Pages\ViewPayment;
use Webkul\Account\Models\Payment;
use Webkul\Account\Support\Posting\PostingContext;
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

    FilamentHelper::actingAs(['view_any_account_payment', 'view_account_payment', 'update_account_payment']);
});

function paidInvoicePayment(): Payment
{
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE);

    AccountHelper::productLine($invoice, AccountHelper::account('income'), qty: 2, priceUnit: 100);

    return AccountHelper::pay(AccountHelper::post($invoice))->matchedPayments->first();
}

it('resets a paid payment and its entry to draft through the action', function () {
    $payment = paidInvoicePayment();

    Livewire::test(ViewPayment::class, ['record' => $payment->id])
        ->assertOk()
        ->callAction(ResetToDraftAction::class);

    expect($payment->refresh()->state)->toBe(PaymentStatus::DRAFT)
        ->and($payment->move->state)->toBe(MoveState::DRAFT);
});

it('keeps the payment and its entry unchanged and shows the reason when a validator refuses reset to draft', function () {
    $payment = paidInvoicePayment();

    $state = $payment->state;

    AccountFacade::posting()->registerValidator(new class implements PostingValidator
    {
        public function validate(PostingContext $context): void
        {
            if ($context->operation === PostingOperation::RESET_TO_DRAFT) {
                throw new PostingRejectedException('closed period');
            }
        }
    });

    Livewire::test(ViewPayment::class, ['record' => $payment->id])
        ->assertOk()
        ->callAction(ResetToDraftAction::class)
        ->assertNotified(Notification::make()
            ->warning()
            ->body(__('accounts::system.posting.rejected-by-validator', ['reason' => 'closed period'])));

    expect($payment->refresh()->state)->toBe($state)
        ->and($payment->move->state)->toBe(MoveState::POSTED)
        ->and($payment->move->lines->every(fn ($line) => $line->parent_state === MoveState::POSTED))->toBeTrue();
});
