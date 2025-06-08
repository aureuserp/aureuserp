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
        Schema::create('sales_order_invoices', function (Blueprint $table) {
            $table->foreignId('order_id')
                ->constrained('sales_orders')
                ->cascadeOnDelete();

            $table->foreignId('move_id')
                ->constrained('accounts_account_moves')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_order_invoices');
    }
};
