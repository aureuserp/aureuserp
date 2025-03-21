<?php

namespace Webkul\Support\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webkul\Security\Models\User;

class ActivityType extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'activity_types';

    protected $fillable = [
        'sort',
        'delay_count',
        'delay_unit',
        'delay_from',
        'icon',
        'decoration_type',
        'chaining_type',
        'plugin',
        'category',
        'name',
        'summary',
        'default_note',
        'is_active',
        'keep_done',
        'creator_id',
        'default_user_id',
        'activity_plan_id',
        'triggered_next_type_id',
    ];

    protected $casts = [
        'is_active'              => 'boolean',
        'keep_done'              => 'boolean',
        'triggered_next_type_id' => 'integer',
    ];

    public function activityPlan(): BelongsTo
    {
        return $this->belongsTo(ActivityPlan::class, 'activity_plan_id');
    }

    public function triggeredNextType(): BelongsTo
    {
        return $this->belongsTo(self::class, 'triggered_next_type_id');
    }

    public function activityTypes(): HasMany
    {
        return $this->hasMany(self::class, 'triggered_next_type_id');
    }

    public function suggestedActivityTypes(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'activity_type_suggestions', 'activity_type_id', 'suggested_activity_type_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function defaultUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'default_user_id');
    }
}
