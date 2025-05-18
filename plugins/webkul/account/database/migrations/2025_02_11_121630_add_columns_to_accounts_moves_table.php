<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('accounts_account_moves', function (Blueprint $table) {
            $table->foreignId('tax_cash_basis_reconcile_id')
                ->nullable()
                ->after('tax_cash_basis_origin_move_id')
                ->comment('Tax Cash Basis Entry of')
                ->constrained('accounts_partial_reconciles')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts_account_moves', function (Blueprint $table) {
            if (Schema::hasColumn('accounts_account_moves', 'tax_cash_basis_reconcile_id')) {
                $table->dropConstrainedForeignId('tax_cash_basis_reconcile_id');
            }
        });
    }
};
