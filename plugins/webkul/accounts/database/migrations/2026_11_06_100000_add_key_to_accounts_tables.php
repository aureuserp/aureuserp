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

        foreach ($this->journalTypes as $key => $type) {
            $this->backfillPerCompany(
                DB::table('accounts_journals')
                    ->select('id', 'company_id')
                    ->where('type', $type)
                    ->where('name', trans("accounts::seeders.journals.{$key}", [], 'en')),
                'accounts_journals',
                "journal.{$key}",
            );
        }

        foreach (array_keys(trans('accounts::seeders.tax-groups', [], 'en')) as $key) {
            $this->backfillPerCompany(
                DB::table('accounts_tax_groups')
                    ->select('id', 'company_id')
                    ->where('name', trans("accounts::seeders.tax-groups.{$key}", [], 'en')),
                'accounts_tax_groups',
                "tax-group.{$key}",
            );
        }

        foreach (trans('accounts::seeders.accounts', [], 'en') as $key => $name) {
            $this->backfillPerCompany(
                DB::table('accounts_accounts')
                    ->join('accounts_account_companies', 'accounts_account_companies.account_id', '=', 'accounts_accounts.id')
                    ->select('accounts_accounts.id', 'accounts_account_companies.company_id')
                    ->where('accounts_accounts.name', $name),
                'accounts_accounts',
                "account.{$key}",
            );
        }
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

    protected function backfillPerCompany(Builder $candidates, string $table, string $seedKey): void
    {
        $ids = $candidates
            ->whereNull("{$table}.key")
            ->get()
            ->groupBy('company_id')
            ->filter(fn ($rows) => $rows->count() === 1)
            ->map(fn ($rows) => $rows->first()->id)
            ->values();

        DB::table($table)->whereIn('id', $ids)->update(['key' => $seedKey]);
    }
};
