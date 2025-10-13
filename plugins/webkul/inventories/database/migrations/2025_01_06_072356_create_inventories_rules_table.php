<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Webkul\Inventory\Enums\ProcureMethod;
use Webkul\Inventory\Enums\RuleAuto;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventories_rules', function (Blueprint $table) {
            $table->id();
            $table->integer('sort')->nullable();
            $table->string('name');
            $table->integer('route_sort')->nullable()->default(0);
            $table->integer('delay')->nullable()->default(0);
            $table->string('group_propagation_option')->nullable();
            $table->string('action')->index();
            $table->string('procure_method')->default(ProcureMethod::MAKE_TO_STOCK);
            $table->string('auto')->default(RuleAuto::MANUAL);
            $table->string('push_domain')->nullable();
            $table->boolean('location_dest_from_rule')->nullable()->default(0);
            $table->boolean('propagate_cancel')->nullable()->default(0);
            $table->boolean('propagate_carrier')->nullable()->default(0);

            $table->foreignId('source_location_id')
                ->nullable()
                ->constrained('inventories_locations')
                ->nullOnDelete();

            $table->foreignId('destination_location_id')
                ->constrained('inventories_locations')
                ->restrictOnDelete();

            $table->foreignId('route_id')
                ->constrained('inventories_routes')
                ->cascadeOnDelete();

            $table->foreignId('operation_type_id')
                ->nullable()
                ->constrained('inventories_operation_types')
                ->nullOnDelete();

            $table->foreignId('partner_address_id')
                ->nullable()
                ->constrained('partners_partners')
                ->nullOnDelete();

            $table->foreignId('warehouse_id')
                ->nullable()
                ->constrained('inventories_warehouses')
                ->nullOnDelete();

            $table->foreignId('propagate_warehouse_id')
                ->nullable()
                ->constrained('inventories_warehouses')
                ->nullOnDelete();

            $table->foreignId('company_id')
                ->constrained('companies')
                ->restrictOnDelete();

            $table->foreignId('creator_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories_rules');
    }
};
