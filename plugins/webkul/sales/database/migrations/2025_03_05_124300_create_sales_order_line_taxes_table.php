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
        Schema::create('sales_order_line_taxes', function (Blueprint $table) {
            $table->foreignId('order_line_id')
                ->constrained('sales_order_lines')
                ->cascadeOnDelete();

            $table->foreignId('tax_id')
                ->constrained('accounts_taxes')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_order_line_taxes');
    }
};
