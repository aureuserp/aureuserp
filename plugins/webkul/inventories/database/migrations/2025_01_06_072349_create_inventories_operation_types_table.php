<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Webkul\Inventory\Enums\CreateBackorder;
use Webkul\Inventory\Enums\MoveType;
use Webkul\Inventory\Enums\ReservationMethod;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventories_operation_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->integer('sort')->nullable();
            $table->string('sequence_code');
            $table->string('reservation_method')->default(ReservationMethod::AT_CONFIRM);
            $table->integer('reservation_days_before')->nullable()->default(0);
            $table->integer('reservation_days_before_priority')->nullable()->default(0);
            $table->string('product_label_format')->nullable();
            $table->string('lot_label_format')->nullable();
            $table->string('package_label_to_print')->nullable();
            $table->string('barcode')->nullable();
            $table->string('create_backorder')->default(CreateBackorder::ASK);
            $table->string('move_type')->nullable()->default(MoveType::DIRECT);
            $table->boolean('show_entire_packs')->nullable()->default(0);
            $table->boolean('use_create_lots')->nullable()->default(0);
            $table->boolean('use_existing_lots')->nullable()->default(0);
            $table->boolean('print_label')->nullable()->default(0);
            $table->boolean('show_operations')->nullable()->default(0);
            $table->boolean('auto_show_reception_report')->nullable()->default(0);
            $table->boolean('auto_print_delivery_slip')->nullable()->default(0);
            $table->boolean('auto_print_return_slip')->nullable()->default(0);
            $table->boolean('auto_print_product_labels')->nullable()->default(0);
            $table->boolean('auto_print_lot_labels')->nullable()->default(0);
            $table->boolean('auto_print_reception_report')->nullable()->default(0);
            $table->boolean('auto_print_reception_report_labels')->nullable()->default(0);
            $table->boolean('auto_print_packages')->nullable()->default(0);
            $table->boolean('auto_print_package_label')->nullable()->default(0);

            $table->foreignId('return_operation_type_id')
                ->nullable()
                ->constrained('inventories_operation_types')
                ->nullOnDelete();

            $table->foreignId('source_location_id')
                ->constrained('inventories_locations')
                ->restrictOnDelete();

            $table->foreignId('destination_location_id')
                ->constrained('inventories_locations')
                ->restrictOnDelete();

            $table->foreignId('warehouse_id')
                ->nullable()
                ->constrained('inventories_warehouses')
                ->cascadeOnDelete();

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
        Schema::dropIfExists('inventories_operation_types');
    }
};
