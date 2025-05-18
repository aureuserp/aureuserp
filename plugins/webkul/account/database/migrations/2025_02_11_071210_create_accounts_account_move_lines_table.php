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
        Schema::create('accounts_account_move_lines', function (Blueprint $table) {
            $table->id();
            $table->integer('sort')->nullable();
            $table->foreignId('move_id')->comment('Journal Entry')->constrained('accounts_account_moves')->cascadeOnDelete();
            $table->foreignId('journal_id')->nullable()->comment('Journal')->constrained('accounts_journals')->nullOnDelete();
            $table->foreignId('company_id')->nullable()->comment('Company')->constrained('companies')->nullOnDelete();
            $table->foreignId('company_currency_id')->nullable()->comment('Company Currency')->constrained('currencies')->nullOnDelete();
            $table->foreignId('reconcile_id')->nullable()->constrained('accounts_reconciles')->nullOnDelete();
            $table->foreignId('payment_id')->nullable()->comment('Payment')->constrained('accounts_account_payments')->nullOnDelete();
            $table->foreignId('tax_repartition_line_id')->nullable()->constrained('accounts_tax_partition_lines')->restrictOnDelete();
            $table->foreignId('account_id')->nullable()->comment('Account')->constrained('accounts_accounts')->cascadeOnDelete();
            $table->foreignId('currency_id')->comment('Currency')->constrained('currencies')->restrictOnDelete();
            $table->foreignId('partner_id')->comment('Partner')->nullable()->constrained('partners_partners')->restrictOnDelete();
            $table->foreignId('group_tax_id')->comment('Originator Group of Taxes')->nullable()->constrained('accounts_taxes')->nullOnDelete();
            $table->foreignId('tax_line_id')->comment('Originator Tax')->nullable()->constrained('accounts_taxes')->restrictOnDelete();
            $table->foreignId('tax_group_id')->comment('Originator tax group')->nullable()->constrained('accounts_tax_groups')->nullOnDelete();
            $table->foreignId('statement_id')->nullable()->constrained('accounts_bank_statements')->nullOnDelete();
            $table->foreignId('statement_line_id')->nullable()->constrained('accounts_bank_statement_lines')->nullOnDelete();

            $table->foreignId('product_id')->comment('Product')->nullable()->constrained('products_products')->restrictOnDelete();
            $table->foreignId('uom_id')->comment('Unit of Measure')->nullable()->constrained('unit_of_measures')->restrictOnDelete();
            $table->foreignId('created_by')->nullable()->comment('Created By')->constrained('users')->nullOnDelete();

            $table->string('move_name')->comment('Number')->nullable();
            $table->string('parent_state')->comment('Status')->nullable();
            $table->string('reference')->comment('Reference')->nullable();
            $table->string('name')->comment('Label')->nullable();
            $table->string('matching_number')->comment('Matching #')->nullable();
            $table->string('display_type')->comment('Display Type')->nullable();

            $table->date('date')->comment('Date')->nullable();
            $table->date('invoice_date')->comment('Invoice/Bill Date')->nullable();
            $table->date('date_maturity')->comment('Due Date')->nullable();
            $table->date('discount_date')->comment('Discount Date')->nullable();

            $table->jsonb('analytic_distribution')->comment('Analytic Distribution')->nullable();
            $table->decimal('debit', 15, 4)->comment('Debit')->nullable();
            $table->decimal('credit', 15, 4)->comment('Credit')->nullable();
            $table->decimal('balance', 15, 4)->comment('Balance')->nullable();
            $table->decimal('amount_currency', 15, 4)->comment('Amount in Currency')->nullable();
            $table->decimal('tax_base_amount', 15, 4)->comment('Base Amount')->nullable();
            $table->decimal('amount_residual', 15, 4)->comment('Residual Amount')->nullable();
            $table->decimal('amount_residual_currency', 15, 4)->comment('Residual Amount in Currency')->nullable();
            $table->decimal('quantity', 15, 4)->nullable()->comment('Quantity');
            $table->decimal('price_unit', 15, 4)->nullable()->comment('Price Unit');
            $table->decimal('price_subtotal', 15, 4)->nullable()->comment('Subtotal');
            $table->decimal('price_total', 15, 4)->nullable()->comment('Total');
            $table->decimal('discount', 5, 2)->nullable()->comment('Discount (%)');
            $table->decimal('discount_amount_currency', 15, 4)->nullable()->comment('Discount Amount in Currency');
            $table->decimal('discount_balance', 15, 4)->nullable()->comment('Discount Balance');

            $table->boolean('is_imported')->nullable()->comment('Imported');
            $table->boolean('tax_tag_invert')->nullable()->comment('Inverted Tax Tag');
            $table->boolean('reconciled')->nullable()->comment('Reconciled');
            $table->boolean('is_downpayment')->nullable()->comment('Down Payment');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts_account_move_lines');
    }
};
