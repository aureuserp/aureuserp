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
        Schema::create('manufacturing_operations', function (Blueprint $table) {
            $table->id();
            $table->integer('sort')->nullable();
            $table->integer('time_mode_batch')->nullable();
            $table->string('name');
            $table->string('worksheet_type')->nullable();
            $table->string('worksheet_google_slide_url')->nullable();
            $table->string('time_mode')->nullable();
            $table->text('note')->nullable();
            $table->decimal('manual_cycle_time', 15, 4)->nullable();

            $table->foreignId('work_center_id')
                ->constrained('manufacturing_work_centers')
                ->restrictOnDelete();

            $table->foreignId('bill_of_material_id')
                ->constrained('manufacturing_bills_of_materials')
                ->cascadeOnDelete();

            $table->foreignId('creator_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacturing_operations');
    }
};
