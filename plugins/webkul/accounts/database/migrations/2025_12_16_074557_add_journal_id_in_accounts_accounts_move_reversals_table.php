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
        Schema::table('accounts_accounts_move_reversals', function (Blueprint $table) {
            $table->foreignId('journal_id')
                ->nullable()
                ->constrained('accounts_journals')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts_accounts_move_reversals', function (Blueprint $table) {
            $table->dropForeign(['journal_id']);
            $table->dropColumn('journal_id');
        });
    }
};
