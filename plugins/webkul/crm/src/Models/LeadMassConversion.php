<?php

namespace Webkul\Crm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Webkul\Sale\Models\Team;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class LeadMassConversion extends Model
{
    protected $table = 'crm_lead_mass_conversions';

    protected $fillable = [
        'action',
        'customer',
        'deduplicate',
        'force_assignment',
        'team_id',
        'converted_count',
        'merged_count',
        'company_id',
        'creator_id',
    ];

    protected $casts = [
        'deduplicate'      => 'boolean',
        'force_assignment' => 'boolean',
        'converted_count'  => 'integer',
        'merged_count'     => 'integer',
    ];

    public function leads(): HasMany
    {
        return $this->hasMany(LeadMassConversionLead::class, 'mass_conversion_id');
    }

    public function salespeople(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'crm_lead_mass_conversion_salespeople', 'mass_conversion_id', 'user_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    protected static function booted(): void
    {
        static::creating(function (self $conversion): void {
            $conversion->creator_id ??= Auth::id();
        });
    }
}
