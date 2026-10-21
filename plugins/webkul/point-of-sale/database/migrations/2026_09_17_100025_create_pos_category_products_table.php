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
        Schema::create('pos_category_products', function (Blueprint $table) {
            $table->foreignId('category_id')
                ->constrained('pos_categories')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained('products_products')
                ->cascadeOnDelete();

            $table->unique(['category_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_category_products');
    }
};
