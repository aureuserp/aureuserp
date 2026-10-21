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
        Schema::create('pos_config_payment_methods', function (Blueprint $table) {
            $table->foreignId('config_id')
                ->constrained('pos_configs')
                ->cascadeOnDelete();

            $table->foreignId('payment_method_id')
                ->constrained('pos_payment_methods')
                ->cascadeOnDelete();

            $table->unique(['config_id', 'payment_method_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_config_payment_methods');
    }
};
