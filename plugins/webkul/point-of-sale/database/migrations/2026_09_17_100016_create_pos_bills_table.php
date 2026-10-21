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
        Schema::create('pos_bills', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('value', 15, 4)->default(0);
            $table->integer('sort')->nullable();
            $table->boolean('is_for_all_configs')->default(1);

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
        Schema::dropIfExists('pos_bills');
    }
};
