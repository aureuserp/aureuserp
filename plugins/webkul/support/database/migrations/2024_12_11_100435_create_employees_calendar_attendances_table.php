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
        Schema::create('employees_calendar_attendances', function (Blueprint $table) {
            $table->id();

            $table->integer('sort')->nullable()->comment('Sort Order');
            $table->string('name')->comment('Name');
            $table->string('day_of_week')->comment('Day of Week');
            $table->string('day_period')->comment('Day Period');
            $table->string('week_type')->nullable()->comment('Week Type');
            $table->string('display_type')->nullable()->comment('Display Type');
            $table->string('date_from')->nullable()->comment('Date From');
            $table->string('date_to')->nullable()->comment('Date To');
            $table->string('duration_days')->nullable()->comment('Durations Days');
            $table->string('hour_from')->comment('Hour From');
            $table->string('hour_to')->comment('Hour To');

            $table->unsignedBigInteger('calendar_id')->comment('Calendar ID');
            $table->unsignedBigInteger('creator_id')->nullable()->comment('Created By');

            $table->foreign('calendar_id')->references('id')->on('employees_calendars')->onDelete('cascade');
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees_calendar_attendances');
    }
};
