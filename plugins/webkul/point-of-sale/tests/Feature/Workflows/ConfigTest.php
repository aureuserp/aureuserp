<?php

use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Models\Account;
use Webkul\Inventory\Models\OperationType;
use Webkul\PointOfSale\Enums\PaymentMethodType;
use Webkul\PointOfSale\Enums\SessionState;
use Webkul\PointOfSale\Enums\TaxDisplay;
use Webkul\PointOfSale\Exceptions\PosConfigurationException;
use Webkul\PointOfSale\Models\Config;
use Webkul\PointOfSale\Models\PaymentMethod;
use Webkul\PointOfSale\Models\Warehouse;
use Webkul\PointOfSale\Services\SessionPreflight;
use Webkul\PointOfSale\Settings\AccountSettings;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Scopes\CompanyScope;
use Webkul\Support\Models\Sequence;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../inventories/tests/Helpers/InventoryHelper.php';
require_once __DIR__.'/../../Helpers/PosHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    TestBootstrapHelper::ensurePluginInstalled('accounts');
    TestBootstrapHelper::ensurePluginInstalled('point-of-sale');

    InventoryHelper::actingAsAdmin();

    $this->warehouse = InventoryHelper::warehouse();
});

it('creates a terminal with the expected defaults', function () {
    $config = PosHelper::config($this->warehouse);

    expect($config->tax_display)->toBe(TaxDisplay::SUBTOTAL)
        ->and($config->is_active)->toBeTrue()
        ->and($config->is_restaurant)->toBeFalse();
});

it('upper cases the terminal code', function () {
    $config = PosHelper::config($this->warehouse, ['code' => 'shop one']);

    expect($config->code)->toBe('SHOP ONE');
});

it('stamps an access token on creation', function () {
    $config = PosHelper::config($this->warehouse);

    expect($config->access_token)->not->toBeNull();
});

it('provisions an order sequence and a session sequence', function () {
    $config = PosHelper::config($this->warehouse, ['code' => 'SHOP']);

    $sequences = Sequence::withoutGlobalScope(CompanyScope::class)
        ->where('scope_type', $config->getMorphClass())
        ->where('scope_id', $config->id)
        ->get();

    expect($sequences)->toHaveCount(2)
        ->and($sequences->pluck('prefix')->all())->toContain('SHOP/', 'SHOP/SESSION/');
});

it('renames its sequences when the terminal code changes', function () {
    $config = PosHelper::config($this->warehouse, ['code' => 'SHOP']);

    $config->update(['code' => 'STORE']);

    $prefixes = Sequence::withoutGlobalScope(CompanyScope::class)
        ->where('scope_type', $config->getMorphClass())
        ->where('scope_id', $config->id)
        ->pluck('prefix');

    expect($prefixes->all())->toContain('STORE/', 'STORE/SESSION/');
});

it('derives the terminal currency from its company', function () {
    $config = PosHelper::config($this->warehouse);

    expect($config->currency_id)->toBe(PosHelper::company()->currency_id);
});

it('attaches payment methods through the pivot', function () {
    $config = PosHelper::config($this->warehouse);
    $bank = PosHelper::bankMethod();

    $config->paymentMethods()->attach($bank);

    expect($config->refresh()->paymentMethods->pluck('id'))->toContain($bank->id);
});

it('derives a cash payment method from its journal', function () {
    $cash = PosHelper::cashMethod();

    expect($cash->type)->toBe(PaymentMethodType::CASH)
        ->and($cash->is_cash_count)->toBeTrue();
});

it('derives a pay later payment method when no journal is set', function () {
    $payLater = PosHelper::payLaterMethod();

    expect($payLater->type)->toBe(PaymentMethodType::PAY_LATER)
        ->and($payLater->is_cash_count)->toBeFalse();
});

it('soft deletes a terminal without removing its row', function () {
    $config = PosHelper::config($this->warehouse);

    $config->delete();

    expect(Config::query()->whereKey($config->id)->exists())->toBeFalse()
        ->and(Config::withTrashed()->whereKey($config->id)->exists())->toBeTrue();
});

it('defaults the receivable account when none was given', function () {
    $config = Config::create([
        'name'       => 'Defaults Shop',
        'code'       => 'DEF',
        'company_id' => PosHelper::company()->id,
    ]);

    expect($config->receivable_account_id)->not->toBeNull()
        ->and($config->receivableAccount->account_type)->toBe(AccountType::ASSET_RECEIVABLE)
        ->and((bool) $config->receivableAccount->reconcile)->toBeTrue();
});

it('ignores a configured receivable account that belongs to another company', function () {
    $foreignAccount = Account::factory()->create([
        'account_type' => AccountType::ASSET_RECEIVABLE,
        'reconcile'    => true,
    ]);

    $foreignAccount->companies()->sync([Company::factory()->create()->id]);

    $settings = app(AccountSettings::class);
    $settings->receivable_account_id = $foreignAccount->id;
    $settings->save();

    $config = Config::create([
        'name'       => 'Foreign Receivable Shop',
        'code'       => 'FRS',
        'company_id' => PosHelper::company()->id,
    ]);

    expect($config->receivable_account_id)->not->toBeNull()
        ->not->toBe($foreignAccount->id);
});

it('defaults the warehouse and its operation types', function () {
    InventoryHelper::warehouse();

    $config = Config::create([
        'name'       => 'Warehouse Defaults',
        'code'       => 'WHD',
        'company_id' => PosHelper::company()->id,
    ]);

    $warehouse = Warehouse::findOrFail($config->warehouse_id);

    expect($config->warehouse_id)->not->toBeNull()
        ->and($config->operation_type_id)->toBe($warehouse->pos_type_id)
        ->and($config->return_operation_type_id)->toBe($warehouse->pos_return_type_id);
});

it('defaults the journals when none were given', function () {
    $config = Config::create([
        'name'       => 'Journal Defaults',
        'code'       => 'JRD',
        'company_id' => PosHelper::company()->id,
    ]);

    expect($config->journal->type)->toBe(JournalType::GENERAL)
        ->and($config->journal->code)->toBe(Config::TERMINAL_JOURNAL_CODE)
        ->and($config->invoiceJournal->type)->toBe(JournalType::SALE);
});

it('attaches a cash payment method to a brand new terminal', function () {
    PosHelper::cashMethod();

    $config = Config::create([
        'name'       => 'Payment Defaults',
        'code'       => 'PMD',
        'company_id' => PosHelper::company()->id,
    ]);

    expect($config->refresh()->paymentMethods)->not->toBeEmpty()
        ->and($config->paymentMethods->contains(fn ($method): bool => (bool) $method->is_cash_count))->toBeTrue();
});

it('does not steal a cash method that another terminal already uses', function () {
    $warehouse = InventoryHelper::warehouse();

    $first = PosHelper::configWithCashMethod($warehouse);

    $second = Config::create([
        'name'       => 'Second Shop',
        'code'       => 'SND',
        'company_id' => PosHelper::company()->id,
    ]);

    $taken = $first->paymentMethods->pluck('id')->all();

    expect($second->refresh()->paymentMethods->pluck('id')->intersect($taken))->toBeEmpty();
});

it('passes the opening preflight straight after creation', function () {
    InventoryHelper::warehouse();

    PosHelper::cashMethod();

    $config = Config::create([
        'name'       => 'Ready Shop',
        'code'       => 'RDY',
        'company_id' => PosHelper::company()->id,
    ]);

    app(SessionPreflight::class)->assertCanOpen($config->refresh());
})->throwsNoExceptions();

it('recreates the point of sale operation type when it was deleted from the warehouse', function () {
    $posWarehouse = PosHelper::warehouse($this->warehouse);

    OperationType::withTrashed()->whereKey($posWarehouse->pos_type_id)->forceDelete();

    $config = PosHelper::config($this->warehouse, [
        'operation_type_id'        => null,
        'return_operation_type_id' => null,
    ]);

    $posWarehouse->refresh();

    expect($posWarehouse->pos_type_id)->not->toBeNull()
        ->and($config->operation_type_id)->toBe($posWarehouse->pos_type_id)
        ->and($config->return_operation_type_id)->toBe($posWarehouse->pos_return_type_id);
});

it('refuses to delete a payment method while a register using it has an open session', function () {
    $session = PosHelper::openSession($this->warehouse);

    $method = $session->config->paymentMethods->first();

    expect(fn () => $method->delete())->toThrow(PosConfigurationException::class)
        ->and(fn () => $method->forceDelete())->toThrow(PosConfigurationException::class)
        ->and(PaymentMethod::withTrashed()->whereKey($method->id)->first()?->trashed())->toBeFalse();

    $session->forceFill(['state' => SessionState::CLOSED])->save();

    PaymentMethod::whereKey($method->id)->firstOrFail()->delete();

    expect(PaymentMethod::withTrashed()->whereKey($method->id)->firstOrFail()->trashed())->toBeTrue();
});
