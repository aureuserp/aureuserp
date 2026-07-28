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
        Schema::create('manufacturing_order_backorder_lines', function (Blueprint $table) {
            $table->id();
            $table->boolean('to_backorder')->nullable();

            $table->foreignId('order_backorder_id')
                ->constrained(table: 'manufacturing_order_backorders', indexName: 'mfg_obl_backorder_fk')
                ->cascadeOnDelete();

            $table->foreignId('manufacturing_order_id')
                ->constrained(table: 'manufacturing_orders', indexName: 'mfg_obl_mo_fk')
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
        Schema::dropIfExists('manufacturing_order_backorder_lines');
    }
};
