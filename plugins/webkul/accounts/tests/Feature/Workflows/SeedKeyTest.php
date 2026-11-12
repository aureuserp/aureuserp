<?php

use Illuminate\Support\Facades\DB;
use Webkul\Account\Database\Seeders\AccountSeeder;
use Webkul\Account\Database\Seeders\JournalSeeder;
use Webkul\Account\Services\AccountingSetupService;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/AccountHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('accounts');

    AccountHelper::actingAsAdmin();
});

it('gives every seeded journal a key unique within its company', function () {
    $keys = DB::table('accounts_journals')->whereBetween('id', [1, 6])->pluck('key');

    expect($keys->filter())->toHaveCount(6)
        ->and($keys->unique())->toHaveCount(6)
        ->and(DB::table('accounts_journals')->where('id', 1)->value('key'))->toBe('journal.sale');
});

it('gives seeded accounts and tax groups a key', function () {
    expect(DB::table('accounts_accounts')->where('id', 1)->value('key'))->toBe('account.current-assets')
        ->and(DB::table('accounts_accounts')->whereNotNull('key')->distinct()->count('key'))->toBeGreaterThanOrEqual(51)
        ->and(DB::table('accounts_tax_groups')->where('id', 1)->value('key'))->toBe('tax-group.tax-15');
});

it('keeps renamed journals and accounts when the seeders run again', function () {
    DB::table('accounts_journals')->where('key', 'journal.sale')->update(['name' => 'Ventes']);

    DB::table('accounts_accounts')->where('key', 'account.current-assets')->update(['name' => 'Actif courant']);

    (new AccountSeeder)->run();

    (new JournalSeeder)->run();

    expect(DB::table('accounts_journals')->where('key', 'journal.sale')->pluck('name')->all())->toContain('Ventes')
        ->and(DB::table('accounts_journals')->where('id', 1)->value('name'))->toBe('Ventes')
        ->and(DB::table('accounts_accounts')->where('id', 1)->value('name'))->toBe('Actif courant');
});

it('carries keys over when accounting is copied to a new company', function () {
    $company = Company::factory()->create();

    app(AccountingSetupService::class)->setUp($company);

    $journalKeys = DB::table('accounts_journals')->where('company_id', $company->id)->pluck('key');

    $accountKeys = DB::table('accounts_accounts')
        ->join('accounts_account_companies', 'accounts_account_companies.account_id', '=', 'accounts_accounts.id')
        ->where('accounts_account_companies.company_id', $company->id)
        ->pluck('accounts_accounts.key');

    expect($journalKeys)->toContain('journal.sale', 'journal.purchase')
        ->and($journalKeys->filter()->unique()->count())->toBe($journalKeys->filter()->count())
        ->and($accountKeys)->toContain('account.current-assets')
        ->and(DB::table('accounts_tax_groups')->where('company_id', $company->id)->pluck('key'))->toContain('tax-group.tax-15');
});
