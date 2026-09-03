<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Webkul\Account\Enums\MoveType;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Support\Models\Currency;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
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

    $this->otherCurrency = Currency::factory()->create(['name' => 'CGC']);
});

function postAnInvoice(): void
{
    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE);

    AccountHelper::productLine($invoice, AccountHelper::account('income'), 1, 500);

    AccountHelper::post(AccountHelper::compute($invoice));
}

it('allows changing the company currency while no journal items exist', function () {
    $this->company->update(['currency_id' => $this->otherCurrency->id]);

    expect($this->company->refresh()->currency_id)->toBe($this->otherCurrency->id);
});

it('blocks changing the company currency once journal items exist', function () {
    postAnInvoice();

    expect(fn () => $this->company->update(['currency_id' => $this->otherCurrency->id]))
        ->toThrow(ValidationException::class);
});

it('keeps the original currency when the change is blocked', function () {
    postAnInvoice();

    $original = $this->company->currency_id;

    try {
        $this->company->update(['currency_id' => $this->otherCurrency->id]);
    } catch (ValidationException) {
        // expected
    }

    expect($this->company->refresh()->currency_id)->toBe($original);
});

it('reports the blocked change against the currency form field', function () {
    postAnInvoice();

    try {
        $this->company->update(['currency_id' => $this->otherCurrency->id]);
    } catch (ValidationException $e) {
        expect($e->errors())->toHaveKey('data.currency_id');

        return;
    }

    $this->fail('The currency change was not blocked.');
});

it('allows editing other company fields once journal items exist', function () {
    postAnInvoice();

    $this->company->update(['name' => 'Renamed Company']);

    expect($this->company->refresh()->name)->toBe('Renamed Company');
});

it('blocks changing a parent company currency when only a branch has journal items', function () {
    $parent = CompanyHelper::company();

    DB::table('companies')
        ->where('id', $this->company->id)
        ->update(['parent_id' => $parent->id]);

    postAnInvoice();

    expect(fn () => $parent->update(['currency_id' => $this->otherCurrency->id]))
        ->toThrow(ValidationException::class);
});

it('blocks changing a branch currency when only the parent has journal items', function () {
    $branch = CompanyHelper::company();

    DB::table('companies')
        ->where('id', $branch->id)
        ->update(['parent_id' => $this->company->id]);

    $branch->refresh();

    postAnInvoice();

    expect(fn () => $branch->update(['currency_id' => $this->otherCurrency->id]))
        ->toThrow(ValidationException::class);
});

it('ignores journal items belonging to an unrelated company', function () {
    $unrelated = CompanyHelper::company();

    postAnInvoice();

    $unrelated->update(['currency_id' => $this->otherCurrency->id]);

    expect($unrelated->refresh()->currency_id)->toBe($this->otherCurrency->id);
});
