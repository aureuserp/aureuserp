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
        Schema::create('accounts_payment_registers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('currency_id')->nullable()->comment('Currency')->constrained('currencies')->nullOnDelete();
            $table->foreignId('journal_id')->nullable()->comment('Journal')->constrained('accounts_journals')->nullOnDelete();
            $table->foreignId('partner_bank_id')->nullable()->comment('Bank Account')->constrained('partners_bank_accounts')->nullOnDelete();
            $table->foreignId('custom_user_currency_id')->nullable()->comment('Custom User Currency')->constrained('currencies')->nullOnDelete();
            $table->foreignId('source_currency_id')->nullable()->comment('Source Currency')->constrained('currencies')->nullOnDelete();
            $table->foreignId('company_id')->nullable()->comment('Company')->constrained('companies')->nullOnDelete();
            $table->foreignId('partner_id')->nullable()->comment('Partner')->constrained('partners_partners')->nullOnDelete();
            $table->foreignId('payment_method_line_id')->nullable()->comment('Payment Method Line')->constrained('accounts_payment_method_lines')->nullOnDelete();
            $table->foreignId('writeoff_account_id')->nullable()->comment('Writeoff Account')->constrained('accounts_accounts')->nullOnDelete();
            $table->foreignId('creator_id')->nullable()->comment('Users')->constrained('users')->nullOnDelete();

            $table->string('communication')->nullable()->comment('Communication');
            $table->string('installments_mode')->nullable()->comment('Installments Mode');
            $table->string('payment_type')->nullable()->comment('Payment Type');
            $table->string('partner_type')->nullable()->comment('Partner Type');
            $table->string('payment_difference_handling')->nullable()->comment('Payment Difference Handling');
            $table->string('writeoff_label')->nullable()->comment('Writeoff Label');
            $table->date('payment_date')->nullable()->comment('Payment Date');
            $table->decimal('amount', 15, 4)->nullable()->comment('Amount');
            $table->decimal('custom_user_amount', 15, 4)->nullable()->comment('Custom User Amount');
            $table->decimal('source_amount', 15, 4)->nullable()->comment('Source Amount');
            $table->decimal('source_amount_currency', 15, 4)->nullable()->comment('Source Amount Currency');
            $table->boolean('group_payment')->nullable()->comment('Group Payment')->default(false);
            $table->boolean('can_group_payments')->nullable()->comment('Can Group Payments')->default(false);
            $table->integer('payment_token_id')->nullable()->comment('Payment Token');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts_payment_registers');
    }
};
