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
        Schema::create('pos_order_line_taxes', function (Blueprint $table) {
            $table->foreignId('order_line_id')
                ->constrained('pos_order_lines')
                ->cascadeOnDelete();

            $table->foreignId('tax_id')
                ->constrained('accounts_taxes')
                ->cascadeOnDelete();

            $table->unique(['order_line_id', 'tax_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_order_line_taxes');
    }
};
