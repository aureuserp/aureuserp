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
        Schema::create('accounts_journal_accounts', function (Blueprint $table) {
            $table->foreignId('journal_id')->constrained('accounts_journals')->onDelete('cascade');
            $table->foreignId('account_id')->constrained('accounts_accounts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts_journal_accounts');
    }
};
