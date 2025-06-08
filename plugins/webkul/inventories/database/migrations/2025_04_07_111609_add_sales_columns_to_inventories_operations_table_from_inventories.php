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
        Schema::table('inventories_operations', function (Blueprint $table) {
            if (Schema::hasTable('sales_orders') && ! Schema::hasColumn('inventories_operations', 'sale_order_id')) {
                $table->foreignId('sale_order_id')
                    ->nullable()
                    ->constrained('sales_orders')
                    ->restrictOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories_operations', function (Blueprint $table) {
            if (Schema::hasColumn('inventories_operations', 'sale_order_id')) {
                $table->dropForeign(['sale_order_id']);
                $table->dropColumn('sale_order_id');
            }
        });
    }
};
