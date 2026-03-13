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
        Schema::table('sales_order_lines', function (Blueprint $table) {
            $table->dropForeign(['product_id']);

            $table->foreign('product_id')
                ->references('id')
                ->on('products_products')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_order_lines', function (Blueprint $table) {
            $table->dropForeign(['product_id']);

            $table->foreign('product_id')
                ->references('id')
                ->on('products_products')
                ->nullOnDelete();
        });
    }
};
