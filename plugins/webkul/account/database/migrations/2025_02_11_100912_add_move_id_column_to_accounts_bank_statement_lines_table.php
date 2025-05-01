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
        Schema::table('accounts_bank_statement_lines', function (Blueprint $table) {
            $table->foreignId('move_id')->after('partner_id')->nullable()->comment('Journal Entry')->constrained('accounts_account_moves')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::hasTable('accounts_bank_statement_lines', function (Blueprint $table) {
            $table->dropForeign(['move_id']);

            $table->dropColumn('move_id');
        });
    }
};
