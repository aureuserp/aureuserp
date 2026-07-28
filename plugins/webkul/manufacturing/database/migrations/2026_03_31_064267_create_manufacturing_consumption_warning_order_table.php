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
        Schema::create('manufacturing_consumption_warning_order', function (Blueprint $table) {
            $table->foreignId('consumption_warning_id')
                ->constrained(table: 'manufacturing_consumption_warnings', indexName: 'mfg_cwo_warn_fk')
                ->cascadeOnDelete();

            $table->foreignId('manufacturing_order_id')
                ->constrained(table: 'manufacturing_orders', indexName: 'mfg_cwo_order_fk')
                ->cascadeOnDelete();

            $table->unique(['consumption_warning_id', 'manufacturing_order_id'], 'mfg_cwo_uq');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacturing_consumption_warning_order');
    }
};
