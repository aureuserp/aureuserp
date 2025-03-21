<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Webkul\TimeOff\Enums\AccruedGainTime;
use Webkul\TimeOff\Enums\CarryoverDate;
use Webkul\TimeOff\Enums\CarryoverMonth;
use Webkul\TimeOff\Enums\TransitionMode;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('time_off_leave_accrual_plans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('time_off_type_id')->nullable()->constrained('time_off_leave_types')->onDelete('cascade');
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('cascade');
            $table->integer('carryover_day')->nullable();
            $table->foreignId('creator_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('name');
            $table->enum('transition_mode', collect(TransitionMode::options())->keys()->toArray())->default(TransitionMode::IMMEDIATELY->value)->comment('Transition Mode');
            $table->enum('accrued_gain_time', collect(AccruedGainTime::options())->keys()->toArray())->default(AccruedGainTime::END->value)->comment('Accrued Gain Time');
            $table->enum('carryover_date', collect(CarryoverDate::options())->keys()->toArray())->default(CarryoverDate::YEAR_START->value)->comment('Carryover Date');
            $table->enum('carryover_month', collect(CarryoverMonth::options())->keys()->toArray())->default(CarryoverMonth::JAN->value)->comment('Carryover Month');
            $table->string('added_value_type')->nullable();
            $table->boolean('is_active')->nullable();
            $table->boolean('is_based_on_worked_time')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_off_leave_accrual_plans');
    }
};
