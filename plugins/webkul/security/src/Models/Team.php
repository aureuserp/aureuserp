<?php

namespace Webkul\Security\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Security\Traits\HasOwnershipScope;

class Team extends Model
{
    use HasCustomFields, HasOwnershipScope;

    protected $fillable = [
        'name',
        'creator_id',
    ];

    protected static function ownershipScopeIsGlobal(): bool
    {
        return false;
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_team', 'team_id', 'user_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($team) {
            $team->creator_id ??= Auth::id();
        });
    }
}
