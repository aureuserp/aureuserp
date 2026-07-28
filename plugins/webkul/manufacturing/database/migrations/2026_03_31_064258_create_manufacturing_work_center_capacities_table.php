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
        Schema::create('manufacturing_work_center_capacities', function (Blueprint $table) {
            $table->id();
            $table->decimal('capacity', 15, 4)->nullable();
            $table->decimal('time_start', 15, 4)->nullable();
            $table->decimal('time_stop', 15, 4)->nullable();

            $table->foreignId('work_center_id')
                ->constrained('manufacturing_work_centers')
                ->restrictOnDelete();

            $table->foreignId('product_id')
                ->constrained('products_products')
                ->restrictOnDelete();

            $table->foreignId('creator_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique(['work_center_id', 'product_id'], 'mfg_wc_caps_wc_prod_uq');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacturing_work_center_capacities');
    }
};
