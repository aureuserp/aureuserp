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
        Schema::create('manufacturing_bill_of_material_byproducts', function (Blueprint $table) {
            $table->id();
            $table->integer('sort')->nullable();
            $table->decimal('quantity', 15, 4)->default(1);
            $table->decimal('cost_share', 5, 2)->nullable();

            $table->foreignId('bill_of_material_id')
                ->nullable()
                ->constrained(table: 'manufacturing_bills_of_materials', indexName: 'mfg_bom_byprod_bom_fk')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained('products_products')
                ->restrictOnDelete();

            $table->foreignId('company_id')
                ->nullable()
                ->constrained('companies')
                ->nullOnDelete();

            $table->foreignId('uom_id')
                ->constrained('unit_of_measures')
                ->restrictOnDelete();

            $table->foreignId('operation_id')
                ->nullable()
                ->constrained('manufacturing_operations')
                ->nullOnDelete();

            $table->foreignId('creator_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacturing_bill_of_material_byproducts');
    }
};
