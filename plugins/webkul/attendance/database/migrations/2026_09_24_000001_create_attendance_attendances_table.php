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
        Schema::create('attendance_attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id')->comment('Employee');
            $table->date('work_date')->comment('Calendar Day (Employee Time Zone)');
            $table->dateTime('check_in')->comment('First Punch (UTC)');
            $table->dateTime('check_out')->nullable()->comment('Last Punch (UTC)');
            $table->string('source', 64)->default('manual')->comment('Writer Key: manual or <writer>[:<id>]');
            $table->unsignedBigInteger('company_id')->nullable()->comment('Company');
            $table->unsignedBigInteger('creator_id')->nullable()->comment('Created By');

            $table->foreign('employee_id')->references('id')->on('employees_employees')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');

            $table->unique(['employee_id', 'work_date', 'source'], 'attendance_attendances_employee_work_date_source_unique');
            $table->index('work_date', 'attendance_attendances_work_date_index');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_attendances');
    }
};
