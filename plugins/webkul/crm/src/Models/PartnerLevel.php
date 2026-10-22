<?php

namespace Webkul\Crm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Webkul\Security\Models\User;

class PartnerLevel extends Model
{
    use SoftDeletes;

    protected $table = 'crm_partner_levels';

    protected $fillable = [
        'name',
        'sort',
        'partner_weight',
        'creator_id',
    ];

    protected $casts = [
        'sort'           => 'integer',
        'partner_weight' => 'integer',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function partners(): HasMany
    {
        return $this->hasMany(CrmPartner::class, 'level_id');
    }

    protected static function booted(): void
    {
        static::creating(function (self $level): void {
            $level->creator_id ??= Auth::id();
        });
    }
}
