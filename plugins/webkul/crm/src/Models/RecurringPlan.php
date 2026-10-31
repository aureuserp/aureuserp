<?php

namespace Webkul\Crm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Webkul\Security\Models\User;

class RecurringPlan extends Model
{
    use SoftDeletes;

    protected $table = 'crm_recurring_plans';

    protected $fillable = [
        'name',
        'number_of_months',
        'sort',
        'creator_id',
    ];

    protected $casts = [
        'number_of_months' => 'integer',
        'sort'             => 'integer',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'recurring_plan_id');
    }

    protected static function booted(): void
    {
        static::creating(function (self $plan): void {
            $plan->creator_id ??= Auth::id();
        });
    }
}
