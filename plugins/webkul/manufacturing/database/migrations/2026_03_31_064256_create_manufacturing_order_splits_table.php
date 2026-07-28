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
        Schema::create('manufacturing_order_splits', function (Blueprint $table) {
            $table->id();
            $table->integer('counter')->nullable();

            $table->foreignId('order_split_batch_id')
                ->nullable()
                ->constrained('manufacturing_order_split_batches')
                ->nullOnDelete();

            $table->foreignId('manufacturing_order_id')
                ->nullable()
                ->constrained('manufacturing_orders')
                ->nullOnDelete();

            $table->foreignId('creator_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacturing_order_splits');
    }
};
