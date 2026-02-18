<?php

namespace Webkul\Support\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Security\Models\User;

class ActivityPlanTemplate extends Model implements Sortable
{
    use HasFactory, SortableTrait;

    protected $table = 'activity_plan_templates';

    protected $fillable = [
        'sort',
        'plan_id',
        'activity_type_id',
        'responsible_id',
        'creator_id',
        'delay_count',
        'delay_unit',
        'delay_from',
        'summary',
        'responsible_type',
        'note',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function activityPlan(): BelongsTo
    {
        return $this->belongsTo(ActivityPlan::class, 'plan_id');
    }

    public function activityType(): BelongsTo
    {
        return $this->belongsTo(ActivityType::class, 'activity_type_id');
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($activityPlanTemplate) {
            $activityPlanTemplate->creator_id ??= Auth::id();
        });
    }
}
