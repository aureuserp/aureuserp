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
        Schema::create('accounts_accounts_move_payment', function (Blueprint $table) {
            $table->foreignId('invoice_id')->comment('Invoice')->constrained('accounts_account_moves')->cascadeOnDelete();
            $table->foreignId('payment_id')->comment('Payment')->constrained('accounts_account_payments')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts_accounts_move_payment');
    }
};
