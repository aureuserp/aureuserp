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
        if (Schema::hasTable('purchases_order_lines')) {
            Schema::table('purchases_order_lines', function (Blueprint $table) {
                if (! Schema::hasColumn('purchases_order_lines', 'procurement_group_id')) {
                    $table->foreignId('procurement_group_id')
                        ->nullable()
                        ->constrained('inventories_procurement_groups')
                        ->nullOnDelete();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('purchases_order_lines')) {
            Schema::table('purchases_order_lines', function (Blueprint $table) {
                if (Schema::hasColumn('purchases_order_lines', 'procurement_group_id')) {
                    $table->dropForeign(['procurement_group_id']);
                    $table->dropColumn('procurement_group_id');
                }
            });
        }
    }
};
