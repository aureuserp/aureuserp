<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Webkul\PointOfSale\Enums\PaymentMethodType;
use Webkul\PointOfSale\Enums\PaymentTerminalType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pos_payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default(PaymentMethodType::CASH)->index();
            $table->string('terminal_type')->default(PaymentTerminalType::NONE);
            $table->integer('sort')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_cash_count')->default(0);
            $table->boolean('is_split_transaction')->default(0);

            $table->foreignId('journal_id')
                ->nullable()
                ->constrained('accounts_journals')
                ->restrictOnDelete();

            $table->foreignId('receivable_account_id')
                ->nullable()
                ->constrained('accounts_accounts')
                ->restrictOnDelete();

            $table->foreignId('outstanding_account_id')
                ->nullable()
                ->constrained('accounts_accounts')
                ->restrictOnDelete();

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
        Schema::dropIfExists('pos_payment_methods');
    }
};
