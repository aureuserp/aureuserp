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
        Schema::create('accounts_account_payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('journal_id')->nullable()->comment('Journal')->constrained('accounts_journals')->restrictOnDelete();
            $table->foreignId('company_id')->nullable()->comment('Company')->constrained('companies')->restrictOnDelete();
            $table->foreignId('partner_bank_id')->nullable()->comment('Partner Bank')->constrained('partners_bank_accounts')->nullOnDelete();

            $table->foreignId('paired_internal_transfer_payment_id')->nullable()->comment('Paired Internal Transfer Payment');
            $table->foreign('paired_internal_transfer_payment_id', 'fk_paired_transfer')
                ->references('id')->on('accounts_account_payments')
                ->nullOnDelete();

            $table->foreignId('payment_method_line_id')->nullable()->comment('Payment Method Line')->constrained('accounts_payment_method_lines')->nullOnDelete();
            $table->foreignId('payment_method_id')->nullable()->comment('Payment Method')->constrained('accounts_payment_methods')->nullOnDelete();
            $table->foreignId('currency_id')->nullable()->comment('Currency')->constrained('currencies')->nullOnDelete();
            $table->foreignId('partner_id')->nullable()->comment('Partner')->constrained('partners_partners')->nullOnDelete();
            $table->foreignId('outstanding_account_id')->nullable()->comment('Outstanding Account')->constrained('accounts_accounts')->nullOnDelete();
            $table->foreignId('destination_account_id')->nullable()->comment('Destination Account')->constrained('accounts_accounts')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->comment('Created By')->constrained('users')->nullOnDelete();
            $table->string('name')->nullable()->comment('Name');
            $table->string('state')->comment('State');
            $table->string('payment_type')->nullable()->comment('Payment Type');
            $table->string('partner_type')->nullable()->comment('Partner Type');
            $table->string('memo')->nullable()->comment('Memo');
            $table->string('payment_reference')->nullable()->comment('Payment Reference');
            $table->date('date')->nullable()->comment('Date');
            $table->decimal('amount', 15, 4)->nullable()->comment('Amount');
            $table->decimal('amount_company_currency_signed', 15, 4)->nullable()->comment('Amount in Company Currency Signed');
            $table->boolean('is_reconciled')->nullable()->comment('Is Reconciled');
            $table->boolean('is_matched')->nullable()->comment('Is Matched');
            $table->boolean('is_sent')->nullable()->comment('Is Sent');

            $table->foreignId('source_payment_id')->nullable()->comment('Source Payment');
            $table->foreign('source_payment_id', 'fk_source_payment')
                ->references('id')->on('accounts_account_payments')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts_account_payments');
    }
};
