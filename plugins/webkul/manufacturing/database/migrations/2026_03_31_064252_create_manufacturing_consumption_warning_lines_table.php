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
        Schema::create('manufacturing_consumption_warning_lines', function (Blueprint $table) {
            $table->id();
            $table->decimal('product_consumed_quantity', 15, 4)->nullable();
            $table->decimal('product_expected_quantity', 15, 4)->nullable();

            $table->foreignId('consumption_warning_id')
                ->constrained(table: 'manufacturing_consumption_warnings', indexName: 'mfg_cwl_warn_fk')
                ->cascadeOnDelete();

            $table->foreignId('manufacturing_order_id')
                ->constrained(table: 'manufacturing_orders', indexName: 'mfg_cwl_order_fk')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained('products_products')
                ->cascadeOnDelete();

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
        Schema::dropIfExists('manufacturing_consumption_warning_lines');
    }
};
