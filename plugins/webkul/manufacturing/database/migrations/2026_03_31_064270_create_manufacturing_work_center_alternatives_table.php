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
        Schema::create('manufacturing_work_center_alternatives', function (Blueprint $table) {
            $table->foreignId('work_center_id')
                ->constrained(table: 'manufacturing_work_centers', indexName: 'mfg_wc_alt_wc_fk')
                ->cascadeOnDelete();

            $table->foreignId('alternative_work_center_id')
                ->constrained(table: 'manufacturing_work_centers', indexName: 'mfg_wc_alt_alt_wc_fk')
                ->cascadeOnDelete();

            $table->unique(['work_center_id', 'alternative_work_center_id'], 'mfg_wc_alt_uq');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacturing_work_center_alternatives');
    }
};
