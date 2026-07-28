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
        Schema::create('manufacturing_work_order_dependencies', function (Blueprint $table) {
            $table->foreignId('work_order_id')
                ->constrained(table: 'manufacturing_work_orders', indexName: 'mfg_wo_dep_wo_fk')
                ->cascadeOnDelete();

            $table->foreignId('depends_on_work_order_id')
                ->constrained(table: 'manufacturing_work_orders', indexName: 'mfg_wo_dep_depends_fk')
                ->cascadeOnDelete();

            $table->unique(['work_order_id', 'depends_on_work_order_id'], 'mfg_wo_dep_uq');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacturing_work_order_dependencies');
    }
};
