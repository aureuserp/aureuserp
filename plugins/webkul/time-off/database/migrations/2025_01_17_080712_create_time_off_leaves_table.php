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
        Schema::create('time_off_leaves', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('manager_id')->nullable()->constrained('employees_employees')->nullOnDelete();
            $table->foreignId('holiday_status_id')->nullable()->constrained('time_off_leave_types')->restrictOnDelete();
            $table->foreignId('employee_id')->constrained('employees_employees')->restrictOnDelete();
            $table->foreignId('employee_company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('employees_departments')->nullOnDelete();
            $table->foreignId('calendar_id')->nullable()->constrained('employees_calendars')->nullOnDelete();
            $table->integer('meeting_id')->nullable();
            $table->foreignId('first_approver_id')->nullable()->constrained('employees_employees')->nullOnDelete();
            $table->foreignId('second_approver_id')->nullable()->constrained('employees_employees')->nullOnDelete();
            $table->foreignId('creator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('private_name')->nullable();
            $table->string('state')->nullable();
            $table->string('duration_display')->nullable();
            $table->string('request_date_from_period')->nullable();
            $table->timestamp('request_date_from')->nullable();
            $table->timestamp('request_date_to')->nullable();
            $table->text('notes')->nullable();
            $table->string('attachment')->nullable();
            $table->boolean('request_unit_half')->nullable();
            $table->boolean('request_unit_hours')->nullable();
            $table->timestamp('date_from')->nullable();
            $table->timestamp('date_to')->nullable();
            $table->decimal('number_of_days', 15, 4)->nullable()->default(0);
            $table->decimal('number_of_hours', 15, 4)->nullable()->default(0);
            $table->decimal('request_hour_from', 15, 4)->nullable()->default(0);
            $table->decimal('request_hour_to', 15, 4)->nullable()->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_off_leaves');
    }
};
