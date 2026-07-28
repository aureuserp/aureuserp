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
        Schema::create('manufacturing_bills_of_materials', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable()->index();
            $table->string('type')->default('normal');
            $table->string('ready_to_produce');
            $table->string('consumption');
            $table->decimal('quantity', 15, 4)->default(1);
            $table->boolean('allow_operation_dependencies')->default(false);

            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products_products')
                ->nullOnDelete();

            $table->foreignId('uom_id')
                ->constrained('unit_of_measures')
                ->restrictOnDelete();

            $table->foreignId('operation_type_id')
                ->nullable()
                ->constrained('inventories_operation_types')
                ->nullOnDelete();

            $table->foreignId('company_id')
                ->nullable()
                ->constrained('companies')
                ->nullOnDelete();

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
        Schema::dropIfExists('manufacturing_bills_of_materials');
    }
};
