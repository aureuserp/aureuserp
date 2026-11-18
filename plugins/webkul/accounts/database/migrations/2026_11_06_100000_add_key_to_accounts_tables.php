<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected array $journalTypes = [
        'sale'                => 'sale',
        'purchase'            => 'purchase',
        'miscellaneous'       => 'general',
        'exchange-difference' => 'general',
        'bank'                => 'bank',
        'cash'                => 'cash',
    ];

    protected array $accountTypes = [
        'current-assets'               => 'asset_current',
        'stock-valuation'              => 'asset_current',
        'stock-interim-received'       => 'asset_current',
        'stock-interim-delivered'      => 'asset_current',
        'cost-of-production'           => 'asset_current',
        'work-in-progress'             => 'asset_current',
        'account-receivable'           => 'asset_receivable',
        'products-to-receive'          => 'asset_current',
        'prepaid-expenses'             => 'asset_current',
        'tax-paid'                     => 'asset_current',
        'tax-receivable'               => 'asset_current',
        'prepayments'                  => 'asset_prepayments',
        'fixed-asset'                  => 'asset_fixed',
        'non-current-assets'           => 'asset_non_current',
        'current-liabilities'          => 'liability_current',
        'account-payable'              => 'liability_payable',
        'bills-to-receive'             => 'liability_current',
        'deferred-revenue'             => 'liability_current',
        'salary-payable'               => 'liability_current',
        'employee-payroll-taxes'       => 'liability_current',
        'employer-payroll-taxes'       => 'liability_current',
        'tax-received'                 => 'liability_current',
        'tax-payable'                  => 'liability_current',
        'non-current-liabilities'      => 'liability_non_current',
        'capital'                      => 'equity',
        'dividends'                    => 'equity',
        'product-sales'                => 'income',
        'foreign-exchange-gain'        => 'income',
        'cash-difference-gain'         => 'income',
        'cash-discount-loss'           => 'expense',
        'other-income'                 => 'income_other',
        'cost-of-goods-sold'           => 'expense_direct_cost',
        'expenses'                     => 'expense',
        'purchase-of-equipments'       => 'expense',
        'rent'                         => 'expense',
        'bank-fees'                    => 'expense',
        'salary-expenses'              => 'expense',
        'foreign-exchange-loss'        => 'expense',
        'cash-difference-loss'         => 'expense',
        'cash-discount-gain'           => 'income',
        'rd-expenses'                  => 'expense',
        'sales-expenses'               => 'expense',
        'account-receivable-pos'       => 'asset_receivable',
        'credit-card'                  => 'liability_credit_card',
        'bank'                         => 'asset_cash',
        'cash'                         => 'asset_cash',
        'bank-suspense-account'        => 'asset_current',
        'liquidity-transfer'           => 'asset_current',
        'outstanding-receipts'         => 'asset_current',
        'outstanding-payments'         => 'asset_current',
        'undistributed-profits-losses' => 'equity_unaffected',
    ];

    public function up(): void
    {
        foreach (['accounts_journals', 'accounts_tax_groups'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->string('key')->nullable()->index();

                $table->unique(['company_id', 'key']);
            });
        }

        Schema::table('accounts_accounts', function (Blueprint $table) {
            $table->string('key')->nullable()->index();
        });

        $this->backfill();
    }

    public function backfill(): void
    {
        $this->backfillJournals();

        $this->backfillAccounts();

        $this->backfillTaxGroups();
    }

    public function down(): void
    {
        foreach (['accounts_journals', 'accounts_tax_groups'] as $tableName) {
            if (Schema::hasColumn($tableName, 'key')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropUnique(['company_id', 'key']);

                    $table->dropIndex(['key']);

                    $table->dropColumn('key');
                });
            }
        }

        if (Schema::hasColumn('accounts_accounts', 'key')) {
            Schema::table('accounts_accounts', function (Blueprint $table) {
                $table->dropIndex(['key']);

                $table->dropColumn('key');
            });
        }
    }

    protected function backfillJournals(): void
    {
        foreach (array_keys($this->journalTypes) as $index => $key) {
            $type = $this->journalTypes[$key];

            $template = DB::table('accounts_journals')->where('id', $index + 1)->where('type', $type)->first();

            if ($template) {
                $this->assignPerCompany($this->rows('accounts_journals')->where('id', $template->id), 'accounts_journals', "journal.{$key}");

                $this->assignPerCompany(
                    $this->rows('accounts_journals')
                        ->where('company_id', '<>', $template->company_id)
                        ->where('type', $type)
                        ->where('code', $template->code),
                    'accounts_journals',
                    "journal.{$key}",
                );
            }

            if ($type !== 'general') {
                $this->assignPerCompany(
                    $this->rows('accounts_journals')
                        ->where('type', $type)
                        ->whereNotIn('company_id', DB::table('accounts_journals')
                            ->select('company_id')
                            ->where('type', $type)
                            ->groupBy('company_id')
                            ->havingRaw('count(*) > 1')),
                    'accounts_journals',
                    "journal.{$key}",
                );
            }

            $this->assignPerCompany(
                $this->rows('accounts_journals')
                    ->where('type', $type)
                    ->where('name', trans("accounts::seeders.journals.{$key}", [], 'en')),
                'accounts_journals',
                "journal.{$key}",
            );
        }
    }

    protected function backfillAccounts(): void
    {
        foreach (array_keys($this->accountTypes) as $index => $key) {
            $accountType = $this->accountTypes[$key];

            $template = DB::table('accounts_accounts')->where('id', $index + 1)->where('account_type', $accountType)->first();

            if ($template) {
                $this->assignPerCompany($this->rows('accounts_accounts')->where('accounts_accounts.id', $template->id), 'accounts_accounts', "account.{$key}");

                $this->assignPerCompany(
                    $this->rows('accounts_accounts')
                        ->where('accounts_accounts.account_type', $accountType)
                        ->where('accounts_accounts.code', $template->code),
                    'accounts_accounts',
                    "account.{$key}",
                );
            }

            $this->assignPerCompany(
                $this->rows('accounts_accounts')->where('accounts_accounts.name', trans("accounts::seeders.accounts.{$key}", [], 'en')),
                'accounts_accounts',
                "account.{$key}",
            );
        }
    }

    protected function backfillTaxGroups(): void
    {
        $template = DB::table('accounts_tax_groups')->where('id', 1)->first();

        if ($template) {
            $this->assignPerCompany($this->rows('accounts_tax_groups')->where('id', $template->id), 'accounts_tax_groups', 'tax-group.tax-15');

            $this->assignPerCompany(
                $this->rows('accounts_tax_groups')
                    ->where('company_id', '<>', $template->company_id)
                    ->where('name', $template->name),
                'accounts_tax_groups',
                'tax-group.tax-15',
            );
        }

        $this->assignPerCompany(
            $this->rows('accounts_tax_groups')->whereNotIn('company_id', DB::table('accounts_tax_groups')
                ->select('company_id')
                ->groupBy('company_id')
                ->havingRaw('count(*) > 1')),
            'accounts_tax_groups',
            'tax-group.tax-15',
        );

        foreach (array_keys(trans('accounts::seeders.tax-groups', [], 'en')) as $key) {
            $this->assignPerCompany(
                $this->rows('accounts_tax_groups')->where('name', trans("accounts::seeders.tax-groups.{$key}", [], 'en')),
                'accounts_tax_groups',
                "tax-group.{$key}",
            );
        }
    }

    protected function rows(string $table): Builder
    {
        if ($table !== 'accounts_accounts') {
            return DB::table($table)->select('id', 'company_id', 'key');
        }

        return DB::table('accounts_accounts')
            ->join('accounts_account_companies', 'accounts_account_companies.account_id', '=', 'accounts_accounts.id')
            ->select('accounts_accounts.id', 'accounts_account_companies.company_id', 'accounts_accounts.key');
    }

    protected function assignPerCompany(Builder $candidates, string $table, string $seedKey): void
    {
        $taken = $this->rows($table)->where("{$table}.key", $seedKey)->pluck('company_id');

        $ids = $candidates
            ->whereNull("{$table}.key")
            ->get()
            ->reject(fn ($row) => $taken->contains($row->company_id))
            ->groupBy('company_id')
            ->filter(fn ($rows) => $rows->count() === 1)
            ->map(fn ($rows) => $rows->first()->id)
            ->values();

        DB::table($table)->whereIn('id', $ids)->update(['key' => $seedKey]);
    }
};
