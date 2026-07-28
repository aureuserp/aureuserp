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
        Schema::create('maintenance_equipments', function (Blueprint $table) {
            $table->id();
            $table->string('partner_ref')->nullable();
            $table->string('location')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_no')->nullable();
            $table->date('effective_date');
            $table->date('warranty_date')->nullable();
            $table->date('assigned_at')->nullable();
            $table->date('scraped_at')->nullable();
            $table->string('name');
            $table->text('note')->nullable();
            $table->double('cost')->nullable();
            $table->integer('maintenance_count')->nullable();
            $table->integer('maintenance_open_count')->nullable();
            $table->integer('expected_mtbf')->nullable();

            $table->foreignId('category_id')
                ->nullable()
                ->constrained('maintenance_equipment_categories')
                ->nullOnDelete();

            $table->foreignId('partner_id')
                ->nullable()
                ->constrained('partners_partners')
                ->nullOnDelete();

            $table->foreignId('owner_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('maintenance_team_id')
                ->nullable()
                ->constrained('maintenance_teams')
                ->nullOnDelete();

            $table->foreignId('technician_user_id')
                ->nullable()
                ->constrained('users')
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
        Schema::dropIfExists('maintenance_equipments');
    }
};
