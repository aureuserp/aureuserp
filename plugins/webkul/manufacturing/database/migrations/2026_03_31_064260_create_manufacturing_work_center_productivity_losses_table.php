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
        Schema::create('manufacturing_work_center_productivity_losses', function (Blueprint $table) {
            $table->id();
            $table->integer('sort')->nullable();
            $table->string('loss_type')->nullable();
            $table->string('name');
            $table->boolean('manual')->nullable();

            $table->foreignId('loss_type_id')
                ->nullable()
                ->constrained(table: 'manufacturing_work_center_loss_types', indexName: 'mfg_wc_prod_loss_type_fk')
                ->nullOnDelete();

            $table->foreignId('creator_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacturing_work_center_productivity_losses');
    }
};
