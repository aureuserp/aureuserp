<?php

namespace Webkul\Crm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Webkul\Crm\Enums\LeadConversionAction;
use Webkul\Crm\Enums\LeadConversionCustomer;
use Webkul\Partner\Models\Partner;
use Webkul\Sale\Models\Team;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class LeadConversion extends Model
{
    protected $table = 'crm_lead_conversions';

    protected $fillable = [
        'lead_id',
        'original_lead_id',
        'lead_name',
        'opportunity_id',
        'action',
        'customer',
        'partner_id',
        'user_id',
        'team_id',
        'company_id',
        'creator_id',
    ];

    protected $casts = [
        'action'   => LeadConversionAction::class,
        'customer' => LeadConversionCustomer::class,
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id')->withTrashed();
    }

    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'opportunity_id')->withTrashed();
    }

    public function duplicates(): HasMany
    {
        return $this->hasMany(LeadConversionDuplicate::class, 'conversion_id');
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
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
