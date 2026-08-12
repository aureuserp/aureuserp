<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Webkul\Employee\Enums\MaritalStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employees_employees', function (Blueprint $table) {
            $table->id();

            $table->string('time_zone')->nullable()->comment('Employee Timezone');
            $table->string('work_permit')->nullable()->comment('Work permit document');
            $table->unsignedBigInteger('address_id')->nullable()->comment('Company address ID');
            $table->unsignedBigInteger('leave_manager_id')->nullable()->comment('Leave manager ID');
            $table->unsignedBigInteger('bank_account_id')->nullable();
            $table->unsignedBigInteger('private_state_id')->nullable()->comment('Private State');
            $table->unsignedBigInteger('private_country_id')->nullable()->comment('Private Country');
            $table->unsignedBigInteger('company_id')->nullable()->comment('Company');
            $table->unsignedBigInteger('user_id')->nullable()->comment('Related user');
            $table->unsignedBigInteger('creator_id')->nullable()->comment('Created by');
            $table->unsignedBigInteger('calendar_id')->nullable()->comment('Calendar');
            $table->unsignedBigInteger('department_id')->nullable()->comment('Department');
            $table->unsignedBigInteger('job_id')->nullable()->comment('Job Position');
            $table->unsignedBigInteger('partner_id')->nullable()->comment('Partner');
            $table->unsignedBigInteger('work_location_id')->nullable()->comment('Work Location');
            $table->unsignedBigInteger('parent_id')->nullable()->comment('Parent');
            $table->unsignedBigInteger('coach_id')->nullable()->comment('Coach');
            $table->unsignedBigInteger('country_id')->nullable()->comment('Country');
            $table->unsignedBigInteger('state_id')->nullable()->comment('State');
            $table->unsignedBigInteger('country_of_birth')->nullable()->comment('Country of Birth');
            $table->unsignedBigInteger('departure_reason_id')->nullable()->comment('Departure Reason');
            $table->unsignedBigInteger('attendance_manager_id')->nullable();

            $table->string('name')->nullable()->comment('Employee Name');
            $table->string('job_title')->nullable()->comment('Job Title');
            $table->string('work_phone')->nullable()->comment('Work Phone');
            $table->string('mobile_phone')->nullable()->comment('Mobile Phone');
            $table->string('color')->nullable()->comment('Color');
            $table->integer('children')->nullable()->comment('Children');
            $table->integer('distance_home_work')->default(0)->nullable()->comment('Distance Home Work');
            $table->integer('km_home_work')->default(0)->nullable()->comment('Km Home Work');
            $table->string('distance_home_work_unit')->default('km')->nullable()->comment('Distance Home Work Unit');
            $table->string('work_email')->nullable()->comment('Work Email');
            $table->string('private_phone')->nullable()->comment('Private Phone');
            $table->string('private_email')->nullable()->comment('Private Email');
            $table->string('private_street1')->nullable()->comment('Private Street 1');
            $table->string('private_street2')->nullable()->comment('Private Street 2');
            $table->string('private_city')->nullable()->comment('Private City');
            $table->string('private_zip')->nullable()->comment('Private Postcode');

            $table->string('private_car_plate')->nullable()->comment('Private Car Plate');
            $table->string('lang')->nullable()->comment('Language');
            $table->string('gender')->nullable()->comment('Gender');
            $table->string('birthday')->nullable()->comment('Birthday');
            $table->string('marital')->default(MaritalStatus::Single)->comment('Marital status');
            $table->string('spouse_complete_name')->nullable()->comment('Spouse Complete Name');
            $table->string('spouse_birthdate')->nullable()->comment('Spouse Birthdate');
            $table->string('place_of_birth')->nullable()->comment('Place of Birth');
            $table->string('ssnid')->nullable()->comment('SSN ID');
            $table->string('sinid')->nullable()->comment('SIN ID');
            $table->string('identification_id')->nullable()->comment('Identification ID');
            $table->string('passport_id')->nullable()->comment('Passport ID');
            $table->string('permit_no')->nullable()->comment('Permit No');
            $table->string('visa_no')->nullable()->comment('Visa No');
            $table->string('certificate')->nullable()->comment('Certificate');
            $table->string('study_field')->nullable()->comment('Study Field');
            $table->string('study_school')->nullable()->comment('Study School');
            $table->string('emergency_contact')->nullable()->comment('Emergency Contact');
            $table->string('emergency_phone')->nullable()->comment('Emergency Phone');
            $table->string('employee_type')->default('employee')->comment('Employee Type');
            $table->string('barcode')->nullable()->comment('Barcode');
            $table->string('pin')->nullable()->comment('Pin');
            $table->string('visa_expire')->nullable()->comment('Visa Expire');
            $table->string('work_permit_expiration_date')->nullable()->comment('Work Permit Expiration Date');
            $table->string('departure_date')->nullable()->comment('Departure Date');
            $table->text('departure_description')->nullable()->comment('Departure Description');
            $table->text('additional_note')->nullable()->comment('Additional Note');
            $table->text('notes')->nullable()->comment('Notes');
            $table->boolean('is_active')->default(false)->comment('Status');
            $table->boolean('is_flexible')->nullable()->comment('Is Flexible');
            $table->boolean('is_fully_flexible')->nullable()->comment('Is Fully Flexible');
            $table->boolean('work_permit_scheduled_activity')->nullable()->comment('Work Permit Scheduled Activity');

            $table->foreign('private_state_id')->references('id')->on('states')->onDelete('set null');
            $table->foreign('private_country_id')->references('id')->on('countries')->onDelete('set null');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('calendar_id')->references('id')->on('calendars')->onDelete('set null');
            $table->foreign('department_id')->references('id')->on('employees_departments')->onDelete('set null');
            $table->foreign('job_id')->references('id')->on('employees_job_positions')->onDelete('set null');
            $table->foreign('partner_id')->references('id')->on('partners_partners')->onDelete('set null');
            $table->foreign('work_location_id')->references('id')->on('employees_work_locations')->onDelete('set null');
            $table->foreign('parent_id')->references('id')->on('employees_employees')->onDelete('set null');
            $table->foreign('coach_id')->references('id')->on('employees_employees')->onDelete('set null');
            $table->foreign('state_id')->references('id')->on('states')->onDelete('set null');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('set null');
            $table->foreign('country_of_birth')->references('id')->on('countries')->onDelete('set null');
            $table->foreign('departure_reason_id')->references('id')->on('employees_departure_reasons')->onDelete('restrict');

            $table->foreign('bank_account_id')->references('id')->on('partners_bank_accounts')->onDelete('set null');
            $table->foreign('leave_manager_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('attendance_manager_id')->references('id')->on('users')->onDelete('set null');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees_employees');
    }
};
