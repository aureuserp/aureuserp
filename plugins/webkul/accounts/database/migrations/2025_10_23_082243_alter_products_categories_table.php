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
        Schema::table('products_categories', function (Blueprint $table) {
            $table->foreignId('property_account_income_id')
                ->nullable()
                ->comment('Income Account')
                ->constrained('accounts_accounts')
                ->nullOnDelete();

            $table->foreignId('property_account_expense_id')
                ->nullable()
                ->comment('Expense Account')
                ->constrained('accounts_accounts')
                ->nullOnDelete();

            $table->foreignId('property_account_down_payment_id')
                ->nullable()
                ->comment('Down Payment Account')
                ->constrained('accounts_accounts')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products_categories', function (Blueprint $table) {
            $table->dropForeign(['property_account_income_id']);
            $table->dropForeign(['property_account_expense_id']);
            $table->dropForeign(['property_account_down_payment_id']);

            $table->dropColumn([
                'property_account_income_id',
                'property_account_expense_id',
                'property_account_down_payment_id',
            ]);
        });
    }
};
