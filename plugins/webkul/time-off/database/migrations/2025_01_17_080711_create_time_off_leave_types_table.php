<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Webkul\TimeOff\Enums\AllocationValidationType;
use Webkul\TimeOff\Enums\EmployeeRequest;
use Webkul\TimeOff\Enums\LeaveValidationType;
use Webkul\TimeOff\Enums\RequestUnit;
use Webkul\TimeOff\Enums\RequiresAllocation;
use Webkul\TimeOff\Enums\TimeType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('time_off_leave_types', function (Blueprint $table) {
            $table->id();

            $table->integer('sort')->nullable()->comment('Sort Order');
            $table->string('color')->nullable()->comment('Color');
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->integer('max_allowed_negative')->nullable()->comment('Max Allowed Negative');
            $table->foreignId('creator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('leave_validation_type', collect(LeaveValidationType::options())->keys()->toArray())->default(LeaveValidationType::HR->value)->nullable()->comment('Leave Validation Type');
            $table->enum('requires_allocation', collect(RequiresAllocation::options())->keys()->toArray())->default(RequiresAllocation::NO)->comment('Requires Allocation');
            $table->enum('employee_requests', collect(EmployeeRequest::options())->keys()->toArray())->default(EmployeeRequest::NO)->comment('Employee Requests');
            $table->enum('allocation_validation_type', collect(AllocationValidationType::options())->keys()->toArray())->default(AllocationValidationType::HR->value)->nullable()->comment('Allocation Validation Type');
            $table->enum('time_type', collect(TimeType::options())->keys()->toArray())->default(TimeType::LEAVE->value)->nullable()->comment('Time Type');
            $table->enum('request_unit', collect(RequestUnit::options())->keys()->toArray())->default(RequestUnit::DAY)->comment('Request Unit');
            $table->string('name')->comment('Name');
            $table->boolean('create_calendar_meeting')->nullable()->comment('Create Calendar Meeting');
            $table->boolean('is_active')->nullable()->comment('Is Active');
            $table->boolean('show_on_dashboard')->nullable()->comment('Show On Dashboard');
            $table->boolean('unpaid')->nullable()->comment('Unpaid');
            $table->boolean('include_public_holidays_in_duration')->nullable()->comment('Include Public Holidays In Duration');
            $table->boolean('support_document')->nullable()->comment('Support Document Required');
            $table->boolean('allows_negative')->nullable()->comment('Allows Negative');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_off_leave_types');
    }
};
