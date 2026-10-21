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
        Schema::create('pos_order_line_attribute_values', function (Blueprint $table) {
            $table->foreignId('order_line_id')
                ->constrained('pos_order_lines')
                ->cascadeOnDelete();

            $table->foreignId('attribute_value_id')
                ->constrained('products_product_attribute_values')
                ->cascadeOnDelete();

            $table->unique(['order_line_id', 'attribute_value_id'], 'pos_order_line_attribute_values_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_order_line_attribute_values');
    }
};
