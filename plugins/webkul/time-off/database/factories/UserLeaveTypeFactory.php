<?php

namespace Webkul\TimeOff\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Security\Models\User;
use Webkul\TimeOff\Models\LeaveType;
use Webkul\TimeOff\Models\UserLeaveType;

/**
 * @extends Factory<UserLeaveType>
 */
class UserLeaveTypeFactory extends Factory
{
    protected $model = UserLeaveType::class;

    public function definition(): array
    {
        return [
            'user_id'        => User::factory(),
            'leave_type_id'  => LeaveType::factory(),
        ];
    }
}
