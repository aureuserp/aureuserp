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
        Schema::table('inventories_move_lines', function (Blueprint $table) {
            $table->foreignId('work_order_id')
                ->nullable()
                ->constrained('manufacturing_work_orders')
                ->nullOnDelete()
                ->noActionOnUpdate();

            $table->foreignId('order_id')
                ->nullable()
                ->constrained('manufacturing_orders')
                ->nullOnDelete()
                ->noActionOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories_move_lines', function (Blueprint $table) {
            if (Schema::hasColumn('inventories_move_lines', 'work_order_id')) {
                $table->dropForeign(['work_order_id']);
                $table->dropColumn('work_order_id');
            }

            if (Schema::hasColumn('inventories_move_lines', 'order_id')) {
                $table->dropForeign(['order_id']);
                $table->dropColumn('order_id');
            }
        });
    }
};
