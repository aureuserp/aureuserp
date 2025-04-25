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
        if (Schema::hasTable('sales_order_lines')) {
            Schema::table('sales_order_lines', function (Blueprint $table) {
                if (! Schema::hasColumn('sales_order_lines', 'warehouse_id')) {
                    $table->foreignId('warehouse_id')
                        ->nullable()
                        ->constrained('inventories_warehouses')
                        ->nullOnDelete();
                }

                if (! Schema::hasColumn('sales_order_lines', 'route_id')) {
                    $table->foreignId('route_id')
                        ->nullable()
                        ->constrained('inventories_routes')
                        ->restrictOnDelete();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('sales_order_lines')) {
            Schema::table('sales_order_lines', function (Blueprint $table) {
                if (Schema::hasColumn('sales_order_lines', 'warehouse_id')) {
                    $table->dropForeign(['warehouse_id']);
                    $table->dropColumn('warehouse_id');
                }

                if (Schema::hasColumn('sales_order_lines', 'route_id')) {
                    $table->dropForeign(['route_id']);
                    $table->dropColumn('route_id');
                }
            });
        }
    }
};
