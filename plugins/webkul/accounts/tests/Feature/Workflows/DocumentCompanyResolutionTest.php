<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\Category;
use Webkul\Account\Models\MoveLine;
use Webkul\Account\Models\Partner;
use Webkul\Account\Models\Product;
use Webkul\Account\Settings\DefaultAccountSettings;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\Support\Models\Company;

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

    SecurityHelper::disableUserEvents();

    $this->companyA = Company::query()->firstOrFail();
    $this->companyB = CompanyHelper::company();

    CompanyHelper::actingAsCompanyUser(
        [$this->companyA, $this->companyB],
        activeIds: [$this->companyA->id, $this->companyB->id],
    );

    $this->receivableA = AccountHelper::account('receivable');
    $this->receivableB = AccountHelper::account('receivable');

    $this->incomeA = AccountHelper::account('income');
    $this->incomeB = AccountHelper::account('income');

    $this->partner = Partner::factory()->create();

    $this->emptyCategory = Category::query()->create([
        'name' => 'No accounts '.uniqid(),
    ]);

    $this->product = AccountHelper::product([
        'company_id'  => null,
        'category_id' => $this->emptyCategory->id,
    ]);

    $this->setPerCompany = function ($record, string $field, array $valuePerCompany) {
        foreach ($valuePerCompany as $companyId => $value) {
            CompanyHelper::setActive([$companyId]);

            $fresh = $record->newQuery()->findOrFail($record->id);
            $fresh->{$field} = $value;
            $fresh->save();
        }

        CompanyHelper::setActive([$this->companyA->id, $this->companyB->id]);
    };
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('resolves the current company from the user default when several are active', function () {
    expect(current_company_id())->toBe($this->companyA->id);
});

it('takes the receivable account from the company of the invoice, not the active company', function () {
    ($this->setPerCompany)($this->partner, 'property_account_receivable_id', [
        $this->companyA->id => $this->receivableA->id,
        $this->companyB->id => $this->receivableB->id,
    ]);

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner, null, [
        'company_id' => $this->companyB->id,
    ]);

    AccountHelper::productLine($invoice, $this->incomeA, qty: 1, priceUnit: 100);

    AccountHelper::post($invoice);

    $termLine = $invoice->refresh()->lines->firstWhere('display_type', DisplayType::PAYMENT_TERM);

    expect($termLine->account_id)->toBe($this->receivableB->id);
});

it('takes the product income account from the company of the invoice', function () {
    ($this->setPerCompany)($this->product, 'property_account_income_id', [
        $this->companyA->id => $this->incomeA->id,
        $this->companyB->id => $this->incomeB->id,
    ]);

    $invoice = AccountHelper::invoice(MoveType::OUT_INVOICE, $this->partner, null, [
        'company_id' => $this->companyB->id,
    ]);

    MoveLine::factory()->create([
        'move_id'      => $invoice->id,
        'display_type' => DisplayType::PRODUCT,
        'account_id'   => $this->incomeA->id,
        'product_id'   => $this->product->id,
        'uom_id'       => AccountHelper::unitsUom()->id,
        'quantity'     => 1,
        'price_unit'   => 100,
        'currency_id'  => $invoice->currency_id,
        'company_id'   => $this->companyB->id,
    ]);

    AccountHelper::post($invoice);

    $productLine = $invoice->refresh()->lines->firstWhere('display_type', DisplayType::PRODUCT);

    expect($productLine->account_id)->toBe($this->incomeB->id);
});

it('reads a product account for an explicit company regardless of the active one', function () {
    ($this->setPerCompany)($this->product, 'property_account_income_id', [
        $this->companyA->id => $this->incomeA->id,
        $this->companyB->id => $this->incomeB->id,
    ]);

    $product = Product::query()->findOrFail($this->product->id);

    expect(current_company_id())->toBe($this->companyA->id)
        ->and($product->getAccounts($this->companyB->id)['income']?->id)->toBe($this->incomeB->id)
        ->and($product->getAccounts($this->companyA->id)['income']?->id)->toBe($this->incomeA->id);
});

it('reads a partner account for an explicit company regardless of the active one', function () {
    ($this->setPerCompany)($this->partner, 'property_account_receivable_id', [
        $this->companyA->id => $this->receivableA->id,
        $this->companyB->id => $this->receivableB->id,
    ]);

    $partner = Partner::query()->findOrFail($this->partner->id);

    expect($partner->companyPropertyValue('property_account_receivable_id', $this->companyB->id))
        ->toBe($this->receivableB->id)
        ->and($partner->companyPropertyValue('property_account_receivable_id', $this->companyA->id))
        ->toBe($this->receivableA->id);
});

it('resolves the configured default account to the equivalent one of the given company', function () {
    $settings = app(DefaultAccountSettings::class);

    $configured = Account::withoutGlobalScopes()->findOrFail($settings->income_account_id);

    $equivalent = AccountHelper::account('income');
    $equivalent->forceFill(['code' => $configured->code])->save();
    $equivalent->companies()->sync([$this->companyB->id]);

    expect(Account::resolveForCompany($settings->income_account_id, $this->companyB->id))
        ->toBe($equivalent->id);
});

it('falls back to the default account of the invoice company when nothing else is set', function () {
    $settings = app(DefaultAccountSettings::class);

    $configured = Account::withoutGlobalScopes()->findOrFail($settings->income_account_id);

    $equivalent = AccountHelper::account('income');
    $equivalent->forceFill(['code' => $configured->code])->save();
    $equivalent->companies()->sync([$this->companyB->id]);

    $product = Product::query()->findOrFail($this->product->id);

    expect($product->getAccounts($this->companyB->id)['income']?->id)->toBe($equivalent->id);
});
