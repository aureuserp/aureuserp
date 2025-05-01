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
        Schema::create('accounts_accounts_move_reversal_move', function (Blueprint $table) {
            $table->foreignId('move_id')->comment('Move')->constrained('accounts_account_moves')->cascadeOnDelete();
            $table->foreignId('reversal_id')->comment('Reversal')->constrained('accounts_accounts_move_reversals')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts_accounts_move_reversal_move');
    }
};
