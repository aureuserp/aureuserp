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
            $table->foreignId('manufacture_pull_id')
                ->nullable()
                ->constrained('inventories_rules')
                ->nullOnDelete();

            $table->foreignId('manufacture_mto_pull_id')
                ->nullable()
                ->constrained('inventories_rules')
                ->nullOnDelete();

            $table->foreignId('pbm_mto_pull_id')
                ->nullable()
                ->constrained('inventories_rules')
                ->nullOnDelete();

            $table->foreignId('sam_rule_id')
                ->nullable()
                ->constrained('inventories_rules')
                ->nullOnDelete();

            $table->foreignId('manu_type_id')
                ->nullable()
                ->constrained('inventories_operation_types')
                ->nullOnDelete();

            $table->foreignId('pbm_type_id')
                ->nullable()
                ->constrained('inventories_operation_types')
                ->nullOnDelete();

            $table->foreignId('sam_type_id')
                ->nullable()
                ->constrained('inventories_operation_types')
                ->nullOnDelete();

            $table->foreignId('pbm_route_id')
                ->nullable()
                ->constrained('inventories_routes')
                ->restrictOnDelete();

            $table->foreignId('pbm_loc_id')
                ->nullable()
                ->constrained('inventories_locations')
                ->nullOnDelete();

            $table->foreignId('sam_loc_id')
                ->nullable()
                ->constrained('inventories_locations')
                ->nullOnDelete();

            $table->string('manufacture_steps')
                ->nullable()
                ->default('one_step');

            $table->boolean('manufacture_to_resupply')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories_warehouses', function (Blueprint $table) {
            foreach (['manufacture_pull_id', 'manufacture_mto_pull_id', 'pbm_mto_pull_id', 'sam_rule_id', 'manu_type_id', 'pbm_type_id', 'sam_type_id', 'pbm_route_id', 'pbm_loc_id', 'sam_loc_id'] as $column) {
                if (Schema::hasColumn('inventories_warehouses', $column)) {
                    $table->dropForeign([$column]);
                    $table->dropColumn($column);
                }
            }

            foreach (['manufacture_steps', 'manufacture_to_resupply'] as $column) {
                if (Schema::hasColumn('inventories_warehouses', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
