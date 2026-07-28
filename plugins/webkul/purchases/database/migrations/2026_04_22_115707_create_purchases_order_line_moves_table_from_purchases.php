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
        if (! Schema::hasTable('purchases_order_line_moves') && Schema::hasTable('inventories_moves')) {
            Schema::create('purchases_order_line_moves', function (Blueprint $table) {
                $table->foreignId('purchase_order_line_id')
                    ->constrained('purchases_order_lines')
                    ->cascadeOnDelete();

                $table->foreignId('inventory_move_id')
                    ->constrained('inventories_moves')
                    ->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases_order_line_moves');
    }
};
