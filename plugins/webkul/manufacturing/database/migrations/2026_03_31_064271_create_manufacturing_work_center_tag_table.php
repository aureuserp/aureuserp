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
        Schema::create('manufacturing_work_center_tag', function (Blueprint $table) {
            $table->foreignId('work_center_id')
                ->constrained('manufacturing_work_centers')
                ->cascadeOnDelete();

            $table->foreignId('tag_id')
                ->constrained('manufacturing_work_center_tags')
                ->cascadeOnDelete();

            $table->unique(['work_center_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacturing_work_center_tag');
    }
};
