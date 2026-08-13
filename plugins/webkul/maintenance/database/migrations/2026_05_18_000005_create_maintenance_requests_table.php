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
        Schema::create('maintenance_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('priority')->nullable();
            $table->string('maintenance_type')->nullable();
            $table->boolean('recurring_maintenance')->nullable();
            $table->integer('repeat_interval')->nullable();
            $table->string('repeat_unit')->nullable();
            $table->string('repeat_type')->nullable();
            $table->date('repeat_until')->nullable();
            $table->double('duration')->nullable();
            $table->date('requested_at')->nullable();
            $table->date('closed_at')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->text('description')->nullable();
            $table->string('instruction_type')->nullable();
            $table->string('instruction_pdf')->nullable();
            $table->string('instruction_google_slide')->nullable();
            $table->text('instruction_text')->nullable();

            $table->foreignId('equipment_id')
                ->nullable()
                ->constrained('maintenance_equipments')
                ->restrictOnDelete();

            $table->foreignId('stage_id')
                ->nullable()
                ->constrained('maintenance_stages')
                ->restrictOnDelete();

            $table->foreignId('category_id')
                ->nullable()
                ->constrained('maintenance_equipment_categories')
                ->nullOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('maintenance_team_id')
                ->constrained('maintenance_teams')
                ->restrictOnDelete();

            $table->foreignId('company_id')
                ->comment('Company')
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
        Schema::dropIfExists('maintenance_requests');
    }
};
