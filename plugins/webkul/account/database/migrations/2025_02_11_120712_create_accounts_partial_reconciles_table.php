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
        Schema::create('accounts_partial_reconciles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('debit_move_id')->nullable()->comment('Debit move')->constrained('accounts_account_moves')->restrictOnDelete();
            $table->foreignId('credit_move_id')->nullable()->comment('Credit move')->constrained('accounts_account_moves')->restrictOnDelete();
            $table->foreignId('full_reconcile_id')->nullable()->comment('Full Reconcile')->constrained('accounts_full_reconciles')->nullOnDelete();
            $table->foreignId('exchange_move_id')->nullable()->comment('Exchange Move')->constrained('accounts_account_moves')->nullOnDelete();
            $table->foreignId('debit_currency_id')->nullable()->comment('Debit Currency')->constrained('currencies')->nullOnDelete();
            $table->foreignId('credit_currency_id')->nullable()->comment('Credit Currency')->constrained('currencies')->nullOnDelete();
            $table->foreignId('company_id')->nullable()->comment('Company')->constrained('companies')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->comment('Created By')->constrained('users')->nullOnDelete();
            $table->date('max_date')->nullable()->comment('Max Date');
            $table->decimal('amount', 15, 4)->nullable()->comment('Amount');
            $table->decimal('debit_amount_currency', 15, 4)->nullable()->comment('Debit Amount Currency');
            $table->decimal('credit_amount_currency', 15, 4)->nullable()->comment('Credit Amount Currency');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts_partial_reconciles');
    }
};
