<?php

namespace Webkul\Support\Models;

use Illuminate\Database\Eloquent\Builder;
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

    public function scopeForPlugin(Builder $query, string $plugin): Builder
    {
        return $query->where('plugin', $plugin);
    }

    public function scopeSales(Builder $query): Builder
    {
        return $query->forPlugin('sales');
    }

    public function scopePurchases(Builder $query): Builder
    {
        return $query->forPlugin('purchases');
    }

    public function scopeProjects(Builder $query): Builder
    {
        return $query->forPlugin('projects');
    }

    public function scopeEmployees(Builder $query): Builder
    {
        return $query->forPlugin('employees');
    }

    public function scopeRecruitments(Builder $query): Builder
    {
        return $query->forPlugin('recruitments');
    }

    public function scopePartners(Builder $query): Builder
    {
        return $query->forPlugin('partners');
    }

    public function scopeAccounts(Builder $query): Builder
    {
        return $query->forPlugin('accounts');
    }

    public function scopeInventories(Builder $query): Builder
    {
        return $query->forPlugin('inventories');
    }

    public function scopeInvoices(Builder $query): Builder
    {
        return $query->forPlugin('invoices');
    }

    public function scopeAccounting(Builder $query): Builder
    {
        return $query->forPlugin('accounting');
    }

    public function scopeProducts(Builder $query): Builder
    {
        return $query->forPlugin('products');
    }

    public function scopeTimeOff(Builder $query): Builder
    {
        return $query->forPlugin('time-off');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
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
