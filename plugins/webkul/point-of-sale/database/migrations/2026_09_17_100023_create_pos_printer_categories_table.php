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
        Schema::create('pos_printer_categories', function (Blueprint $table) {
            $table->foreignId('printer_id')
                ->constrained('pos_printers')
                ->cascadeOnDelete();

            $table->foreignId('category_id')
                ->constrained('pos_categories')
                ->cascadeOnDelete();

            $table->unique(['printer_id', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_printer_categories');
    }
};
