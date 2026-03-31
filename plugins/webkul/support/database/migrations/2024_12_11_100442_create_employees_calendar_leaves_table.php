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
        Schema::create('employees_calendar_leaves', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('time_type');
            $table->string('date_from');
            $table->string('date_to');

            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('calendar_id')->nullable();
            $table->unsignedBigInteger('creator_id')->nullable();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
            $table->foreign('calendar_id')->references('id')->on('employees_calendars')->onDelete('set null');
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees_calendar_leaves');
    }
};
