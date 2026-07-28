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
        Schema::create('manufacturing_unbuild_orders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('state')->default('draft')->index();
            $table->decimal('quantity', 15, 4)->default(1);

            $table->foreignId('product_id')
                ->constrained('products_products')
                ->restrictOnDelete();

            $table->foreignId('company_id')
                ->constrained('companies')
                ->restrictOnDelete();

            $table->foreignId('uom_id')
                ->constrained('unit_of_measures')
                ->restrictOnDelete();

            $table->foreignId('bill_of_material_id')
                ->nullable()
                ->constrained(table: 'manufacturing_bills_of_materials', indexName: 'mfg_unbuild_bom_fk')
                ->nullOnDelete();

            $table->foreignId('manufacturing_order_id')
                ->nullable()
                ->constrained(table: 'manufacturing_orders', indexName: 'mfg_unbuild_order_fk')
                ->nullOnDelete();

            $table->foreignId('lot_id')
                ->nullable()
                ->constrained('inventories_lots')
                ->nullOnDelete();

            $table->foreignId('location_id')
                ->constrained('inventories_locations')
                ->restrictOnDelete();

            $table->foreignId('destination_location_id')
                ->constrained('inventories_locations')
                ->restrictOnDelete();

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
        Schema::dropIfExists('manufacturing_unbuild_orders');
    }
};
