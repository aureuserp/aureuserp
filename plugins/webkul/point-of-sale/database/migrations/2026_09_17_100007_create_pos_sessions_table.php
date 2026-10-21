<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Webkul\PointOfSale\Enums\SessionState;
use Webkul\PointOfSale\Enums\StockUpdateMode;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pos_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable()->index();
            $table->string('state')->default(SessionState::OPENING_CONTROL)->index();
            $table->string('stock_update_mode')->default(StockUpdateMode::REAL_TIME);
            $table->unsignedInteger('sequence_number')->default(0);
            $table->unsignedInteger('login_number')->default(0);
            $table->text('opening_notes')->nullable();
            $table->text('closing_notes')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('stopped_at')->nullable();
            $table->decimal('cash_balance_start', 15, 4)->default(0);
            $table->decimal('cash_balance_end_real', 15, 4)->nullable();
            $table->decimal('cash_balance_end', 15, 4)->nullable();
            $table->decimal('cash_difference', 15, 4)->nullable();
            $table->decimal('cash_transaction_total', 15, 4)->default(0);
            $table->decimal('total_payments_amount', 15, 4)->default(0);
            $table->unsignedInteger('order_count')->default(0);
            $table->unsignedInteger('operation_count')->default(0);
            $table->boolean('is_rescue')->default(0);
            $table->boolean('has_failed_operations')->default(0);

            $table->foreignId('config_id')
                ->constrained('pos_configs')
                ->restrictOnDelete();

            $table->foreignId('rescue_for_session_id')
                ->nullable()
                ->constrained('pos_sessions')
                ->nullOnDelete();

            $table->foreignId('cash_journal_id')
                ->nullable()
                ->constrained('accounts_journals')
                ->restrictOnDelete();

            $table->foreignId('move_id')
                ->nullable()
                ->constrained('accounts_account_moves')
                ->nullOnDelete();

            $table->foreignId('cogs_move_id')
                ->nullable()
                ->constrained('accounts_account_moves')
                ->nullOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('closed_by_id')
                ->nullable()
                ->constrained('users')
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

            $table->timestamps();

            $table->index(['config_id', 'state']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_sessions');
    }
};
