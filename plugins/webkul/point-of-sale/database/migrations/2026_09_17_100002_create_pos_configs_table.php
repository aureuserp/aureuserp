<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Webkul\PointOfSale\Enums\PickingPolicy;
use Webkul\PointOfSale\Enums\TaxDisplay;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pos_configs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->index();
            $table->string('access_token')->nullable();
            $table->integer('sort')->nullable();
            $table->string('tax_display')->default(TaxDisplay::SUBTOTAL);
            $table->string('picking_policy')->default(PickingPolicy::DIRECT);
            $table->text('receipt_header')->nullable();
            $table->text('receipt_footer')->nullable();
            $table->unsignedInteger('limited_products_amount')->default(500);
            $table->decimal('amount_authorized_diff', 15, 4)->nullable();
            $table->boolean('is_active')->default(1);
            $table->boolean('is_restaurant')->default(0);
            $table->boolean('is_closing_entry_by_product')->default(0);
            $table->boolean('enable_maximum_difference')->default(0);
            $table->boolean('enable_line_discount')->default(1);
            $table->boolean('enable_global_discount')->default(0);
            $table->boolean('enable_price_control')->default(0);
            $table->boolean('enable_customer_required')->default(0);
            $table->boolean('enable_receipt_print')->default(1);
            $table->boolean('enable_receipt_auto_print')->default(0);
            $table->boolean('enable_price_list')->default(0);
            $table->boolean('enable_fiscal_position')->default(0);
            $table->boolean('enable_tip')->default(0);
            $table->boolean('enable_ship_later')->default(0);
            $table->boolean('enable_cash_rounding')->default(0);
            $table->boolean('enable_only_round_cash_method')->default(0);
            $table->boolean('enable_cogs')->default(0);
            $table->boolean('enable_split_bill')->default(0);
            $table->boolean('enable_print_bill')->default(0);
            $table->boolean('enable_takeaway')->default(0);
            $table->boolean('limit_categories')->default(0);
            $table->boolean('show_product_images')->default(1);
            $table->boolean('show_category_images')->default(1);

            $table->foreignId('warehouse_id')
                ->constrained('inventories_warehouses')
                ->restrictOnDelete();

            $table->foreignId('operation_type_id')
                ->constrained('inventories_operation_types')
                ->restrictOnDelete();

            $table->foreignId('return_operation_type_id')
                ->nullable()
                ->constrained('inventories_operation_types')
                ->nullOnDelete();

            $table->foreignId('ship_later_route_id')
                ->nullable()
                ->constrained('inventories_routes')
                ->nullOnDelete();

            $table->foreignId('journal_id')
                ->nullable()
                ->constrained('accounts_journals')
                ->restrictOnDelete();

            $table->foreignId('invoice_journal_id')
                ->nullable()
                ->constrained('accounts_journals')
                ->restrictOnDelete();

            $table->foreignId('cogs_journal_id')
                ->nullable()
                ->constrained('accounts_journals')
                ->restrictOnDelete();

            $table->foreignId('receivable_account_id')
                ->nullable()
                ->constrained('accounts_accounts')
                ->restrictOnDelete();

            $table->foreignId('stock_output_account_id')
                ->nullable()
                ->constrained('accounts_accounts')
                ->restrictOnDelete();

            $table->foreignId('balancing_account_id')
                ->nullable()
                ->constrained('accounts_accounts')
                ->restrictOnDelete();

            $table->foreignId('cash_movement_account_id')
                ->nullable()
                ->constrained('accounts_accounts')
                ->restrictOnDelete();

            $table->foreignId('cash_rounding_id')
                ->nullable()
                ->constrained('accounts_cash_roundings')
                ->nullOnDelete();

            $table->foreignId('price_list_id')
                ->nullable()
                ->constrained('products_product_price_lists')
                ->nullOnDelete();

            $table->foreignId('fiscal_position_id')
                ->nullable()
                ->constrained('accounts_fiscal_positions')
                ->nullOnDelete();

            $table->foreignId('takeaway_fiscal_position_id')
                ->nullable()
                ->constrained('accounts_fiscal_positions')
                ->nullOnDelete();

            $table->foreignId('discount_product_id')
                ->nullable()
                ->constrained('products_products')
                ->nullOnDelete();

            $table->foreignId('tip_product_id')
                ->nullable()
                ->constrained('products_products')
                ->nullOnDelete();

            $table->foreignId('currency_id')
                ->nullable()
                ->constrained('currencies')
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
        Schema::dropIfExists('pos_configs');
    }
};
