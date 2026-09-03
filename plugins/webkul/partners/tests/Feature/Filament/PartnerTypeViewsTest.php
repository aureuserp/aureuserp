<?php

use Webkul\Account\Filament\Resources\CustomerResource\Pages\ListCustomers;
use Webkul\Account\Filament\Resources\VendorResource\Pages\ListVendors;
use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Filament\Resources\PartnerResource\Pages\ListPartners;
use Webkul\Partner\Models\Partner;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    // Customer and vendor ranks are columns the accounts plugin adds.
    TestBootstrapHelper::ensurePluginInstalled('accounts');
});

/**
 * Every partner built here is stamped, so the assertions can ignore the partners the
 * installation seeds for itself.
 */
const PARTNER_TYPE_TEST_REFERENCE = 'partner-type-views-test';

function partnerOfType(string $name, array $attributes = []): Partner
{
    return Partner::create(array_merge([
        'account_type' => AccountType::INDIVIDUAL,
        'sub_type'     => 'partner',
        'name'         => $name,
        'reference'    => PARTNER_TYPE_TEST_REFERENCE,
    ], $attributes));
}

/**
 * The names a preset view keeps, out of the partners this test created.
 */
function namesInView(string $view): array
{
    $query = Partner::query()->where('reference', PARTNER_TYPE_TEST_REFERENCE);

    return (new ListPartners)->getPresetTableViews()[$view]
        ->modifyQuery($query)
        ->pluck('name')
        ->sort()
        ->values()
        ->all();
}

it('keeps only employees in the employees view', function () {
    partnerOfType('Employee One', ['sub_type' => 'employee']);
    partnerOfType('Plain Partner');
    partnerOfType('Ranked Customer', ['customer_rank' => 1]);

    expect(namesInView('employees'))->toBe(['Employee One']);
});

it('keeps only ranked customers in the customers view', function () {
    partnerOfType('Ranked Customer', ['customer_rank' => 1]);
    partnerOfType('Ranked Vendor', ['supplier_rank' => 1]);
    partnerOfType('Unranked', ['customer_rank' => 0]);
    partnerOfType('Never Ranked');

    expect(namesInView('customers'))->toBe(['Ranked Customer']);
});

it('keeps only ranked vendors in the vendors view', function () {
    partnerOfType('Ranked Vendor', ['supplier_rank' => 1]);
    partnerOfType('Ranked Customer', ['customer_rank' => 1]);
    partnerOfType('Unranked', ['supplier_rank' => 0]);
    partnerOfType('Never Ranked');

    expect(namesInView('vendors'))->toBe(['Ranked Vendor']);
});

it('leaves the existing views in place', function () {
    partnerOfType('An Individual');
    partnerOfType('Acme Corp', ['account_type' => AccountType::COMPANY]);

    expect(namesInView('individuals'))->toBe(['An Individual'])
        ->and(namesInView('companies'))->toBe(['Acme Corp'])
        ->and(namesInView('archived'))->toBe([]);
});

it('offers the type views on the shared contact list in a stable order', function () {
    expect(array_keys((new ListPartners)->getPresetTableViews()))
        ->toBe(['individuals', 'companies', 'employees', 'customers', 'vendors', 'archived']);
});

it('drops the redundant type views on the vendor and customer lists', function (string $page) {
    $views = array_keys((new $page)->getPresetTableViews());

    expect($views)->not->toContain('employees')
        ->and($views)->not->toContain('customers')
        ->and($views)->not->toContain('vendors')
        ->and($views)->toBe(['individuals', 'companies', 'archived']);
})->with([[ListVendors::class], [ListCustomers::class]]);
