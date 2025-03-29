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
        if (! Schema::hasTable('purchases_order_operations')) {
            Schema::disableForeignKeyConstraints();
            Schema::create('purchases_order_operations', function (Blueprint $table) {
               $table->foreignId('purchase_order_id')
               ->constrained('purchases_orders')
                ->cascadeOnDelete();

                $table->foreignId('inventory_operation_id')
                    ->constrained('inventories_operations')
                    ->cascadeOnDelete();
            });
            Schema::enableForeignKeyConstraints();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases_order_operations');
    }
};
