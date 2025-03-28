<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Webkul\TimeOff\Enums\AllocationType;
use Webkul\TimeOff\Enums\State;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('time_off_leave_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('holiday_status_id')->constrained('time_off_leave_types')->restrictOnDelete();
            $table->foreignId('employee_id')->constrained('employees_employees')->restrictOnDelete();
            $table->foreignId('employee_company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('manager_id')->nullable()->constrained('employees_employees')->nullOnDelete();
            $table->foreignId('approver_id')->nullable()->constrained('employees_employees')->nullOnDelete();
            $table->foreignId('second_approver_id')->nullable()->constrained('employees_employees')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('employees_departments')->nullOnDelete();
            $table->foreignId('accrual_plan_id')->nullable()->constrained('time_off_leave_accrual_plans')->nullOnDelete();
            $table->foreignId('creator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name')->nullable();
            $table->enum('state', collect(State::options())->keys()->toArray())->default(State::CONFIRM->value)->nullable();
            $table->enum('allocation_type', collect(AllocationType::options())->keys()->toArray())->nullable()->default(AllocationType::REGULAR->value);
            $table->timestamp('date_from');
            $table->timestamp('date_to')->nullable();
            $table->timestamp('last_executed_carryover_date')->nullable();
            $table->timestamp('last_called')->nullable();
            $table->timestamp('actual_last_called')->nullable();
            $table->timestamp('next_call')->nullable();
            $table->timestamp('carried_over_days_expiration_date')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('already_accrued')->nullable();
            $table->decimal('number_of_days', 15, 4)->nullable()->default(0);
            $table->decimal('number_of_hours_display', 15, 4)->nullable()->default(0);
            $table->decimal('yearly_accrued_amount', 15, 4)->nullable()->default(0);
            $table->decimal('expiring_carryover_days', 15, 4)->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_off_leave_allocations');
    }
};
