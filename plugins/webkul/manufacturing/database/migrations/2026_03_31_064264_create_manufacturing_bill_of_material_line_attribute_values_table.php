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
        Schema::create('manufacturing_bill_of_material_line_attribute_values', function (Blueprint $table) {
            $table->foreignId('bill_of_material_line_id')
                ->constrained(table: 'manufacturing_bill_of_material_lines', indexName: 'mfg_bom_line_attr_line_fk')
                ->cascadeOnDelete();

            $table->foreignId('product_attribute_value_id')
                ->constrained(table: 'products_product_attribute_values', indexName: 'mfg_bom_line_attr_pav_fk')
                ->cascadeOnDelete();

            $table->unique(['bill_of_material_line_id', 'product_attribute_value_id'], 'mfg_bom_line_attr_uq');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacturing_bill_of_material_line_attribute_values');
    }
};
