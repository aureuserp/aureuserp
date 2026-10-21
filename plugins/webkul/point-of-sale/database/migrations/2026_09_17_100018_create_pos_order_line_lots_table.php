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
        Schema::create('pos_order_line_lots', function (Blueprint $table) {
            $table->id();
            $table->string('lot_name');
            $table->decimal('qty', 15, 4)->default(0);

            $table->foreignId('order_line_id')
                ->constrained('pos_order_lines')
                ->cascadeOnDelete();

            $table->foreignId('lot_id')
                ->nullable()
                ->constrained('inventories_lots')
                ->nullOnDelete();

            $table->foreignId('creator_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['order_line_id', 'lot_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_order_line_lots');
    }
};
