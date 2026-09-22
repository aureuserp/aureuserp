<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('sales_orders', 'price_list_id')) {
            return;
        }

        Schema::table('sales_orders', function (Blueprint $table) {
            $table->foreignId('price_list_id')
                ->nullable()
                ->comment('Price List')
                ->constrained('products_product_price_lists')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('sales_orders', 'price_list_id')) {
            return;
        }

        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropForeign(['price_list_id']);

            $table->dropColumn('price_list_id');
        });
    }
};
