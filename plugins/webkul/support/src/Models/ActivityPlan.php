<?php

namespace Webkul\Support\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Security\Models\User;

class ActivityPlan extends Model
{
    use HasCustomFields, HasFactory, SoftDeletes;

    protected $table = 'activity_plans';

    protected $fillable = [
        'company_id',
        'plugin',
        'creator_id',
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function activityTypes(): HasMany
    {
        return $this->hasMany(ActivityType::class, 'activity_plan_id');
    }

    public function activityPlanTemplates(): HasMany
    {
        return $this->hasMany(ActivityPlanTemplate::class, 'plan_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($activityPlan) {
            $authUser = Auth::user();

            $activityPlan->creator_id ??= $authUser->id;

            $activityPlan->company_id ??= $authUser?->default_company_id;
        });
    }
}
