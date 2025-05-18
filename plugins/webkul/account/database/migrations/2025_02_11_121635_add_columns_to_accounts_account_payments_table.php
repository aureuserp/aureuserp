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
        Schema::table('accounts_account_move_lines', function (Blueprint $table) {
            $table->foreignId('full_reconcile_id')->nullable()->after('reconcile_id')->constrained('accounts_full_reconciles')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts_moves_lines', function (Blueprint $table) {
            //
        });
    }
};
