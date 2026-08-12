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
        Schema::create('manufacturing_orders', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable()->index();
            $table->string('reference')->nullable()->index();
            $table->string('priority')->nullable()->default('0');
            $table->string('origin')->nullable();
            $table->string('state')->default('draft')->index();
            $table->string('reservation_state')->nullable();
            $table->string('consumption');
            $table->decimal('quantity', 15, 4)->default(1);
            $table->decimal('quantity_producing', 15, 4)->default(0);
            $table->decimal('product_uom_qty', 15, 4)->default(0);
            $table->timestamp('deadline_at')->nullable();
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('finished_at')->nullable();
            $table->boolean('is_planned')->default(0);
            $table->boolean('is_locked')->default(0);

            $table->foreignId('product_id')
                ->constrained('products_products')
                ->restrictOnDelete();

            $table->foreignId('uom_id')
                ->constrained('unit_of_measures')
                ->restrictOnDelete();

            $table->foreignId('producing_lot_id')
                ->nullable()
                ->constrained('inventories_lots')
                ->nullOnDelete();

            $table->foreignId('operation_type_id')
                ->constrained('inventories_operation_types')
                ->restrictOnDelete();

            $table->foreignId('source_location_id')
                ->constrained('inventories_locations')
                ->restrictOnDelete();

            $table->foreignId('destination_location_id')
                ->constrained('inventories_locations')
                ->restrictOnDelete();

            $table->foreignId('final_location_id')
                ->nullable()
                ->constrained('inventories_locations')
                ->nullOnDelete();

            $table->foreignId('production_location_id')
                ->nullable()
                ->constrained('inventories_locations')
                ->nullOnDelete();

            $table->foreignId('procurement_group_id')
                ->nullable()
                ->constrained('inventories_procurement_groups')
                ->nullOnDelete();

            $table->foreignId('bill_of_material_id')
                ->nullable()
                ->constrained('manufacturing_bills_of_materials')
                ->nullOnDelete();

            $table->foreignId('assigned_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('company_id')
                ->constrained('companies')
                ->restrictOnDelete();

            $table->foreignId('order_point_id')
                ->nullable()
                ->constrained('inventories_order_points')
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
        Schema::dropIfExists('manufacturing_orders');
    }
};
