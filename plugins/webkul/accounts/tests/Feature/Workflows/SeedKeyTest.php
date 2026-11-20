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
    DB::table('accounts_journals')->where('key', 'journal.sale')->update(['name' => 'Ventes', 'code' => 'VTE']);

    DB::table('accounts_accounts')->where('key', 'account.current-assets')->update(['name' => 'Actif courant', 'code' => '411000']);

    (new AccountSeeder)->run();

    (new JournalSeeder)->run();

    expect(DB::table('accounts_journals')->where('key', 'journal.sale')->pluck('name')->all())->toContain('Ventes')
        ->and(DB::table('accounts_journals')->where('id', 1)->value('name'))->toBe('Ventes')
        ->and(DB::table('accounts_journals')->where('id', 1)->value('code'))->toBe('VTE')
        ->and(DB::table('accounts_accounts')->where('id', 1)->value('name'))->toBe('Actif courant')
        ->and(DB::table('accounts_accounts')->where('id', 1)->value('code'))->toBe('411000');
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

it('restores accounting keys from the seeded ids when names and codes are translated', function () {
    app(AccountingSetupService::class)->setUp(Company::factory()->create());

    $expected = [
        'accounts_journals'   => DB::table('accounts_journals')
            ->whereIn('key', ['journal.sale', 'journal.purchase', 'journal.miscellaneous', 'journal.exchange-difference', 'journal.bank', 'journal.cash'])
            ->pluck('key', 'id')
            ->all(),
        'accounts_accounts'   => DB::table('accounts_accounts')->where('key', 'like', 'account.%')->pluck('key', 'id')->all(),
        'accounts_tax_groups' => DB::table('accounts_tax_groups')->where('key', 'like', 'tax-group.%')->pluck('key', 'id')->all(),
    ];

    foreach ($expected as $table => $keys) {
        foreach (array_keys($keys) as $id) {
            DB::table($table)->where('id', $id)->update(['key' => null, 'name' => "Nom {$id}"]);
        }
    }

    DB::table('accounts_journals')->whereIn('id', array_keys($expected['accounts_journals']))->update(['code' => DB::raw("concat('L', code)")]);

    DB::table('accounts_accounts')->whereIn('id', array_keys($expected['accounts_accounts']))->update(['code' => DB::raw("concat('9', code)")]);

    (require __DIR__.'/../../../database/migrations/2026_11_06_100000_add_key_to_accounts_tables.php')->backfill();

    expect(count($expected['accounts_journals']))->toBeGreaterThanOrEqual(12)
        ->and(count($expected['accounts_accounts']))->toBeGreaterThanOrEqual(102)
        ->and(count($expected['accounts_tax_groups']))->toBeGreaterThanOrEqual(2);

    foreach ($expected as $table => $keys) {
        expect(DB::table($table)->whereIn('id', array_keys($keys))->pluck('key', 'id')->all())->toEqual($keys);
    }
});
