<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products_product_company_accounts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->comment('Product')
                ->constrained('products_products')
                ->cascadeOnDelete();

            $table->foreignId('company_id')
                ->comment('Company')
                ->constrained('companies')
                ->cascadeOnDelete();

            $table->foreignId('property_account_income_id')
                ->nullable()
                ->comment('Income Account')
                ->constrained(table: 'accounts_accounts', indexName: 'product_company_accounts_income_foreign')
                ->nullOnDelete();

            $table->foreignId('property_account_expense_id')
                ->nullable()
                ->comment('Expense Account')
                ->constrained(table: 'accounts_accounts', indexName: 'product_company_accounts_expense_foreign')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique(['product_id', 'company_id'], 'product_company_accounts_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products_product_company_accounts');
    }
};
