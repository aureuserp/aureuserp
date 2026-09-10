<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected array $prefixes = [
        'bank' => '1014',
        'cash' => '1015',
    ];

    public function up(): void
    {
        if (! Schema::hasTable('accounts_journals') || ! Schema::hasTable('accounts_accounts')) {
            return;
        }

        $journals = DB::table('accounts_journals')
            ->whereIn('type', array_keys($this->prefixes))
            ->get(['id', 'type', 'company_id', 'default_account_id']);

        foreach ($journals as $journal) {
            $accountId = $this->liquidityAccount($journal->company_id, $this->prefixes[$journal->type]);

            if ($accountId !== null && $journal->default_account_id != $accountId) {
                DB::table('accounts_journals')
                    ->where('id', $journal->id)
                    ->update(['default_account_id' => $accountId]);
            }
        }
    }

    public function down(): void {}

    protected function liquidityAccount(?int $companyId, string $prefix): ?int
    {
        $linkedIds = DB::table('accounts_account_companies')
            ->where('company_id', $companyId)
            ->pluck('account_id');

        $linked = DB::table('accounts_accounts')
            ->whereIn('id', $linkedIds)
            ->where('code', 'like', $prefix.'%')
            ->where('account_type', 'asset_cash')
            ->orderBy('code')
            ->value('id');

        if ($linked !== null) {
            return $linked;
        }

        $anyLinkedId = DB::table('accounts_account_companies')->pluck('account_id');

        return DB::table('accounts_accounts')
            ->where('code', 'like', $prefix.'%')
            ->where('account_type', 'asset_cash')
            ->whereNotIn('id', $anyLinkedId)
            ->orderBy('code')
            ->value('id');
    }
};
