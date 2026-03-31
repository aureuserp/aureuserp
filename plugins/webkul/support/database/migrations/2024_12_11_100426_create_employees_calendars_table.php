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
        Schema::create('employees_calendars', function (Blueprint $table) {
            $table->id();

            $table->string('name')->comment('Name');
            $table->string('timezone')->comment('Timezone');
            $table->float('hours_per_day')->nullable()->comment('Average Hour per Day');
            $table->boolean('is_active')->default(false)->comment('Status');
            $table->boolean('two_weeks_calendar')->nullable()->default(false)->comment('Calendar in 2 weeks mode');
            $table->boolean('flexible_hours')->nullable()->default(false)->comment('Flexible Hours');
            $table->float('full_time_required_hours')->nullable()->comment('Company Full Time');

            $table->unsignedBigInteger('creator_id')->nullable()->comment('Created By');
            $table->unsignedBigInteger('company_id')->nullable()->comment('Company');

            $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees_calendars');
    }
};
