<?php

namespace Webkul\TimeOff\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class LeaveMandatoryDay extends Model
{
    use HasFactory;

    protected $table = 'time_off_leave_mandatory_days';

    protected $fillable = [
        'company_id',
        'creator_id',
        'color',
        'name',
        'start_date',
        'end_date',
    ];

    protected $dates = [
        'start_date',
        'end_date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($leaveMandatoryDay) {
            $authUser = Auth::user();

            $leaveMandatoryDay->creator_id ??= $authUser->id;

            $leaveMandatoryDay->company_id ??= $authUser?->default_company_id;
        });
    }
}
