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
        Schema::create('plugins', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('author')->nullable();
            $table->text('summary')->nullable();
            $table->text('description')->nullable();
            $table->string('latest_version')->nullable();
            $table->string('license')->nullable();
            $table->boolean('is_active')->default(0);
            $table->boolean('is_installed')->default(0);
            $table->integer('sort')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plugins');
    }
};
