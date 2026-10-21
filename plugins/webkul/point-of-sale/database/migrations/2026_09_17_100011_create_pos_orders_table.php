<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Webkul\PointOfSale\Enums\OrderState;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pos_orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name')->nullable()->index();
            $table->string('reference')->nullable()->index();
            $table->string('tracking_number')->nullable();
            $table->string('receipt_code')->nullable();
            $table->string('access_token')->nullable();
            $table->string('origin')->nullable();
            $table->string('state')->default(OrderState::DRAFT)->index();
            $table->unsignedInteger('sequence_number')->default(0);
            $table->timestamp('ordered_at')->nullable()->index();
            $table->timestamp('confirmed_at')->nullable();
            $table->date('shipped_at')->nullable();
            $table->decimal('currency_rate', 15, 4)->default(1);
            $table->decimal('amount_untaxed', 15, 4)->default(0);
            $table->decimal('amount_tax', 15, 4)->default(0);
            $table->decimal('amount_total', 15, 4)->default(0);
            $table->decimal('amount_paid', 15, 4)->default(0);
            $table->decimal('amount_return', 15, 4)->default(0);
            $table->decimal('amount_difference', 15, 4)->default(0);
            $table->decimal('total_cost', 15, 4)->default(0);
            $table->decimal('margin', 15, 4)->default(0);
            $table->decimal('margin_percent', 15, 4)->default(0);
            $table->decimal('tip_amount', 15, 4)->default(0);
            $table->decimal('amount_rounding', 15, 4)->default(0);
            $table->unsignedInteger('print_count')->default(0);
            $table->unsignedInteger('customer_count')->default(0);
            $table->text('note')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile')->nullable();
            $table->boolean('is_to_invoice')->default(0);
            $table->boolean('is_invoiced')->default(0);
            $table->boolean('is_tipped')->default(0);
            $table->boolean('is_takeaway')->default(0);
            $table->boolean('is_cost_computed')->default(0);
            $table->boolean('is_edited')->default(0);
            $table->boolean('has_failed_operation')->default(0);

            $table->foreignId('session_id')
                ->constrained('pos_sessions')
                ->restrictOnDelete();

            $table->foreignId('config_id')
                ->constrained('pos_configs')
                ->restrictOnDelete();

            $table->foreignId('partner_id')
                ->nullable()
                ->constrained('partners_partners')
                ->nullOnDelete();

            $table->foreignId('price_list_id')
                ->nullable()
                ->constrained('products_product_price_lists')
                ->nullOnDelete();

            $table->foreignId('fiscal_position_id')
                ->nullable()
                ->constrained('accounts_fiscal_positions')
                ->nullOnDelete();

            $table->foreignId('currency_id')
                ->nullable()
                ->constrained('currencies')
                ->nullOnDelete();

            $table->foreignId('operation_id')
                ->nullable()
                ->constrained('inventories_operations')
                ->nullOnDelete();

            $table->foreignId('procurement_group_id')
                ->nullable()
                ->constrained('inventories_procurement_groups')
                ->nullOnDelete();

            $table->foreignId('refunded_order_id')
                ->nullable()
                ->constrained('pos_orders')
                ->nullOnDelete();

            $table->foreignId('account_move_id')
                ->nullable()
                ->constrained('accounts_account_moves')
                ->nullOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('company_id')
                ->constrained('companies')
                ->restrictOnDelete();

            $table->foreignId('creator_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['session_id', 'state']);
            $table->index(['config_id', 'ordered_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_orders');
    }
};
