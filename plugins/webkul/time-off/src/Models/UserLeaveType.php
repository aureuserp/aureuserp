<?php

namespace Webkul\TimeOff\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Security\Models\User;

class UserLeaveType extends Model
{
    protected $table = 'time_off_user_leave_types';

    protected $timestamps = false;

    protected $fillable = [
        'user_id',
        'leave_type_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }
}
