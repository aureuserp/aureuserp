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
        Schema::create('accounts_account_account_tags', function (Blueprint $table) {
            $table->foreignId('account_id')->constrained('accounts_accounts')->cascadeOnDelete();
            $table->foreignId('account_tag_id')->constrained('accounts_account_tags')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts_account_account_tags');
    }
};
