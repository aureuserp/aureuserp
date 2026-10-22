<?php

namespace Webkul\Crm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Crm\Services\AssignmentRules;
use Webkul\Sale\Models\Team;

class CrmTeam extends Model
{
    protected $table = 'crm_teams';

    protected $fillable = [
        'team_id',
        'use_leads',
        'use_opportunities',
        'assignment_optout',
        'assignment_domain',
    ];

    protected $casts = [
        'use_leads'         => 'boolean',
        'use_opportunities' => 'boolean',
        'assignment_optout' => 'boolean',
        'assignment_domain' => 'array',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id')->withTrashed();
    }

    protected static function booted(): void
    {
        static::saving(function (self $model): void {
            $model->assignment_domain = AssignmentRules::clean($model->assignment_domain);
        });
    }
}
