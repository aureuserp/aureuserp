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
        Schema::table('accounts_account_payments', function (Blueprint $table) {
            $table->foreignId('move_id')->nullable()->comment('Journal Entry')->constrained('accounts_account_moves')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts_account_payments', function (Blueprint $table) {
            if (Schema::hasColumn('accounts_account_payments', 'move_id')) {
                $table->dropForeign(['move_id']);
                $table->dropColumn('move_id');
            }
        });
    }
};
