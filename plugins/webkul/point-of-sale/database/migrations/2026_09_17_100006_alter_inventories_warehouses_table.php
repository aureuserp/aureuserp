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
        Schema::table('inventories_warehouses', function (Blueprint $table) {
            $table->foreignId('pos_type_id')
                ->nullable()
                ->constrained('inventories_operation_types')
                ->nullOnDelete();

            $table->foreignId('pos_return_type_id')
                ->nullable()
                ->constrained('inventories_operation_types')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories_warehouses', function (Blueprint $table) {
            foreach (['pos_type_id', 'pos_return_type_id'] as $column) {
                if (Schema::hasColumn('inventories_warehouses', $column)) {
                    $table->dropForeign([$column]);

                    $table->dropColumn($column);
                }
            }
        });
    }
};
