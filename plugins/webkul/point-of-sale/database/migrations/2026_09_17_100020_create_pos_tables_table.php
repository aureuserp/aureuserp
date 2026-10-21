<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Webkul\PointOfSale\Enums\TableShape;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pos_tables', function (Blueprint $table) {
            $table->id();
            $table->string('table_number');
            $table->string('shape')->default(TableShape::SQUARE);
            $table->decimal('position_h', 15, 4)->default(0);
            $table->decimal('position_v', 15, 4)->default(0);
            $table->decimal('width', 15, 4)->default(50);
            $table->decimal('height', 15, 4)->default(50);
            $table->unsignedInteger('seats')->default(2);
            $table->string('color')->nullable();

            $table->foreignId('floor_id')
                ->constrained('pos_floors')
                ->cascadeOnDelete();

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('pos_tables')
                ->nullOnDelete();

            $table->foreignId('creator_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['floor_id', 'table_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_tables');
    }
};
