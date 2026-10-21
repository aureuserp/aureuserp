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
        Schema::create('pos_payments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->decimal('amount', 15, 4)->default(0);
            $table->decimal('currency_rate', 15, 4)->default(1);
            $table->string('terminal_status')->nullable();
            $table->string('transaction_reference')->nullable();
            $table->string('card_type')->nullable();
            $table->string('card_brand')->nullable();
            $table->string('cardholder_name')->nullable();
            $table->string('ticket')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->boolean('is_change')->default(0);

            $table->foreignId('order_id')
                ->constrained('pos_orders')
                ->cascadeOnDelete();

            $table->foreignId('session_id')
                ->constrained('pos_sessions')
                ->restrictOnDelete();

            $table->foreignId('payment_method_id')
                ->constrained('pos_payment_methods')
                ->restrictOnDelete();

            $table->foreignId('partner_id')
                ->nullable()
                ->constrained('partners_partners')
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

            $table->index(['session_id', 'payment_method_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_payments');
    }
};
