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
        Schema::create('manufacturing_work_centers', function (Blueprint $table) {
            $table->id();
            $table->integer('sort');
            $table->string('color')->nullable();
            $table->string('name');
            $table->string('code')->nullable()->index();
            $table->string('working_state')->nullable();
            $table->text('note')->nullable();
            $table->decimal('time_efficiency', 5, 2)->nullable();
            $table->unsignedInteger('default_capacity')->nullable();
            $table->decimal('costs_per_hour', 15, 4)->nullable();
            $table->decimal('setup_time', 15, 4)->nullable();
            $table->decimal('cleanup_time', 15, 4)->nullable();
            $table->decimal('oee_target', 5, 2)->nullable();

            $table->foreignId('company_id')
                ->nullable()
                ->constrained('companies')
                ->nullOnDelete();

            $table->foreignId('calendar_id')
                ->nullable()
                ->constrained('calendars')
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
        Schema::dropIfExists('manufacturing_work_centers');
    }
};
