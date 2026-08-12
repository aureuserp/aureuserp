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
        Schema::create('manufacturing_operation_attribute_values', function (Blueprint $table) {
            $table->foreignId('operation_id')
                ->constrained(table: 'manufacturing_operations', indexName: 'mfg_op_attr_op_fk')
                ->cascadeOnDelete();

            $table->foreignId('product_attribute_value_id')
                ->constrained(table: 'products_product_attribute_values', indexName: 'mfg_op_attr_pav_fk')
                ->cascadeOnDelete();

            $table->unique(['operation_id', 'product_attribute_value_id'], 'mfg_op_attr_uq');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacturing_operation_attribute_values');
    }
};
