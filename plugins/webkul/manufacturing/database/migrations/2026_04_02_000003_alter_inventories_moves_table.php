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
            $table->foreignId('created_order_id')
                ->nullable()
                ->constrained('manufacturing_orders')
                ->nullOnDelete()
                ->noActionOnUpdate();

            $table->foreignId('order_id')
                ->nullable()
                ->constrained('manufacturing_orders')
                ->nullOnDelete()
                ->noActionOnUpdate();

            $table->foreignId('raw_material_order_id')
                ->nullable()
                ->constrained('manufacturing_orders')
                ->nullOnDelete()
                ->noActionOnUpdate();

            $table->foreignId('unbuild_order_id')
                ->nullable()
                ->constrained('manufacturing_unbuild_orders')
                ->nullOnDelete()
                ->noActionOnUpdate();

            $table->foreignId('consume_unbuild_order_id')
                ->nullable()
                ->constrained('manufacturing_unbuild_orders')
                ->nullOnDelete()
                ->noActionOnUpdate();

            $table->foreignId('mo_operation_id')
                ->nullable()
                ->constrained('manufacturing_operations')
                ->nullOnDelete()
                ->noActionOnUpdate();

            $table->foreignId('work_order_id')
                ->nullable()
                ->constrained('manufacturing_work_orders')
                ->nullOnDelete()
                ->noActionOnUpdate();

            $table->foreignId('bom_line_id')
                ->nullable()
                ->constrained('manufacturing_bill_of_material_lines')
                ->nullOnDelete()
                ->noActionOnUpdate();

            $table->foreignId('byproduct_id')
                ->nullable()
                ->constrained('manufacturing_bill_of_material_byproducts')
                ->nullOnDelete()
                ->noActionOnUpdate();

            $table->foreignId('order_finished_lot_id')
                ->nullable()
                ->constrained('inventories_lots')
                ->nullOnDelete()
                ->noActionOnUpdate();

            $table->decimal('cost_share', 15, 4)
                ->nullable();

            $table->boolean('manual_consumption')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories_moves', function (Blueprint $table) {
            foreach (['created_order_id', 'order_id', 'raw_material_order_id', 'unbuild_order_id', 'consume_unbuild_order_id', 'mo_operation_id', 'work_order_id', 'bom_line_id', 'byproduct_id', 'order_finished_lot_id'] as $column) {
                if (Schema::hasColumn('inventories_moves', $column)) {
                    $table->dropForeign([$column]);
                    $table->dropColumn($column);
                }
            }

            foreach (['cost_share', 'manual_consumption'] as $column) {
                if (Schema::hasColumn('inventories_moves', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
