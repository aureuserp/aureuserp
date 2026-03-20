<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->repointForeignKey('partners_bank_accounts');
    }

    public function down(): void
    {
        $this->repointForeignKey('banks');
    }

    private function repointForeignKey(string $referencedTable): void
    {
        $this->dropForeignKeysOnBankAccountId();

        DB::table('accounts_journals')
            ->whereNotNull('bank_account_id')
            ->whereNotExists(function ($query) use ($referencedTable) {
                $query->selectRaw('1')
                    ->from($referencedTable)
                    ->whereColumn("{$referencedTable}.id", 'accounts_journals.bank_account_id');
            })
            ->update(['bank_account_id' => null]);

        if ($this->hasForeignKeyTo($referencedTable)) {
            return;
        }

        Schema::table('accounts_journals', function (Blueprint $table) use ($referencedTable) {
            $table->foreign('bank_account_id')
                ->references('id')
                ->on($referencedTable)
                ->restrictOnDelete();
        });
    }

    private function dropForeignKeysOnBankAccountId(): void
    {
        $constraintNames = $this->getForeignConstraintNames();

        foreach ($constraintNames as $constraintName) {
            DB::statement(sprintf(
                'ALTER TABLE `accounts_journals` DROP FOREIGN KEY `%s`',
                str_replace('`', '``', (string) $constraintName)
            ));
        }
    }

    private function hasForeignKeyTo(string $referencedTable): bool
    {
        return DB::table('information_schema.KEY_COLUMN_USAGE')
            ->where('TABLE_SCHEMA', DB::raw('DATABASE()'))
            ->where('TABLE_NAME', 'accounts_journals')
            ->where('COLUMN_NAME', 'bank_account_id')
            ->where('REFERENCED_TABLE_NAME', $referencedTable)
            ->exists();
    }

    private function getForeignConstraintNames(): Collection
    {
        return DB::table('information_schema.KEY_COLUMN_USAGE')
            ->select('CONSTRAINT_NAME')
            ->where('TABLE_SCHEMA', DB::raw('DATABASE()'))
            ->where('TABLE_NAME', 'accounts_journals')
            ->where('COLUMN_NAME', 'bank_account_id')
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->pluck('CONSTRAINT_NAME');
    }
};
