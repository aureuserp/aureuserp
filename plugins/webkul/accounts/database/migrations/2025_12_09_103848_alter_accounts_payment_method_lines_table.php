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
        Schema::table('accounts_payment_method_lines', function (Blueprint $table) {
            $table->dropForeign('accounts_payment_method_lines_journal_id_foreign');

            $table->foreign('journal_id')
                ->references('id')
                ->on('accounts_journals')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts_payment_method_lines', function (Blueprint $table) {
            $table->dropForeign('accounts_payment_method_lines_journal_id_foreign');

            $table->foreign('journal_id')
                ->references('id')
                ->on('accounts_journals')
                ->restrictOnDelete();
        });
    }
};
