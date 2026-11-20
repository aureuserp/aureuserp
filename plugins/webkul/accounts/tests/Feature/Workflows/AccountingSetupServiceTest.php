<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Monolog\Handler\TestHandler;
use Monolog\Level;
use Webkul\Account\Enums\AccountingSetupResult;
use Webkul\Account\Enums\AmountType;
use Webkul\Account\Enums\DocumentType;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Tax;
use Webkul\Account\Models\TaxPartition;
use Webkul\Account\Services\AccountingSetupService;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');
});

function pointGlobalDefaultCompanyAt(Company $company): void
{
    DB::table('settings')
        ->where('group', 'general')
        ->where('name', 'default_company_id')
        ->delete();

    DB::table('settings')->insert([
        'group'      => 'general',
        'name'       => 'default_company_id',
        'company_id' => null,
        'payload'    => json_encode($company->id),
        'locked'     => false,
    ]);
}

function companyJournalCount(Company $company): int
{
    return DB::table('accounts_journals')->where('company_id', $company->id)->count();
}

function companyAccountingSettingCount(Company $company): int
{
    return DB::table('settings')
        ->whereIn('group', ['accounts_accounts', 'accounts_taxes'])
        ->where('company_id', $company->id)
        ->count();
}

function companyAccountingSetting(Company $company, string $group, string $name): mixed
{
    $payload = DB::table('settings')
        ->where('group', $group)
        ->where('name', $name)
        ->where('company_id', $company->id)
        ->value('payload');

    return $payload === null ? null : json_decode($payload, true);
}

function accountOwnedBy(Company $company): Account
{
    $account = Account::factory()->create();

    $account->companies()->sync([$company->id]);

    return $account;
}

function templateWithAccounting(bool $withSaleAndPurchaseJournals = true, array $settingOverrides = []): array
{
    $template = Company::factory()->create();

    $account = accountOwnedBy($template);

    $journalFactory = Journal::factory()->company($template);

    $journal = $withSaleAndPurchaseJournals
        ? $journalFactory->sale()->create(['default_account_id' => $account->id])
        : $journalFactory->create(['default_account_id' => $account->id]);

    if ($withSaleAndPurchaseJournals) {
        Journal::factory()->purchase()->company($template)->create(['default_account_id' => $account->id]);
    }

    $saleTax = Tax::factory()->company($template)->create();

    $purchaseTax = Tax::factory()->purchase()->company($template)->create();

    $groupTax = Tax::factory()->company($template)->create(['amount_type' => AmountType::GROUP]);

    $groupTax->childrenTaxes()->attach([$saleTax->id, $purchaseTax->id]);

    TaxPartition::factory()->company($template)->create(['tax_id' => $saleTax->id, 'account_id' => $account->id]);

    TaxPartition::factory()->refund()->company($template)->create(['tax_id' => $saleTax->id, 'account_id' => $account->id]);

    $settings = array_merge([
        'accounts_accounts.currency_exchange_journal_id'              => $journal->id,
        'accounts_accounts.income_currency_exchange_account_id'       => $account->id,
        'accounts_accounts.expense_currency_exchange_account_id'      => $account->id,
        'accounts_accounts.account_discount_expense_allocation_id'    => null,
        'accounts_accounts.account_discount_income_allocation_id'     => null,
        'accounts_accounts.account_journal_suspense_account_id'       => $account->id,
        'accounts_accounts.transfer_account_id'                       => $account->id,
        'accounts_accounts.account_journal_payment_debit_account_id'  => $account->id,
        'accounts_accounts.account_journal_payment_credit_account_id' => $account->id,
        'accounts_accounts.income_account_id'                         => $account->id,
        'accounts_accounts.expense_account_id'                        => $account->id,
        'accounts_taxes.account_sale_tax_id'                          => $saleTax->id,
        'accounts_taxes.account_purchase_tax_id'                      => $purchaseTax->id,
        'accounts_taxes.tax_calculation_rounding_method'              => 'round_per_line',
        'accounts_taxes.account_fiscal_country_id'                    => 1,
        'accounts_taxes.account_price_include'                        => 'tax_excluded',
    ], $settingOverrides);

    foreach ($settings as $key => $value) {
        [$group, $name] = explode('.', $key, 2);

        DB::table('settings')->updateOrInsert(
            ['group' => $group, 'name' => $name, 'company_id' => $template->id],
            ['payload' => json_encode($value), 'locked' => false]
        );
    }

    pointGlobalDefaultCompanyAt($template);

    return compact('template', 'account', 'journal', 'saleTax', 'purchaseTax', 'groupTax');
}

function captureSetupLogRecords(): TestHandler
{
    $handler = new TestHandler;

    Log::channel()->getLogger()->pushHandler($handler);

    return $handler;
}

function setupWarningsFor(TestHandler $handler, Company $company): array
{
    return array_values(array_filter(
        $handler->getRecords(),
        function ($record) use ($company) {
            $level = $record['level'] ?? null;
            $level = $level instanceof BackedEnum ? $level->value : $level;

            return $level === Level::Warning->value
                && ($record['context']['company_id'] ?? null) === $company->id;
        }
    ));
}

it('reports no usable template and provisions nothing when the template company resolves to the target itself', function () {
    $target = Company::factory()->create();

    pointGlobalDefaultCompanyAt($target);

    expect(app(AccountingSetupService::class)->isSetUp($target))->toBeFalse();

    $result = app(AccountingSetupService::class)->setUp($target);

    expect($result)->toBe(AccountingSetupResult::NO_TEMPLATE)
        ->and(companyJournalCount($target))->toBe(0);
});

it('reports why the setup was skipped instead of failing silently', function () {
    $handler = captureSetupLogRecords();

    $target = Company::factory()->create();

    pointGlobalDefaultCompanyAt($target);

    app(AccountingSetupService::class)->setUp($target);

    expect(setupWarningsFor($handler, $target))->not->toBeEmpty();
});

it('completes the setup from a template that has sale and purchase journals and every required setting', function () {
    $source = templateWithAccounting();

    $target = Company::factory()->create();

    $result = app(AccountingSetupService::class)->setUp($target);

    $journalId = companyAccountingSetting($target, 'accounts_accounts', 'currency_exchange_journal_id');

    $accountId = companyAccountingSetting($target, 'accounts_accounts', 'income_account_id');

    $saleTaxId = companyAccountingSetting($target, 'accounts_taxes', 'account_sale_tax_id');

    expect($result)->toBe(AccountingSetupResult::COMPLETED)
        ->and(app(AccountingSetupService::class)->isSetUp($target))->toBeTrue()
        ->and(companyJournalCount($target))->toBe(2)
        ->and($journalId)->not->toBe($source['journal']->id)
        ->and((int) DB::table('accounts_journals')->where('id', $journalId)->value('company_id'))->toBe($target->id)
        ->and($accountId)->not->toBe($source['account']->id)
        ->and(DB::table('accounts_account_companies')->where('account_id', $accountId)->where('company_id', $target->id)->exists())->toBeTrue()
        ->and($saleTaxId)->not->toBe($source['saleTax']->id)
        ->and((int) DB::table('accounts_taxes')->where('id', $saleTaxId)->value('company_id'))->toBe($target->id);
});

it('copies tax distribution lines and group tax children onto the new company records', function () {
    $source = templateWithAccounting();

    $target = Company::factory()->create();

    app(AccountingSetupService::class)->setUp($target);

    $accountId = companyAccountingSetting($target, 'accounts_accounts', 'income_account_id');

    $saleTaxId = companyAccountingSetting($target, 'accounts_taxes', 'account_sale_tax_id');

    $purchaseTaxId = companyAccountingSetting($target, 'accounts_taxes', 'account_purchase_tax_id');

    $lines = DB::table('accounts_tax_partition_lines')->where('tax_id', $saleTaxId)->get();

    $groupTaxId = DB::table('accounts_taxes')
        ->where('company_id', $target->id)
        ->where('amount_type', AmountType::GROUP->value)
        ->value('id');

    $childIds = DB::table('accounts_tax_taxes')
        ->where('parent_tax_id', $groupTaxId)
        ->pluck('child_tax_id')
        ->map(fn ($id): int => (int) $id)
        ->sort()
        ->values()
        ->all();

    expect($lines->pluck('document_type')->sort()->values()->all())->toBe([DocumentType::INVOICE->value, DocumentType::REFUND->value])
        ->and($lines->every(fn ($line): bool => (int) $line->account_id === $accountId && (int) $line->company_id === $target->id))->toBeTrue()
        ->and($groupTaxId)->not->toBeNull()
        ->and((int) $groupTaxId)->not->toBe($source['groupTax']->id)
        ->and($childIds)->toBe(collect([$saleTaxId, $purchaseTaxId])->sort()->values()->all());
});

it('copies settings the template inherits from the shared defaults and points them at the new company records', function () {
    $source = templateWithAccounting();

    DB::table('settings')
        ->where('group', 'accounts_accounts')
        ->where('name', 'income_account_id')
        ->where('company_id', $source['template']->id)
        ->delete();

    DB::table('settings')->updateOrInsert(
        ['group' => 'accounts_accounts', 'name' => 'income_account_id', 'company_id' => null],
        ['payload' => json_encode($source['account']->id), 'locked' => false]
    );

    $target = Company::factory()->create();

    $result = app(AccountingSetupService::class)->setUp($target);

    $accountId = companyAccountingSetting($target, 'accounts_accounts', 'income_account_id');

    expect($result)->toBe(AccountingSetupResult::COMPLETED)
        ->and($accountId)->not->toBeNull()
        ->and($accountId)->not->toBe($source['account']->id)
        ->and(DB::table('accounts_account_companies')->where('account_id', $accountId)->where('company_id', $target->id)->exists())->toBeTrue();
});

it('rolls back and reports an incomplete setup when the template has no sale or purchase journal', function () {
    $handler = captureSetupLogRecords();

    templateWithAccounting(withSaleAndPurchaseJournals: false);

    $target = Company::factory()->create();

    $result = app(AccountingSetupService::class)->setUp($target);

    $warnings = setupWarningsFor($handler, $target);

    expect($result)->toBe(AccountingSetupResult::INCOMPLETE)
        ->and(app(AccountingSetupService::class)->isSetUp($target))->toBeFalse()
        ->and(companyJournalCount($target))->toBe(0)
        ->and(companyAccountingSettingCount($target))->toBe(0)
        ->and(DB::table('accounts_account_companies')->where('company_id', $target->id)->count())->toBe(0)
        ->and(DB::table('accounts_taxes')->where('company_id', $target->id)->count())->toBe(0)
        ->and($warnings)->not->toBeEmpty()
        ->and(end($warnings)['context']['missing'])->toContain('journal.'.JournalType::SALE->value, 'journal.'.JournalType::PURCHASE->value);
});

it('rolls back and reports an incomplete setup when the template lacks a required default setting', function () {
    templateWithAccounting(settingOverrides: ['accounts_accounts.income_account_id' => null]);

    $target = Company::factory()->create();

    $result = app(AccountingSetupService::class)->setUp($target);

    expect($result)->toBe(AccountingSetupResult::INCOMPLETE)
        ->and(app(AccountingSetupService::class)->isSetUp($target))->toBeFalse()
        ->and(companyJournalCount($target))->toBe(0)
        ->and(companyAccountingSettingCount($target))->toBe(0);
});

it('rolls back and reports an incomplete setup when a template setting points at an account the template does not own', function () {
    $handler = captureSetupLogRecords();

    $foreignAccount = accountOwnedBy(Company::factory()->create());

    templateWithAccounting(settingOverrides: ['accounts_accounts.income_account_id' => $foreignAccount->id]);

    $target = Company::factory()->create();

    $result = app(AccountingSetupService::class)->setUp($target);

    $warnings = setupWarningsFor($handler, $target);

    expect($result)->toBe(AccountingSetupResult::INCOMPLETE)
        ->and(companyJournalCount($target))->toBe(0)
        ->and(companyAccountingSettingCount($target))->toBe(0)
        ->and(end($warnings)['context']['missing'])->toContain('accounts_accounts.income_account_id');
});

it('reports an already set up company without copying anything again', function () {
    templateWithAccounting();

    $target = Company::factory()->create();

    Journal::factory()->sale()->company($target)->create();

    Journal::factory()->purchase()->company($target)->create();

    $result = app(AccountingSetupService::class)->setUp($target);

    expect($result)->toBe(AccountingSetupResult::ALREADY_SET_UP)
        ->and(companyJournalCount($target))->toBe(2)
        ->and(companyAccountingSettingCount($target))->toBe(0);
});

it('reports the company as already set up when the setup runs a second time', function () {
    templateWithAccounting();

    $target = Company::factory()->create();

    $service = app(AccountingSetupService::class);

    expect($service->setUp($target))->toBe(AccountingSetupResult::COMPLETED);

    $journalCount = companyJournalCount($target);

    $settingCount = companyAccountingSettingCount($target);

    expect($service->setUp($target))->toBe(AccountingSetupResult::ALREADY_SET_UP)
        ->and(companyJournalCount($target))->toBe($journalCount)
        ->and(companyAccountingSettingCount($target))->toBe($settingCount);
});

it('rolls back every copied record and rethrows when a copy step fails', function () {
    templateWithAccounting();

    $target = Company::factory()->create();

    $service = Mockery::mock(AccountingSetupService::class)
        ->makePartial()
        ->shouldAllowMockingProtectedMethods();

    $service->shouldReceive('copySettings')->andThrow(new RuntimeException('Copy failed.'));

    expect(fn () => $service->setUp($target))->toThrow(RuntimeException::class, 'Copy failed.')
        ->and(companyJournalCount($target))->toBe(0)
        ->and(DB::table('accounts_taxes')->where('company_id', $target->id)->exists())->toBeFalse()
        ->and(DB::table('accounts_account_companies')->where('company_id', $target->id)->exists())->toBeFalse()
        ->and(companyAccountingSettingCount($target))->toBe(0);
});
