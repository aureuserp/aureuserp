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
        Schema::create('manufacturing_order_label_types', function (Blueprint $table) {
            $table->foreignId('manufacturing_order_id')
                ->constrained('manufacturing_orders')
                ->cascadeOnDelete();

            $table->string('label_type');

            $table->unique(['manufacturing_order_id', 'label_type'], 'mfg_order_label_type_uq');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacturing_order_label_types');
    }
};
