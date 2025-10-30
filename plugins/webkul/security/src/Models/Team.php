<?php

namespace Webkul\Security\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Webkul\Security\Traits\HasPermissionScope;

class Team extends Model
{
    use HasPermissionScope;

    /**
     * Fillable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'creator_id',
    ];

    /**
     * The user that created the team.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * The users that belong to the team.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_team', 'team_id', 'user_id');
    }
}
