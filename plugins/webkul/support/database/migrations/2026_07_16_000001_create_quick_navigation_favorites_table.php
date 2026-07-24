<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quick_navigation_favorites', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->string('url', 1024);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();

            $table->index(['user_id', 'sort']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quick_navigation_favorites');
    }
};
