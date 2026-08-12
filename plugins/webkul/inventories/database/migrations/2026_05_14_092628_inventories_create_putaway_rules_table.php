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
        Schema::create('inventories_putaway_rules', function (Blueprint $table) {
            $table->id();
            $table->string('sub_location')->nullable();
            $table->integer('sort')->nullable();

            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products_products')
                ->cascadeOnDelete();

            $table->foreignId('category_id')
                ->nullable()
                ->constrained('products_categories')
                ->cascadeOnDelete();

            $table->foreignId('storage_category_id')
                ->nullable()
                ->constrained('inventories_storage_categories')
                ->cascadeOnDelete();

            $table->foreignId('in_location_id')
                ->constrained('inventories_locations')
                ->cascadeOnDelete();

            $table->foreignId('out_location_id')
                ->constrained('inventories_locations')
                ->cascadeOnDelete();

            $table->foreignId('company_id')
                ->constrained('companies')
                ->restrictOnDelete();

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
        Schema::dropIfExists('inventories_putaway_rules');
    }
};
