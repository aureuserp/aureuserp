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
        Schema::table('products_products', function (Blueprint $table) {
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

            $table->string('image')->nullable()->comment('Image');
            $table->string('service_type')->nullable()->comment('Service Type');
            $table->string('sale_line_warn')->nullable()->comment('Sale Line Warning');
            $table->text('expense_policy')->nullable()->comment('Expense Policy');
            $table->text('invoice_policy')->nullable()->comment('Invoicing Policy');
            $table->boolean('sales_ok')->default(true)->comment('Can be Sold');
            $table->boolean('purchase_ok')->default(true)->comment('Can be Purchased');
            $table->string('sale_line_warn_msg')->nullable()->comment('Sale Line Warning Message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products_products', function (Blueprint $table) {
            $table->dropForeign(['property_account_income_id']);
            $table->dropForeign(['property_account_expense_id']);

            $table->dropColumn([
                'property_account_income_id',
                'property_account_expense_id',
                'image',
                'service_type',
                'sale_line_warn',
                'expense_policy',
                'invoice_policy',
                'sales_ok',
                'purchase_ok',
                'sale_line_warn_msg',
            ]);
        });
    }
};
