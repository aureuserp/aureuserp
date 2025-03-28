<?php

namespace Webkul\TimeOff\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('time_off_leave_types')->delete();

        $company = Company::first();

        $user = User::first();

        $timeOffLeaveTypes = [
            [
                'sort'                                => 1,
                'color'                               => fake()->hexColor(),
                'company_id'                          => $company?->id,
                'max_allowed_negative'                => 20,
                'creator_id'                          => $user?->id,
                'leave_validation_type'               => 'both',
                'requires_allocation'                 => 'yes',
                'employee_requests'                   => 'no',
                'allocation_validation_type'          => 'hr',
                'time_type'                           => 'leave',
                'request_unit'                        => 'day',
                'name'                                => 'Training Time Off',
                'create_calendar_meeting'             => true,
                'is_active'                           => true,
                'show_on_dashboard'                   => true,
                'unpaid'                              => false,
                'include_public_holidays_in_duration' => false,
                'support_document'                    => false,
                'allows_negative'                     => null,
            ],
            [
                'sort'                                => 1,
                'color'                               => fake()->hexColor(),
                'company_id'                          => $company?->id,
                'max_allowed_negative'                => null,
                'creator_id'                          => $user?->id,
                'leave_validation_type'               => 'both',
                'requires_allocation'                 => 'yes',
                'employee_requests'                   => 'no',
                'allocation_validation_type'          => 'hr',
                'time_type'                           => 'leave',
                'request_unit'                        => 'day',
                'name'                                => 'Paid Time Off',
                'create_calendar_meeting'             => true,
                'is_active'                           => true,
                'show_on_dashboard'                   => true,
                'unpaid'                              => false,
                'include_public_holidays_in_duration' => false,
                'support_document'                    => false,
                'allows_negative'                     => null,
            ],
            [
                'sort'                                => 3,
                'color'                               => fake()->hexColor(),
                'company_id'                          => $company?->id,
                'max_allowed_negative'                => null,
                'creator_id'                          => $user?->id,
                'leave_validation_type'               => 'manager',
                'requires_allocation'                 => 'yes',
                'employee_requests'                   => 'no',
                'allocation_validation_type'          => 'hr',
                'time_type'                           => 'leave',
                'request_unit'                        => 'day',
                'name'                                => 'Parental Leaves',
                'create_calendar_meeting'             => true,
                'is_active'                           => true,
                'show_on_dashboard'                   => true,
                'unpaid'                              => false,
                'include_public_holidays_in_duration' => false,
                'support_document'                    => false,
                'allows_negative'                     => null,
            ],
            [
                'sort'                                => 4,
                'color'                               => fake()->hexColor(),
                'company_id'                          => $company?->id,
                'max_allowed_negative'                => null,
                'creator_id'                          => $user?->id,
                'leave_validation_type'               => 'manager',
                'requires_allocation'                 => 'yes',
                'employee_requests'                   => 'yes',
                'allocation_validation_type'          => 'hr',
                'time_type'                           => 'leave',
                'request_unit'                        => 'day',
                'name'                                => 'Compensatory Days test',
                'create_calendar_meeting'             => true,
                'is_active'                           => true,
                'show_on_dashboard'                   => true,
                'unpaid'                              => false,
                'include_public_holidays_in_duration' => true,
                'support_document'                    => true,
                'allows_negative'                     => true,
            ],
            [
                'sort'                                => 5,
                'color'                               => fake()->hexColor(),
                'company_id'                          => $company?->id,
                'max_allowed_negative'                => null,
                'creator_id'                          => $user?->id,
                'leave_validation_type'               => 'both',
                'requires_allocation'                 => 'no',
                'employee_requests'                   => 'no',
                'allocation_validation_type'          => 'hr',
                'time_type'                           => 'leave',
                'request_unit'                        => 'day',
                'name'                                => 'Sick Time Off',
                'create_calendar_meeting'             => true,
                'is_active'                           => true,
                'show_on_dashboard'                   => true,
                'unpaid'                              => false,
                'include_public_holidays_in_duration' => true,
                'support_document'                    => true,
                'allows_negative'                     => null,
            ],
            [
                'sort'                                => 6,
                'color'                               => fake()->hexColor(),
                'company_id'                          => $company?->id,
                'max_allowed_negative'                => null,
                'creator_id'                          => $user?->id,
                'leave_validation_type'               => 'both',
                'requires_allocation'                 => 'yes',
                'employee_requests'                   => 'no',
                'allocation_validation_type'          => 'hr',
                'time_type'                           => 'leave',
                'request_unit'                        => 'hour',
                'name'                                => 'Unpaid',
                'create_calendar_meeting'             => true,
                'is_active'                           => true,
                'show_on_dashboard'                   => true,
                'unpaid'                              => true,
                'include_public_holidays_in_duration' => false,
                'support_document'                    => null,
                'allows_negative'                     => true,
            ],
        ];

        DB::table('time_off_leave_types')->insert(collect($timeOffLeaveTypes)->map(function ($leaveType) {
            return array_merge($leaveType, [
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        })->toArray());
    }
}
