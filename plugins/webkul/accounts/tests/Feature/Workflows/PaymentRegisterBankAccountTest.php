<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentType;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\PaymentRegister;
use Webkul\Partner\Models\BankAccount;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/AccountHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');

    DB::table('plugins')->updateOrInsert(
        ['name' => 'accounts'],
        ['is_installed' => true, 'is_active' => true, 'updated_at' => now()],
    );

    Package::$plugins = Plugin::all()->keyBy('name');

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    AccountHelper::actingAsAdmin();

    $this->company = AccountHelper::company();
});

function registerFor(Move $move, Journal $journal): PaymentRegister
{
    $register = new PaymentRegister;
    $register->lines = $move->lines;
    $register->company = $move->company;
    $register->currency = $move->currency;
    $register->currency_id = $move->currency_id;
    $register->payment_type = $move->isInbound(true) ? PaymentType::RECEIVE : PaymentType::SEND;
    $register->computeBatches();
    $register->journal_id = $journal->id;
    $register->journal = $journal;

    return $register;
}

function postedInvoice(MoveType $type): Move
{
    $move = AccountHelper::invoice($type);

    AccountHelper::productLine(
        $move,
        AccountHelper::account($type === MoveType::IN_INVOICE ? 'expense' : 'income'),
        1,
        500,
    );

    return AccountHelper::post(AccountHelper::compute($move));
}

it('offers the journal bank account as the only recipient bank for an inbound payment', function () {
    $journal = AccountHelper::bankJournal();

    $bankAccount = BankAccount::factory()->create([
        'partner_id' => $this->company->partner_id,
    ]);

    $journal->update(['bank_account_id' => $bankAccount->id]);

    $register = registerFor(postedInvoice(MoveType::OUT_INVOICE), $journal->refresh());

    $available = $register->getBatchAvailablePartnerBanks($register->batches[0], $register->journal);

    expect($available)->toHaveCount(1)
        ->and($available->first())->toBeInstanceOf(BankAccount::class)
        ->and($available->first()->id)->toBe($bankAccount->id);
});

it('offers no recipient bank for an inbound payment when the journal has no bank account', function () {
    $journal = AccountHelper::bankJournal();

    $journal->update(['bank_account_id' => null]);

    $register = registerFor(postedInvoice(MoveType::OUT_INVOICE), $journal->refresh());

    $available = $register->getBatchAvailablePartnerBanks($register->batches[0], $register->journal);

    expect($available)->toBeEmpty();
});

it('offers the vendor bank accounts as recipient banks for an outbound payment', function () {
    $journal = AccountHelper::bankJournal();

    $bill = postedInvoice(MoveType::IN_INVOICE);

    $vendorAccounts = BankAccount::factory()->count(2)->create([
        'partner_id' => $bill->partner_id,
    ]);

    $register = registerFor($bill, $journal);

    $available = $register->getBatchAvailablePartnerBanks($register->batches[0], $register->journal);

    expect($available->pluck('id')->all())
        ->toContain($vendorAccounts[0]->id)
        ->toContain($vendorAccounts[1]->id)
        ->and($available->pluck('partner_id')->unique()->values()->all())
        ->toBe([$bill->partner_id]);
});

it('resolves the batch account to a bank account model for an inbound payment', function () {
    $journal = AccountHelper::bankJournal();

    $bankAccount = BankAccount::factory()->create([
        'partner_id' => $this->company->partner_id,
    ]);

    $journal->update(['bank_account_id' => $bankAccount->id]);

    $register = registerFor(postedInvoice(MoveType::OUT_INVOICE), $journal->refresh());

    $account = $register->getBatchAccount($register->batches[0]);

    expect($account)->toBeInstanceOf(BankAccount::class)
        ->and($account->id)->toBe($bankAccount->id);
});

it('resolves the batch account to the bank account already set on the bill', function () {
    $journal = AccountHelper::bankJournal();

    $bill = AccountHelper::invoice(MoveType::IN_INVOICE);

    $preferred = BankAccount::factory()->create([
        'partner_id' => $bill->partner_id,
    ]);

    BankAccount::factory()->create([
        'partner_id' => $bill->partner_id,
    ]);

    $bill->update(['partner_bank_id' => $preferred->id]);

    AccountHelper::productLine($bill, AccountHelper::account('expense'), 1, 500);

    $bill = AccountHelper::post(AccountHelper::compute($bill));

    $register = registerFor($bill, $journal);

    $account = $register->getBatchAccount($register->batches[0]);

    expect($account)->toBeInstanceOf(BankAccount::class)
        ->and($account->id)->toBe($preferred->id);
});
