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
        Schema::create('accounts_account_taxes', function (Blueprint $table) {
            $table->foreignId('account_id')->constrained('accounts_accounts')->cascadeOnDelete();
            $table->foreignId('tax_id')->constrained('accounts_taxes')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts_account_taxes');
    }
};
