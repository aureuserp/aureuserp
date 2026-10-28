<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->constrained('companies')
                ->restrictOnDelete();

            $table->foreignId('asset_id')
                ->constrained('assets')
                ->restrictOnDelete();

            $table->string('type')->index();
            $table->string('source_type')->nullable();
            $table->nullableMorphs('reference');

            $table->foreignId('from_warehouse_id')
                ->nullable()
                ->constrained('asset_warehouses')
                ->restrictOnDelete();

            $table->foreignId('from_employee_id')
                ->nullable()
                ->constrained('employees_employees')
                ->restrictOnDelete();

            $table->foreignId('from_site_id')
                ->nullable()
                ->constrained('asset_sites')
                ->restrictOnDelete();

            $table->foreignId('to_warehouse_id')
                ->nullable()
                ->constrained('asset_warehouses')
                ->restrictOnDelete();

            $table->foreignId('to_employee_id')
                ->nullable()
                ->constrained('employees_employees')
                ->restrictOnDelete();

            $table->foreignId('to_site_id')
                ->nullable()
                ->constrained('asset_sites')
                ->restrictOnDelete();

            $table->string('condition');
            $table->timestamp('occurred_at')->index();
            $table->date('expected_return_at')->nullable();

            $table->foreignId('performed_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('note')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'type']);
            $table->index(['asset_id', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_movements');
    }
};
