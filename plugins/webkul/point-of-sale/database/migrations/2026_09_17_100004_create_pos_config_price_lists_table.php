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
        Schema::create('pos_config_price_lists', function (Blueprint $table) {
            $table->foreignId('config_id')
                ->constrained('pos_configs')
                ->cascadeOnDelete();

            $table->foreignId('price_list_id')
                ->constrained('products_product_price_lists')
                ->cascadeOnDelete();

            $table->unique(['config_id', 'price_list_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_config_price_lists');
    }
};
