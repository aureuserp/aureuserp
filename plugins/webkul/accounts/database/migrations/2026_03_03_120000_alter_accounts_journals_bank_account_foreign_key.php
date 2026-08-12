<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        foreach ($this->foreignKeysOnBankAccountId() as $foreignKey) {
            Schema::table('accounts_journals', function (Blueprint $table) use ($foreignKey) {
                $table->dropForeign($foreignKey['name']);
            });
        }
    }

    private function hasForeignKeyTo(string $referencedTable): bool
    {
        foreach ($this->foreignKeysOnBankAccountId() as $foreignKey) {
            if ($foreignKey['foreign_table'] === $referencedTable) {
                return true;
            }
        }

        return false;
    }

    /**
     * Schema::getForeignKeys() is driver-agnostic (works on both MySQL and
     * PostgreSQL), unlike hand-rolled information_schema.KEY_COLUMN_USAGE
     * queries which relied on MySQL-only DATABASE().
     *
     * @return list<array{name: string|null, columns: list<string>, foreign_table: string}>
     */
    private function foreignKeysOnBankAccountId(): array
    {
        return array_values(array_filter(
            Schema::getForeignKeys('accounts_journals'),
            fn (array $foreignKey) => in_array('bank_account_id', $foreignKey['columns'], true)
        ));
    }
};
