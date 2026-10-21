<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Webkul\PointOfSale\Enums\PriceType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pos_order_lines', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name')->nullable();
            $table->string('full_product_name')->nullable();
            $table->integer('sort')->nullable();
            $table->decimal('qty', 15, 4)->default(0);
            $table->decimal('price_unit', 15, 4)->default(0);
            $table->decimal('price_extra', 15, 4)->default(0);
            $table->string('price_type')->default(PriceType::ORIGINAL);
            $table->decimal('discount', 5, 2)->default(0);
            $table->decimal('price_subtotal', 15, 4)->default(0);
            $table->decimal('price_subtotal_incl', 15, 4)->default(0);
            $table->decimal('price_tax', 15, 4)->default(0);
            $table->decimal('unit_cost', 15, 4)->default(0);
            $table->decimal('total_cost', 15, 4)->default(0);
            $table->decimal('margin', 15, 4)->default(0);
            $table->decimal('margin_percent', 15, 4)->default(0);
            $table->decimal('refunded_qty', 15, 4)->default(0);
            $table->text('customer_note')->nullable();
            $table->text('note')->nullable();
            $table->boolean('is_cost_computed')->default(0);
            $table->boolean('is_edited')->default(0);
            $table->boolean('is_skipped_in_preparation')->default(0);

            $table->foreignId('order_id')
                ->constrained('pos_orders')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained('products_products')
                ->restrictOnDelete();

            $table->foreignId('uom_id')
                ->nullable()
                ->constrained('unit_of_measures')
                ->nullOnDelete();

            $table->foreignId('price_list_id')
                ->nullable()
                ->constrained('products_product_price_lists')
                ->nullOnDelete();

            $table->foreignId('refunded_order_line_id')
                ->nullable()
                ->constrained('pos_order_lines')
                ->nullOnDelete();

            $table->foreignId('account_move_line_id')
                ->nullable()
                ->constrained('accounts_account_move_lines')
                ->nullOnDelete();

            $table->foreignId('route_id')
                ->nullable()
                ->constrained('inventories_routes')
                ->nullOnDelete();

            $table->foreignId('warehouse_id')
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

            $table->timestamps();

            $table->index(['order_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_order_lines');
    }
};
