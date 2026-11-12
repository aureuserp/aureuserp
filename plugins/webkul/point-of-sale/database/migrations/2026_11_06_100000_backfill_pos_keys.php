<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('inventories_operation_types', 'key') || ! Schema::hasColumn('accounts_journals', 'key')) {
            return;
        }

        foreach (['pos_type_id' => 'operation-type.pos-orders', 'pos_return_type_id' => 'operation-type.pos-returns'] as $column => $seedKey) {
            DB::table('inventories_operation_types')
                ->whereIn('id', DB::table('inventories_warehouses')->whereNotNull($column)->select($column))
                ->whereNull('key')
                ->update(['key' => $seedKey]);
        }

        DB::table('accounts_journals')
            ->where('code', 'POSS')
            ->where('type', 'general')
            ->whereNull('key')
            ->orderBy('id')
            ->get(['id', 'company_id'])
            ->unique('company_id')
            ->each(fn ($journal) => DB::table('accounts_journals')->where('id', $journal->id)->update(['key' => 'journal.pos-terminal']));
    }

    public function down(): void {}
};
