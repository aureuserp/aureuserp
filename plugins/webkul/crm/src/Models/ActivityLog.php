<?php

namespace Webkul\Crm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Sale\Models\Team;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Traits\BelongsToCompany;

class ActivityLog extends Model
{
    use BelongsToCompany;

    protected $table = 'crm_activity_logs';

    protected $fillable = [
        'lead_id',
        'lead_type',
        'activity_type_id',
        'assigned_to',
        'done_by',
        'user_id',
        'team_id',
        'stage_id',
        'company_id',
        'summary',
        'note',
        'date_deadline',
        'activity_created_at',
        'done_at',
    ];

    protected $casts = [
        'date_deadline'       => 'date',
        'activity_created_at' => 'datetime',
        'done_at'             => 'datetime',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id')->withTrashed();
    }

    public function activityType(): BelongsTo
    {
        return $this->belongsTo(ActivityType::class, 'activity_type_id')->withTrashed();
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function doneBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'done_by');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id')->withTrashed();
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(Stage::class, 'stage_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
