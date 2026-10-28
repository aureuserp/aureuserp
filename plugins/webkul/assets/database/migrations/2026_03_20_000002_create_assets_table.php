<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->constrained('companies')
                ->restrictOnDelete();

            $table->foreignId('product_id')
                ->constrained('asset_products')
                ->restrictOnDelete();

            $table->foreignId('warehouse_id')
                ->nullable()
                ->constrained('asset_warehouses')
                ->restrictOnDelete();

            $table->string('asset_tag');
            $table->string('serial_no')->nullable();
            $table->string('status')->default('available')->index();
            $table->string('condition')->default('new')->index();

            $table->foreignId('current_employee_id')
                ->nullable()
                ->constrained('employees_employees')
                ->nullOnDelete();

            $table->foreignId('current_site_id')
                ->nullable()
                ->constrained('asset_sites')
                ->restrictOnDelete();

            $table->timestamp('issued_at')->nullable();
            $table->date('expected_return_at')->nullable();
            $table->text('custody_note')->nullable();

            $table->date('purchase_date')->nullable();
            $table->date('warranty_until')->nullable();
            $table->decimal('cost', 15, 4)->nullable();
            $table->text('note')->nullable();

            $table->foreignId('creator_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'asset_tag']);
            $table->unique(['company_id', 'serial_no']);
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'condition']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
