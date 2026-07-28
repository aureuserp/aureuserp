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
        Schema::create('manufacturing_work_orders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('sort')->nullable();
            $table->string('barcode')->nullable()->index();
            $table->string('production_availability')->nullable();
            $table->string('state')->default('pending')->index();
            $table->decimal('quantity_produced', 15, 4)->nullable();
            $table->decimal('expected_duration', 15, 4)->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->decimal('duration', 15, 4)->nullable();
            $table->decimal('duration_per_unit', 15, 4)->nullable();
            $table->integer('duration_percent')->nullable();
            $table->decimal('costs_per_hour', 15, 4)->nullable();

            $table->foreignId('work_center_id')
                ->constrained('manufacturing_work_centers')
                ->restrictOnDelete();

            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products_products')
                ->nullOnDelete();

            $table->foreignId('uom_id')
                ->constrained('unit_of_measures')
                ->restrictOnDelete();

            $table->foreignId('manufacturing_order_id')
                ->constrained('manufacturing_orders')
                ->restrictOnDelete();

            $table->foreignId('calendar_leave_id')
                ->nullable()
                ->constrained('calendar_leaves')
                ->nullOnDelete();

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
        Schema::dropIfExists('manufacturing_work_orders');
    }
};
