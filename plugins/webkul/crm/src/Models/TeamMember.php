<?php

namespace Webkul\Crm\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Crm\Services\AssignmentRules;
use Webkul\Sale\Models\Team;
use Webkul\Security\Models\User;

class TeamMember extends Model
{
    protected $table = 'crm_team_members';

    protected $fillable = [
        'team_id',
        'user_id',
        'is_active',
        'assignment_optout',
        'assignment_max',
        'assignment_domain',
    ];

    protected $casts = [
        'is_active'         => 'boolean',
        'assignment_optout' => 'boolean',
        'assignment_max'    => 'integer',
        'assignment_domain' => 'array',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id')->withTrashed();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function leadsAssignedLastMonth(): int
    {
        return Lead::query()
            ->withTrashed()
            ->withoutGlobalScopes()
            ->where('team_id', $this->team_id)
            ->where('user_id', $this->user_id)
            ->where('assigned_at', '>=', now()->subDays(30))
            ->count();
    }

    protected static function booted(): void
    {
        static::saving(function (self $model): void {
            $model->assignment_domain = AssignmentRules::clean($model->assignment_domain);
        });
    }
}
