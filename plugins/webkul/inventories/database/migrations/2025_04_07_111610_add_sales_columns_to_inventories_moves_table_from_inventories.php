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
        Schema::table('inventories_moves', function (Blueprint $table) {
            if (Schema::hasTable('sales_order_lines') && ! Schema::hasColumn('inventories_moves', 'sale_order_line_id')) {
                $table->foreignId('sale_order_line_id')
                    ->nullable()
                    ->constrained('sales_order_lines')
                    ->restrictOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories_moves', function (Blueprint $table) {
            if (Schema::hasColumn('inventories_moves', 'sale_order_line_id')) {
                $table->dropForeign(['sale_order_line_id']);
                $table->dropColumn('sale_order_line_id');
            }
        });
    }
};
