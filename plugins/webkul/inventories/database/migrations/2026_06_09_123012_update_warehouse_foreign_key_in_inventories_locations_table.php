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
        Schema::table('inventories_locations', function (Blueprint $table) {
            $table->dropForeign(['warehouse_id']);

            $table->foreign('warehouse_id')
                ->references('id')
                ->on('inventories_warehouses')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories_locations', function (Blueprint $table) {
           $table->dropForeign(['warehouse_id']);

            $table->foreign('warehouse_id')
                ->references('id')
                ->on('inventories_warehouses')
                ->nullOnDelete();
        });
    }
};
