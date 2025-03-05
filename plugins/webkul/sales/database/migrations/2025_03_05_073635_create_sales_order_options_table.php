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
        Schema::create('sales_order_options', function (Blueprint $table) {
            $table->id();

            $table->integer('sort')->nullable()->default(0);

            $table->foreignId('order_id')->nullable()->comment('Sale Order Reference')->constrained('sales_orders')->cascadeOnDelete();
            $table->foreignId('product_id')->comment('Product')->constrained('products_products')->restrictOnDelete();
            $table->foreignId('line_id')->nullable()->comment('Sale Order Line Reference')->constrained('sales_order_lines')->nullOnDelete();
            $table->foreignId('uom_id')->nullable()->constrained('unit_of_measures')->nullOnDelete();
            $table->foreignId('creator_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('name')->comment('Description');
            $table->decimal('quantity', 15, 4)->nullable()->comment('Quantity');
            $table->decimal('price_unit', 15, 4)->nullable()->comment('Price Unit');
            $table->decimal('discount', 5, 2)->nullable()->comment('Discount (%)');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_order_options');
    }
};
