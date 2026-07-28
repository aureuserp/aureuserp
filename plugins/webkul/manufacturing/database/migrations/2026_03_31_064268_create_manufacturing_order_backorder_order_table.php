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
        Schema::create('manufacturing_order_backorder_order', function (Blueprint $table) {
            $table->foreignId('order_backorder_id')
                ->constrained(table: 'manufacturing_order_backorders', indexName: 'mfg_obo_backorder_fk')
                ->cascadeOnDelete();

            $table->foreignId('manufacturing_order_id')
                ->constrained(table: 'manufacturing_orders', indexName: 'mfg_obo_order_fk')
                ->cascadeOnDelete();

            $table->unique(['order_backorder_id', 'manufacturing_order_id'], 'mfg_obo_uq');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacturing_order_backorder_order');
    }
};
