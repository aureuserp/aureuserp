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
        Schema::create('accounts_account_payment_register_move_lines', function (Blueprint $table) {
            $table->foreignId('payment_register_id')->comment('Account Payment Register Id');
            $table->foreignId('move_line_id')->comment('Account move line');

            $table->foreign('payment_register_id', 'fk_payment_register')
                ->references('id')
                ->on('accounts_payment_registers')
                ->cascadeOnDelete();

            $table->foreign('move_line_id', 'fk_move_line')
                ->references('id')
                ->on('accounts_account_move_lines')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts_account_payment_register_move_lines');
    }
};
