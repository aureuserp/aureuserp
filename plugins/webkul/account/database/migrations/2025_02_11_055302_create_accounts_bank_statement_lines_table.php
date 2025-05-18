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
        Schema::create('accounts_bank_statement_lines', function (Blueprint $table) {
            $table->id();

            $table->integer('sort')->nullable()->comment('Sort Order');

            $table->foreignId('journal_id')->nullable()->comment('Journal')->constrained('accounts_journals')->nullOnDelete();
            $table->foreignId('company_id')->nullable()->comment('Company')->constrained('companies')->nullOnDelete();
            $table->foreignId('statement_id')->nullable()->comment('Bank Statement')->constrained('accounts_bank_statements')->cascadeOnDelete();
            $table->foreignId('partner_id')->nullable()->comment('Partner')->constrained('partners_partners')->nullOnDelete();
            $table->foreignId('currency_id')->nullable()->comment('Currency')->constrained('currencies')->nullOnDelete();
            $table->foreignId('foreign_currency_id')->nullable()->comment('Foreign Currency')->constrained('currencies')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->comment('Created By')->constrained('users')->nullOnDelete();

            $table->string('account_number')->nullable()->comment('Account Number');
            $table->string('partner_name')->nullable()->comment('Partner Name');
            $table->string('transaction_type')->nullable()->comment('Transaction Type');
            $table->string('payment_reference')->nullable()->comment('Payment Reference');
            $table->string('internal_index')->nullable()->comment('Internal Index');
            $table->json('transaction_details')->nullable()->comment('Transaction Details');
            $table->decimal('amount', 15, 4)->nullable()->comment('Amount');
            $table->decimal('amount_currency', 15, 4)->nullable()->comment('Amount Currency');
            $table->boolean('is_reconciled')->default(0)->comment('Is Reconciled');
            $table->decimal('amount_residual', 15, 4)->nullable()->comment('Amount Residual');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts_bank_statement_lines');
    }
};
