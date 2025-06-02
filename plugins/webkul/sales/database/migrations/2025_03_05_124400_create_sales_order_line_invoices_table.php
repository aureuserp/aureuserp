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
        Schema::create('sales_order_line_invoices', function (Blueprint $table) {
            $table->foreignId('order_line_id')
                ->constrained('sales_order_lines')
                ->cascadeOnDelete();

            $table->foreignId('invoice_line_id')
                ->constrained('accounts_account_move_lines')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_order_line_invoices');
    }
};
