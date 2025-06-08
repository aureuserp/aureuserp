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
        Schema::create('sales_order_lines', function (Blueprint $table) {
            $table->id();
            $table->integer('sort')->nullable()->comment('Sort Order');
            $table->foreignId('order_id')->comment('Order Reference')->constrained('sales_orders')->cascadeOnDelete();
            $table->foreignId('company_id')->nullable()->comment('Company Reference')->constrained('companies')->nullOnDelete();
            $table->foreignId('currency_id')->nullable()->comment('Currency Reference')->constrained('currencies')->nullOnDelete();
            $table->foreignId('order_partner_id')->nullable()->comment('Order Partner Reference')->constrained('partners_partners')->nullOnDelete();
            $table->foreignId('salesman_id')->nullable()->comment('Salesman Reference')->constrained('users')->nullOnDelete();
            $table->foreignId('product_id')->nullable()->comment('Product Reference')->constrained('products_products')->nullOnDelete();
            $table->foreignId('product_uom_id')->nullable()->comment('Product UOM Reference')->constrained('unit_of_measures')->nullOnDelete();
            $table->foreignId('linked_sale_order_sale_id')->nullable()->comment('Linked Sale Order Sale Reference')->constrained('sales_order_lines')->nullOnDelete();
            $table->foreignId('product_packaging_id')->nullable()->comment('Product Packaging Reference')->constrained('products_packagings')->nullOnDelete();
            $table->foreignId('creator_id')->nullable()->comment('Creator Reference')->constrained('users')->nullOnDelete();
            $table->string('state')->nullable()->comment('Order State');
            $table->string('display_type')->nullable()->comment('Display Type');
            $table->string('virtual_id')->nullable()->comment('Virtual ID');
            $table->string('linked_virtual_id')->nullable()->comment('Linked Virtual ID');
            $table->string('qty_delivered_method')->nullable()->comment('Quantity Delivered Method');
            $table->string('invoice_status')->nullable()->comment('Invoice Status');
            $table->string('analytic_distribution')->nullable()->comment('Analytic Distribution Status');
            $table->string('name')->comment('Name');
            $table->decimal('product_uom_qty', 15, 4)->comment('Product UOM Quantity')->default(0);
            $table->decimal('product_qty', 15, 4)->comment('Product Quantity')->default(0);
            $table->decimal('price_unit', 15, 4)->comment('Price Unit')->default(0);
            $table->decimal('discount', 15, 4)->nullable()->comment('Discount')->default(0);
            $table->decimal('price_subtotal', 15, 4)->nullable()->comment('Price Subtotal')->default(0);
            $table->decimal('price_total', 15, 4)->nullable()->comment('Price Total')->default(0);
            $table->decimal('price_reduce_taxexcl', 15, 4)->nullable()->comment('Price Reduce Tax excl')->default(0);
            $table->decimal('price_reduce_taxinc', 15, 4)->nullable()->comment('Price Reduce Tax incl')->default(0);
            $table->decimal('purchase_price', 15, 4)->nullable()->comment('Cost')->default(0);
            $table->decimal('margin', 15, 4)->nullable()->comment('Margin')->default(0);
            $table->decimal('margin_percent', 15, 4)->nullable()->comment('Margin (%)')->default(0);
            $table->decimal('qty_delivered', 15, 4)->nullable()->comment('Delivery Quantity')->default(0);
            $table->decimal('qty_invoiced', 15, 4)->nullable()->comment('Invoiced Quantity')->default(0);
            $table->decimal('qty_to_invoice', 15, 4)->nullable()->comment('Quantity To Invoice')->default(0);
            $table->decimal('untaxed_amount_invoiced', 15, 4)->nullable()->comment('Untaxed Invoiced Amount')->default(0);
            $table->decimal('untaxed_amount_to_invoice', 15, 4)->nullable()->comment('Untaxed Amount To Invoice')->default(0);
            $table->boolean('is_downpayment')->default(false)->nullable()->comment('Is a down payment');
            $table->boolean('is_expense')->default(false)->nullable()->comment('Is expense');
            $table->timestamp('create_date')->nullable()->comment('Created on');
            $table->timestamp('write_date')->nullable()->comment('Last Updated on');
            $table->decimal('technical_price_unit', 15, 4)->nullable()->comment('Technical Price Unit')->default(0);
            $table->decimal('price_tax', 15, 4)->nullable()->comment('Total Tax')->default(0);
            $table->decimal('product_packaging_qty', 15, 4)->nullable()->comment('Packaging Quantity')->default(0);
            $table->decimal('customer_lead', 15, 4)->comment('Lead Time')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_order_lines');
    }
};
