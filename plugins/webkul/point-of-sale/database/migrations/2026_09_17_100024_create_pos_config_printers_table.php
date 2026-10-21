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
        Schema::create('pos_config_printers', function (Blueprint $table) {
            $table->foreignId('config_id')
                ->constrained('pos_configs')
                ->cascadeOnDelete();

            $table->foreignId('printer_id')
                ->constrained('pos_printers')
                ->cascadeOnDelete();

            $table->unique(['config_id', 'printer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_config_printers');
    }
};
